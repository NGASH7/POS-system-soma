<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'user'])
            ->when($request->category, function($q) use ($request) {
                return $q->where('category_id', $request->category);
            })
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->start_date, function($q) use ($request) {
                return $q->whereDate('expense_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($q) use ($request) {
                return $q->whereDate('expense_date', '<=', $request->end_date);
            })
            ->orderBy('expense_date', 'desc')
            ->paginate(20);

        $categories = ExpenseCategory::where('is_active', true)->get();
        $summary = $this->getSummary($request);

        return view('expenses.index', compact('query', 'categories', 'summary'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,mobile_money,bank_transfer',
            'vendor' => 'nullable|string|max:255',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5120',
            'is_recurring' => 'nullable|boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurring_end_date' => 'nullable|date|after:expense_date'
        ]);

        // Generate reference number
        $referenceNo = 'EXP-' . date('Ymd') . '-' . str_pad(Expense::count() + 1, 4, '0', STR_PAD_LEFT);

        $validated['reference_no'] = $referenceNo;
        $validated['user_id'] = Auth::id();
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['status'] = 'pending'; // Default status

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')->store('expenses/receipts', 'public');
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', "Expense created successfully! Reference: {$referenceNo}");
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'user']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,mobile_money,bank_transfer',
            'vendor' => 'nullable|string|max:255',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5120',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($request->hasFile('receipt_image')) {
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            $validated['receipt_image'] = $request->file('receipt_image')->store('expenses/receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }

    public function approve(Expense $expense)
    {
        $expense->update(['status' => 'approved']);
        return redirect()->back()
            ->with('success', 'Expense approved successfully!');
    }

    public function reject(Expense $expense)
    {
        $expense->update(['status' => 'rejected']);
        return redirect()->back()
            ->with('success', 'Expense rejected successfully!');
    }

    // ========== Category Management Methods ==========

    public function categories()
    {
        $categories = ExpenseCategory::withCount('expenses')->paginate(20);
        return view('expenses.categories', compact('categories'));
    }

    /**
     * Get category data for editing (AJAX)
     */
    public function editCategory(ExpenseCategory $category)
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'color' => $category->color,
            'is_active' => $category->is_active
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories',
            'description' => 'nullable|string',
            'color' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;

        ExpenseCategory::create($validated);

        return redirect()->route('expenses.categories')
            ->with('success', 'Category created successfully!');
    }

    public function updateCategory(Request $request, ExpenseCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('expenses.categories')
            ->with('success', 'Category updated successfully!');
    }

    public function deleteCategory(ExpenseCategory $category)
    {
        if ($category->expenses()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with associated expenses.');
        }

        $category->delete();

        return redirect()->route('expenses.categories')
            ->with('success', 'Category deleted successfully!');
    }

    private function getSummary($request)
    {
        $query = Expense::query();

        if ($request->start_date) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

        // Check if status column exists
        $hasStatusColumn = Schema::hasColumn('expenses', 'status');

        return [
            'total' => $query->sum('amount'),
            'pending' => $hasStatusColumn ? (clone $query)->where('status', 'pending')->sum('amount') : 0,
            'approved' => $hasStatusColumn ? (clone $query)->where('status', 'approved')->sum('amount') : 0,
            'rejected' => $hasStatusColumn ? (clone $query)->where('status', 'rejected')->sum('amount') : 0,
            'count' => $query->count()
        ];
    }
}