<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['product', 'user'])
            ->when($request->type, function($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->reason, function($q) use ($request) {
                return $q->where('reason', $request->reason);
            })
            ->when($request->product_id, function($q) use ($request) {
                return $q->where('product_id', $request->product_id);
            })
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->start_date, function($q) use ($request) {
                return $q->whereDate('adjustment_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($q) use ($request) {
                return $q->whereDate('adjustment_date', '<=', $request->end_date);
            })
            ->orderBy('created_at', 'desc');

        $adjustments = $query->paginate(20);
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $summary = [
            'total_adjustments' => StockAdjustment::count(),
            'total_increase' => StockAdjustment::where('type', 'increase')->sum('quantity'),
            'total_decrease' => StockAdjustment::where('type', 'decrease')->sum('quantity'),
            'total_products_affected' => StockAdjustment::distinct('product_id')->count(),
            'pending' => StockAdjustment::where('status', 'pending')->count(),
            'completed' => StockAdjustment::where('status', 'completed')->count(),
            'cancelled' => StockAdjustment::where('status', 'cancelled')->count(),
        ];

        return view('stock-adjustments.index', compact('adjustments', 'products', 'summary'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock-adjustments.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:spoilage,damage,theft,return,correction,expired,lost,other',
            'reason_description' => 'nullable|string|max:255',
            'adjustment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $product = Product::lockForUpdate()->find($validated['product_id']);
            
            if (!$product) {
                throw new \Exception('Product not found.');
            }

            // Check if decreasing stock would go below zero
            if ($validated['type'] === 'decrease' && $product->stock_quantity < $validated['quantity']) {
                throw new \Exception("Insufficient stock. Available: {$product->stock_quantity}, Requested to remove: {$validated['quantity']}");
            }

            $oldStock = $product->stock_quantity;
            
            // For increase, stock is updated immediately
            // For decrease, stock is NOT updated until the adjustment is completed
            $newStock = $validated['type'] === 'increase' 
                ? $oldStock + $validated['quantity'] 
                : $oldStock; // Don't change stock for decrease until completed

            // Create adjustment record
            $adjustment = StockAdjustment::create([
                'adjustment_no' => 'ADJ-' . date('Ymd') . '-' . str_pad(StockAdjustment::count() + 1, 4, '0', STR_PAD_LEFT),
                'product_id' => $validated['product_id'],
                'user_id' => Auth::id(),
                'outlet_id' => Auth::user()->outlet_id ?? 1,
                'quantity' => $validated['quantity'],
                'type' => $validated['type'],
                'reason' => $validated['reason'],
                'reason_description' => $validated['reason_description'],
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'adjustment_date' => $validated['adjustment_date'],
                'status' => 'pending',
                'notes' => $validated['notes']
            ]);

            // If increase, update stock immediately
            if ($validated['type'] === 'increase') {
                $product->update(['stock_quantity' => $newStock]);
                $adjustment->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('stock-adjustments.show', $adjustment)
                ->with('success', "Stock adjustment created successfully! Reference: {$adjustment->adjustment_no}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock adjustment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create stock adjustment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['product', 'user']);
        return view('stock-adjustments.show', compact('stockAdjustment'));
    }

    public function edit(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status === 'completed') {
            return redirect()->route('stock-adjustments.index')
                ->with('error', 'Cannot edit a completed adjustment.');
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock-adjustments.edit', compact('stockAdjustment', 'products'));
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status === 'completed') {
            return redirect()->route('stock-adjustments.index')
                ->with('error', 'Cannot update a completed adjustment.');
        }

        $validated = $request->validate([
            'reason' => 'sometimes|in:spoilage,damage,theft,return,correction,expired,lost,other',
            'reason_description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $stockAdjustment->update($validated);

        return redirect()->route('stock-adjustments.show', $stockAdjustment)
            ->with('success', 'Stock adjustment updated successfully!');
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        // We don't allow deletion to maintain audit trail
        return redirect()->route('stock-adjustments.index')
            ->with('error', 'Stock adjustments cannot be deleted for audit purposes.');
    }

    /**
     * Complete a stock adjustment
     */
    public function complete(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status === 'completed') {
            return redirect()->back()
                ->with('error', 'This adjustment is already completed.');
        }

        if ($stockAdjustment->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'Cannot complete a cancelled adjustment.');
        }

        DB::beginTransaction();

        try {
            $product = Product::lockForUpdate()->find($stockAdjustment->product_id);
            
            if (!$product) {
                throw new \Exception('Product not found.');
            }

            // For decrease, check if stock is sufficient
            if ($stockAdjustment->type === 'decrease' && $product->stock_quantity < $stockAdjustment->quantity) {
                throw new \Exception("Insufficient stock. Available: {$product->stock_quantity}, Requested to remove: {$stockAdjustment->quantity}");
            }

            // Calculate new stock
            $oldStock = $product->stock_quantity;
            $newStock = $stockAdjustment->type === 'increase' 
                ? $oldStock + $stockAdjustment->quantity 
                : $oldStock - $stockAdjustment->quantity;

            // Update product stock
            $product->update(['stock_quantity' => $newStock]);

            // Update adjustment record
            $stockAdjustment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'old_stock' => $oldStock,
                'new_stock' => $newStock
            ]);

            DB::commit();

            return redirect()->route('stock-adjustments.show', $stockAdjustment)
                ->with('success', 'Stock adjustment completed successfully! Stock updated from ' . $oldStock . ' to ' . $newStock . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock adjustment completion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to complete adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a stock adjustment
     */
    public function cancel(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot cancel a completed adjustment.');
        }

        if ($stockAdjustment->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'This adjustment is already cancelled.');
        }

        $stockAdjustment->update([
            'status' => 'cancelled',
            'completed_at' => now()
        ]);

        return redirect()->route('stock-adjustments.show', $stockAdjustment)
            ->with('success', 'Stock adjustment cancelled successfully.');
    }

    public function getProductStock($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                'stock' => $product->stock_quantity,
                'name' => $product->name,
                'sku' => $product->sku
            ]);
        }
        return response()->json(['stock' => 0], 404);
    }
}