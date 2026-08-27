<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
    /**
     * Display a listing of the discounts.
     */
    public function index(Request $request)
    {
        $query = Discount::with(['user', 'outlet'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->status;
                $now = now();
                if ($status === 'active') {
                    return $q->where('is_active', true)
                        ->where(function ($sub) use ($now) {
                            $sub->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                        })
                        ->where(function ($sub) {
                            $sub->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
                        });
                } elseif ($status === 'inactive') {
                    return $q->where('is_active', false);
                } elseif ($status === 'expired') {
                    return $q->where(function ($sub) use ($now) {
                        $sub->where(function ($d) use ($now) {
                            $d->whereNotNull('ends_at')->where('ends_at', '<', $now);
                        })->orWhere(function ($u) {
                            $u->whereNotNull('usage_limit')->whereColumn('used_count', '>=', 'usage_limit');
                        });
                    });
                }
            })
            ->orderBy('created_at', 'desc');

        $discounts = $query->paginate(15)->withQueryString();

        // Calculate summary cards metrics
        $allDiscounts = Discount::all();
        $now = now();

        $summary = [
            'total' => $allDiscounts->count(),
            'active' => $allDiscounts->filter(function ($d) use ($now) {
                return $d->is_active && (!$d->ends_at || $d->ends_at >= $now) && (!$d->usage_limit || $d->used_count < $d->usage_limit);
            })->count(),
            'expired' => $allDiscounts->filter(function ($d) use ($now) {
                return ($d->ends_at && $d->ends_at < $now) || ($d->usage_limit && $d->used_count >= $d->usage_limit);
            })->count(),
            'total_used' => $allDiscounts->sum('used_count'),
        ];

        return view('discounts.index', compact('discounts', 'summary'));
    }

    /**
     * Show the form for creating a new discount.
     */
    public function create()
    {
        return view('discounts.create');
    }

    /**
     * Store a newly created discount in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percentage' && $value > 100) {
                        $fail('Percentage discount value cannot exceed 100%.');
                    }
                },
            ],
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['code'] = $validated['code'] ? strtoupper(trim($validated['code'])) : null;
        $validated['is_active'] = $request->has('is_active');
        $validated['user_id'] = Auth::id();

        Discount::create($validated);

        return redirect()->route('discounts.index')->with('success', 'Discount created successfully.');
    }

    /**
     * Show the form for editing the specified discount.
     */
    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    /**
     * Update the specified discount in storage.
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percentage' && $value > 100) {
                        $fail('Percentage discount value cannot exceed 100%.');
                    }
                },
            ],
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['code'] = $validated['code'] ? strtoupper(trim($validated['code'])) : null;
        $validated['is_active'] = $request->has('is_active');

        $discount->update($validated);

        return redirect()->route('discounts.index')->with('success', 'Discount updated successfully.');
    }

    /**
     * Toggle active/inactive status of the specified discount.
     */
    public function toggleStatus(Discount $discount)
    {
        $discount->is_active = !$discount->is_active;
        $discount->save();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $discount->is_active,
                'message' => 'Discount status updated successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Discount status updated successfully.');
    }

    /**
     * Remove the specified discount from storage.
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('discounts.index')->with('success', 'Discount deleted successfully.');
    }
}
