<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        // Item details are fetched on demand by the purchase-details drawer.
        // Loading every item and product here makes the list page increasingly
        // expensive as the purchase history grows.
        $query = Purchase::with(['supplier', 'outlet', 'user'])
            ->orderByDesc('purchase_date')
            ->orderByDesc('id');

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter outlet
        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        // Filter date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }

        $purchases = $query->paginate(15)->withQueryString();

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();

        // Calculate metrics
        $summary = [
            'total_count' => Purchase::count(),
            'total_spent' => Purchase::sum('total_amount'),
            'received_count' => Purchase::where('status', 'received')->count(),
            'total_due' => Purchase::sum('due_amount'),
        ];

        return view('purchases.index', compact('purchases', 'suppliers', 'outlets', 'summary'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $defaultOutletId = Auth::user()->outlet_id ?? Outlet::first()?->id ?? 1;

        // Fetch products for quick dropdown selection / live search
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'price', 'cost', 'stock_quantity']);

        // Generate PO reference number
        $referenceNo = 'PO-' . date('Ymd') . '-' . str_pad(Purchase::count() + 1, 4, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'outlets', 'products', 'referenceNo', 'defaultOutletId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'outlet_id' => 'required|exists:outlets,id',
            'purchase_date' => 'required|date',
            'status' => 'required|in:received,pending,ordered,cancelled',
            'payment_status' => 'required|in:paid,due,partial',
            'payment_method' => 'nullable|string|max:50',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        DB::beginTransaction();

        try {
            // Auto generate reference if empty
            $referenceNo = $request->reference_no;
            if (!$referenceNo) {
                $referenceNo = 'PO-' . date('Ymd') . '-' . str_pad(Purchase::count() + 1, 4, '0', STR_PAD_LEFT);
            }

            // Handle document upload
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('purchases/documents', 'public');
            }

            $paidAmount = $validated['paid_amount'] ?? 0;
            $totalAmount = $validated['total_amount'];
            $dueAmount = max(0, $totalAmount - $paidAmount);

            // Create purchase record
            $purchase = Purchase::create([
                'reference_no' => $referenceNo,
                'supplier_id' => $validated['supplier_id'],
                'outlet_id' => $validated['outlet_id'],
                'user_id' => Auth::id(),
                'purchase_date' => $validated['purchase_date'],
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'subtotal' => $validated['subtotal'],
                'tax' => $validated['tax'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'notes' => $validated['notes'] ?? null,
                'document' => $documentPath,
            ]);

            // Save purchase line items
            foreach ($validated['items'] as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                    'batch_no' => $item['batch_no'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);

                // If purchase is received, increment stock quantity & update product cost
                if ($purchase->status === 'received') {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->increment('stock_quantity', $item['quantity']);
                        if ($item['unit_cost'] > 0) {
                            $product->update(['cost' => $item['unit_cost']]);
                        }
                    }
                }
            }

            // Auto log Expense record if purchase is paid
            if ($paidAmount > 0) {
                $category = \App\Models\ExpenseCategory::firstOrCreate(
                    ['slug' => 'inventory-purchases'],
                    [
                        'name' => 'Inventory Purchases',
                        'description' => 'Automatic expense records for stock and product purchases',
                        'color' => '#2563eb',
                        'is_active' => true,
                    ]
                );

                \App\Models\Expense::create([
                    'reference_no' => 'EXP-' . $purchase->reference_no,
                    'category_id' => $category->id,
                    'user_id' => Auth::id(),
                    'outlet_id' => $purchase->outlet_id,
                    'title' => 'Purchase Order Payment: ' . $purchase->reference_no,
                    'description' => 'Payment for purchase order ' . $purchase->reference_no . ($purchase->supplier ? ' from ' . $purchase->supplier->name : ''),
                    'amount' => $paidAmount,
                    'expense_date' => $purchase->purchase_date,
                    'payment_method' => $purchase->payment_method ?? 'cash',
                    'vendor' => $purchase->supplier?->name ?? 'Supplier',
                    'status' => 'approved',
                ]);
            }

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase created successfully!',
                    'redirect' => route('purchases.index'),
                ]);
            }

            return redirect()->route('purchases.index')->with('success', 'Purchase record added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create purchase: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to create purchase: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'outlet', 'user', 'items.product'])->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'purchase' => $purchase,
            ]);
        }

        return view('purchases.show', compact('purchase'));
    }

    public function destroy($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // If status was received, reverse stock updates
            if ($purchase->status === 'received') {
                foreach ($purchase->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('stock_quantity', min($product->stock_quantity, $item->quantity));
                    }
                }
            }

            $purchase->delete();
            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'Purchase record deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }

    // Quick add supplier AJAX endpoint
    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier added successfully!',
            'supplier' => $supplier,
        ]);
    }

    // Quick add product AJAX endpoint for Purchase page
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $sku = $validated['sku'] ?: ('PRD-' . strtoupper(substr(md5(uniqid()), 0, 6)));

        $product = Product::create([
            'name' => $validated['name'],
            'sku' => $sku,
            'cost' => $validated['cost'],
            'price' => $validated['price'],
            'stock_quantity' => 0,
            'outlet_id' => Auth::user()->outlet_id ?? 1,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created and added to catalog successfully!',
            'product' => $product,
        ]);
    }
}
