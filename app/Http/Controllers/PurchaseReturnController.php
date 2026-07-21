<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseReturn::with(['purchase', 'supplier', 'outlet', 'user', 'items.product'])
            ->orderByDesc('return_date')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_no', 'like', "%{$search}%")
                  ->orWhereHas('purchase', function ($pq) use ($search) {
                      $pq->where('reference_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $returns = $query->paginate(15)->withQueryString();

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        $summary = [
            'total_count' => PurchaseReturn::count(),
            'total_amount' => PurchaseReturn::sum('total_amount'),
            'completed_count' => PurchaseReturn::where('status', 'completed')->count(),
        ];

        return view('purchases.returns.index', compact('returns', 'suppliers', 'summary'));
    }

    public function create()
    {
        $purchases = Purchase::with(['supplier', 'outlet', 'items.product'])
            ->orderByDesc('created_at')
            ->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();

        $returnNo = 'PR-' . date('Ymd') . '-' . str_pad(PurchaseReturn::count() + 1, 4, '0', STR_PAD_LEFT);

        return view('purchases.returns.create', compact('purchases', 'suppliers', 'outlets', 'returnNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'return_date' => 'required|date',
            'status' => 'required|in:completed,pending,cancelled',
            'refund_status' => 'required|in:refunded,pending',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255',
        ]);

        $purchase = Purchase::findOrFail($validated['purchase_id']);

        DB::beginTransaction();
        try {
            $returnNo = $request->return_no;
            if (!$returnNo) {
                $returnNo = 'PR-' . date('Ymd') . '-' . str_pad(PurchaseReturn::count() + 1, 4, '0', STR_PAD_LEFT);
            }

            $purchaseReturn = PurchaseReturn::create([
                'return_no' => $returnNo,
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'outlet_id' => $purchase->outlet_id,
                'user_id' => Auth::id(),
                'return_date' => $validated['return_date'],
                'status' => $validated['status'],
                'refund_status' => $validated['refund_status'],
                'total_amount' => $validated['total_amount'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                    'reason' => $item['reason'] ?? 'Returned to supplier',
                ]);

                // If return status is completed, decrement stock
                if ($purchaseReturn->status === 'completed') {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->decrement('stock_quantity', min($product->stock_quantity, $item['quantity']));
                    }
                }
            }

            DB::commit();

            return redirect()->route('purchases.returns')->with('success', 'Purchase Return record created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save purchase return: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $returnRecord = PurchaseReturn::with(['purchase', 'supplier', 'outlet', 'user', 'items.product'])->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'return' => $returnRecord,
            ]);
        }

        return response()->json(['success' => true, 'return' => $returnRecord]);
    }

    public function destroy($id)
    {
        $returnRecord = PurchaseReturn::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($returnRecord->status === 'completed') {
                foreach ($returnRecord->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            $returnRecord->delete();
            DB::commit();

            return redirect()->route('purchases.returns')->with('success', 'Purchase Return deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete purchase return: ' . $e->getMessage());
        }
    }
}
