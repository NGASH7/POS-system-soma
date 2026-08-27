<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\WarrantyClaim;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'warranties');

        $query = Warranty::with(['product', 'customer', 'user'])
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->product_id, function($q) use ($request) {
                return $q->where('product_id', $request->product_id);
            })
            ->when($request->customer_id, function($q) use ($request) {
                return $q->where('customer_id', $request->customer_id);
            })
            ->when($request->search, function($q) use ($request) {
                return $q->where('warranty_no', 'like', "%{$request->search}%")
                    ->orWhere('serial_number', 'like', "%{$request->search}%");
            })
            ->orderBy('created_at', 'desc');

        $warranties = $query->paginate(20, ['*'], 'warranties_page');

        $claimsQuery = WarrantyClaim::with(['warranty.product', 'warranty.customer', 'user'])
            ->when($request->claim_status, function($q) use ($request) {
                return $q->where('status', $request->claim_status);
            })
            ->when($request->search, function($q) use ($request) {
                return $q->where('claim_no', 'like', "%{$request->search}%")
                    ->orWhere('issue_description', 'like', "%{$request->search}%")
                    ->orWhereHas('warranty', function($w) use ($request) {
                        $w->where('warranty_no', 'like', "%{$request->search}%")
                          ->orWhere('serial_number', 'like', "%{$request->search}%");
                    });
            })
            ->orderBy('created_at', 'desc');

        $claims = $claimsQuery->paginate(20, ['*'], 'claims_page');

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        $summary = [
            'total' => Warranty::count(),
            'active' => Warranty::where('status', 'active')->count(),
            'expired' => Warranty::where('status', 'expired')->count(),
            'claimed' => Warranty::where('status', 'claimed')->count(),
            'expiring_soon' => Warranty::where('status', 'active')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->count(),
            'open_claims' => WarrantyClaim::whereIn('status', ['pending', 'approved'])->count(),
            'total_claims' => WarrantyClaim::count()
        ];

        return view('warranties.index', compact('warranties', 'claims', 'products', 'customers', 'summary', 'activeTab'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $sales = Sale::where('status', 'completed')
            ->where('is_return', false)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Generate warranty number
        $warrantyNo = 'WAR-' . date('Ymd') . '-' . str_pad(Warranty::count() + 1, 4, '0', STR_PAD_LEFT);
        
        return view('warranties.create', compact('products', 'customers', 'sales', 'warrantyNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warranty_no' => 'nullable|string|max:255',
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'purchase_date' => 'required|date',
            'expiry_date' => 'required|date|after:purchase_date',
            'serial_number' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'warranty_type' => 'required|in:manufacturer,store,extended',
            'warranty_duration_months' => 'required|integer|min:1',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $warrantyNo = $validated['warranty_no'] ?? $request->warranty_no;
            if (empty($warrantyNo)) {
                $warrantyNo = 'WAR-' . date('Ymd') . '-' . str_pad(Warranty::count() + 1, 4, '0', STR_PAD_LEFT);
            }

            $warranty = Warranty::create([
                'warranty_no' => $warrantyNo,
                'product_id' => $validated['product_id'],
                'customer_id' => $validated['customer_id'],
                'sale_id' => $validated['sale_id'],
                'user_id' => Auth::id() ?? 1,
                'outlet_id' => Auth::user()->outlet_id ?? 1,
                'purchase_date' => $validated['purchase_date'],
                'expiry_date' => $validated['expiry_date'],
                'serial_number' => $validated['serial_number'],
                'batch_number' => $validated['batch_number'],
                'warranty_type' => $validated['warranty_type'],
                'warranty_duration_months' => $validated['warranty_duration_months'],
                'status' => 'active',
                'terms' => $validated['terms'],
                'notes' => $validated['notes']
            ]);

            DB::commit();

            return redirect()->route('warranties.show', $warranty)
                ->with('success', "Warranty created successfully! Reference: {$warranty->warranty_no}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Warranty creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create warranty: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Warranty $warranty)
    {
        $warranty->load(['product', 'customer', 'user', 'sale', 'claims.user']);
        return view('warranties.show', compact('warranty'));
    }

    public function edit(Warranty $warranty)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $sales = Sale::where('status', 'completed')
            ->where('is_return', false)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('warranties.edit', compact('warranty', 'products', 'customers', 'sales'));
    }

    public function update(Request $request, Warranty $warranty)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'purchase_date' => 'required|date',
            'expiry_date' => 'required|date',
            'serial_number' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'warranty_type' => 'required|in:manufacturer,store,extended',
            'warranty_duration_months' => 'required|integer|min:1',
            'status' => 'required|in:active,expired,claimed,replaced,void',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $warranty->update($validated);

        return redirect()->route('warranties.show', $warranty)
            ->with('success', 'Warranty updated successfully!');
    }

    public function destroy(Warranty $warranty)
    {
        if ($warranty->claims()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete warranty with associated claims.');
        }

        $warranty->delete();

        return redirect()->route('warranties.index')
            ->with('success', 'Warranty deleted successfully!');
    }

    public function createClaim(Warranty $warranty)
    {
        if ($warranty->claims()->count() > 0) {
            return redirect()->route('warranties.show', $warranty)
                ->with('error', 'A claim already exists for this warranty. Only one claim is allowed per warranty.');
        }

        return view('warranties.claims.create', compact('warranty'));
    }

    public function storeClaim(Request $request, Warranty $warranty)
    {
        if ($warranty->claims()->count() > 0) {
            return redirect()->back()
                ->with('error', 'A claim already exists for this warranty. Only one claim is allowed per warranty.');
        }

        $validated = $request->validate([
            'issue_description' => 'required|string',
            'claim_date' => 'required|date'
        ]);

        DB::beginTransaction();

        try {
            $claim = WarrantyClaim::create([
                'claim_no' => 'CLM-' . date('Ymd') . '-' . str_pad(WarrantyClaim::count() + 1, 4, '0', STR_PAD_LEFT),
                'warranty_id' => $warranty->id,
                'user_id' => Auth::id() ?? 1,
                'claim_date' => $validated['claim_date'],
                'issue_description' => $validated['issue_description'],
                'status' => 'pending'
            ]);

            // Update warranty status to claimed
            $warranty->update(['status' => 'claimed']);

            DB::commit();

            return redirect()->route('warranties.show', $warranty)
                ->with('success', "Claim created successfully! Claim #: {$claim->claim_no}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Warranty claim error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create claim: ' . $e->getMessage());
        }
    }

    public function updateClaim(Request $request, WarrantyClaim $claim)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'resolution' => 'required|in:repair,replace,refund,none',
            'resolution_notes' => 'nullable|string',
            'resolved_date' => 'nullable|date'
        ]);

        DB::beginTransaction();

        try {
            $claim->update([
                'status' => $validated['status'],
                'resolution' => $validated['resolution'],
                'resolution_notes' => $validated['resolution_notes'],
                'resolved_date' => $validated['resolved_date'] ?? now()
            ]);

            // Update warranty status if claim is completed
            if ($validated['status'] === 'completed') {
                $claim->warranty->update(['status' => 'replaced']);
            }

            DB::commit();

            return redirect()->route('warranties.show', $claim->warranty)
                ->with('success', 'Claim updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Warranty claim update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update claim: ' . $e->getMessage());
        }
    }

    public function printWarranty(Warranty $warranty)
    {
        $warranty->load(['product', 'customer', 'user']);
        return view('warranties.print', compact('warranty'));
    }

    public function checkWarranty(Request $request)
    {
        $search = $request->get('q');
        
        $warranties = Warranty::with(['product', 'customer'])
            ->where(function($query) use ($search) {
                $query->where('warranty_no', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
            })
            ->where('status', 'active')
            ->limit(10)
            ->get();

        return response()->json($warranties);
    }
}