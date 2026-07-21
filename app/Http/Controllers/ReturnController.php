<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ReturnModel;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = ReturnModel::with(['originalSale', 'customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $summary = [
            'total_returns' => ReturnModel::count(),
            'total_refunded' => ReturnModel::sum('refund_amount'),
            'pending' => ReturnModel::where('status', 'pending')->count(),
            'exchanges' => ReturnModel::where('return_type', 'exchange')->count(),
        ];

        return view('returns.index', compact('returns', 'summary'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->take(50)
            ->get();
        
        return view('returns.create', compact('customers', 'products'));
    }

    public function searchSale(Request $request)
    {
        try {
            $search = $request->get('search');
            
            Log::info('Searching for sale:', ['search' => $search]);
            
            if (empty($search)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a search term'
                ]);
            }

            $sale = Sale::with(['items.product', 'customer', 'user'])
                ->where(function($query) use ($search) {
                    $query->where('invoice_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('phone', 'like', "%{$search}%");
                        });
                })
                ->where('status', 'completed')
                ->where('is_return', false)
                ->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale not found. Please check the invoice number or customer name.'
                ]);
            }

            // Get returnable items (items not already returned)
            $returnedItems = ReturnModel::where('original_sale_id', $sale->id)
                ->get()
                ->pluck('items')
                ->flatten()
                ->toArray();

            $items = $sale->items->map(function($item) use ($returnedItems) {
                $alreadyReturned = in_array($item->id, $returnedItems);
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                    'already_returned' => $alreadyReturned,
                    'available_quantity' => $alreadyReturned ? 0 : $item->quantity
                ];
            });

            return response()->json([
                'success' => true,
                'sale' => $sale,
                'items' => $items,
                'customer' => $sale->customer ? [
                    'id' => $sale->customer->id,
                    'name' => $sale->customer->name,
                    'phone' => $sale->customer->phone
                ] : null
            ]);

        } catch (\Exception $e) {
            Log::error('Search sale error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error searching for sale: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'original_sale_id' => 'required|exists:sales,id',
                'return_type' => 'required|in:return,exchange',
                'reason' => 'required|string|max:255',
                'refund_method' => 'required|in:cash,card,credit,mobile_money',
                'notes' => 'nullable|string',
                'items' => 'required|json',
                'refund_amount' => 'required|numeric',
                'exchange_items' => 'nullable|array',
                'exchange_items.*.product_id' => 'exists:products,id',
                'exchange_items.*.quantity' => 'integer|min:1'
            ]);

            DB::beginTransaction();

            $originalSale = Sale::find($validated['original_sale_id']);
            
            // Decode items
            $items = json_decode($validated['items'], true);
            
            // Use the user-provided refund amount, but still track returned items for restocking
            $refundAmount = $validated['refund_amount'];
            $returnedItems = [];
            foreach ($items as $itemId) {
                $saleItem = SaleItem::find($itemId);
                if ($saleItem) {
                    $returnedItems[] = $itemId;
                }
            }

            // Handle exchange
            $exchangeItems = [];
            if ($validated['return_type'] === 'exchange' && isset($validated['exchange_items'])) {
                foreach ($validated['exchange_items'] as $item) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $exchangeItems[] = [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity' => $item['quantity'],
                            'price' => $product->price,
                            'total' => $product->price * $item['quantity']
                        ];
                    }
                }
            }

            // Create return record
            $return = ReturnModel::create([
                'return_no' => 'RET-' . date('Ymd') . '-' . str_pad(ReturnModel::count() + 1, 4, '0', STR_PAD_LEFT),
                'original_sale_id' => $originalSale->id,
                'user_id' => Auth::id(),
                'customer_id' => $originalSale->customer_id,
                'refund_amount' => $refundAmount,
                'refund_method' => $validated['refund_method'],
                'return_type' => $validated['return_type'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
                'items' => $returnedItems,
                'exchange_items' => $exchangeItems
            ]);

            // Update stock for returned items (ADD back to inventory)
            foreach ($items as $itemId) {
                $saleItem = SaleItem::find($itemId);
                if ($saleItem) {
                    Product::where('id', $saleItem->product_id)
                        ->increment('stock_quantity', $saleItem->quantity);
                }
            }

            // If exchange, deduct stock for exchanged items
            if ($validated['return_type'] === 'exchange') {
                foreach ($validated['exchange_items'] ?? [] as $item) {
                    Product::where('id', $item['product_id'])
                        ->decrement('stock_quantity', $item['quantity']);
                }
            }

            // CRITICAL: Create a NEGATIVE sale record for the return
            $returnSale = Sale::create([
                'invoice_no' => $return->return_no,
                'user_id' => Auth::id(),
                'customer_id' => $originalSale->customer_id,
                'terminal_id' => session()->get('terminal_id', 'TERM-01'),
                'subtotal' => -$refundAmount, // NEGATIVE amount
                'discount' => 0,
                'tax' => 0,
                'total' => -$refundAmount, // NEGATIVE amount
                'paid' => -$refundAmount, // NEGATIVE amount to track cash outflow
                'change_due' => 0,
                'payment_method' => 'return',
                'status' => 'completed',
                'sale_date' => now(),
                'is_return' => true,
                'returned_from_sale_id' => $originalSale->id,
                'return_type' => $validated['return_type'],
                'return_amount' => $refundAmount,
                'return_reason' => $validated['reason'],
                'return_receipt_no' => $return->return_no,
                'notes' => "Return processed: {$return->return_no}"
            ]);

            // Update the original sale to mark it as having returns
            $originalSale->update([
                'is_return' => true,
                'return_amount' => $refundAmount,
                'return_reason' => $validated['reason'],
                'return_type' => $validated['return_type'],
                'return_receipt_no' => $return->return_no
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Return processed successfully! Amount deducted from sales: KES ' . number_format($refundAmount, 2),
                'return_no' => $return->return_no,
                'refund_amount' => $refundAmount,
                'return_id' => $return->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process return: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $return = ReturnModel::with(['originalSale', 'customer', 'user'])->findOrFail($id);
        return view('returns.show', compact('return'));
    }

    public function edit($id)
    {
        $return = ReturnModel::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        return view('returns.edit', compact('return', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $return = ReturnModel::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'notes' => 'nullable|string'
        ]);

        $return->update($validated);

        return redirect()->route('returns.index')
            ->with('success', 'Return updated successfully!');
    }

    public function destroy($id)
    {
        $return = ReturnModel::findOrFail($id);
        $return->delete();

        return redirect()->route('returns.index')
            ->with('success', 'Return deleted successfully!');
    }

    public function printReturn($id)
    {
        $return = ReturnModel::with(['originalSale', 'customer', 'user'])->findOrFail($id);
        return view('returns.print', compact('return'));
    }
}