<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromOutlet', 'toOutlet', 'user', 'items.product'])
            ->orderByDesc('transferred_at');

        $outletId = $this->resolvedOutletId($request);
        if ($outletId) {
            $query->where(function ($q) use ($outletId) {
                $q->where('from_outlet_id', $outletId)
                    ->orWhere('to_outlet_id', $outletId);
            });
        }

        $transfers = $query->paginate(20);
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();

        return view('stock-transfers.index', compact('transfers', 'outlets', 'outletId'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $defaultFromOutlet = $this->resolvedOutletId(request());

        $products = collect();
        if ($defaultFromOutlet) {
            $products = Product::withoutGlobalScopes()
                ->where('outlet_id', $defaultFromOutlet)
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'stock_quantity']);
        }

        return view('stock-transfers.create', compact('outlets', 'defaultFromOutlet', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_outlet_id' => 'required|exists:outlets,id',
            'to_outlet_id' => 'required|exists:outlets,id|different:from_outlet_id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if (!$this->canTransferFrom($validated['from_outlet_id'])) {
            return back()->withInput()->with('error', 'You are not allowed to transfer stock from this store.');
        }

        try {
            $transfer = DB::transaction(function () use ($validated) {
                $referenceNo = 'ST-' . date('Ymd') . '-' . str_pad(
                    StockTransfer::count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                $transfer = StockTransfer::create([
                    'reference_no' => $referenceNo,
                    'from_outlet_id' => $validated['from_outlet_id'],
                    'to_outlet_id' => $validated['to_outlet_id'],
                    'user_id' => Auth::id(),
                    'status' => 'completed',
                    'notes' => $validated['notes'] ?? null,
                    'transferred_at' => now(),
                ]);

                foreach ($validated['items'] as $item) {
                    $this->transferProduct(
                        $transfer,
                        (int) $item['product_id'],
                        (int) $item['quantity'],
                        (int) $validated['from_outlet_id'],
                        (int) $validated['to_outlet_id']
                    );
                }

                return $transfer;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('stock-transfers.show', $transfer)
            ->with('success', "Stock transfer {$transfer->reference_no} completed successfully.");
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['fromOutlet', 'toOutlet', 'user', 'items.product', 'items.destinationProduct']);

        return view('stock-transfers.show', compact('stockTransfer'));
    }

    public function products(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'q' => 'nullable|string|max:100',
        ]);

        if (!$this->canTransferFrom((int) $request->outlet_id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Product::withoutGlobalScopes()
            ->where('outlet_id', $request->outlet_id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->limit(20)->get(['id', 'name', 'sku', 'stock_quantity']);

        return response()->json($products);
    }

    private function transferProduct(
        StockTransfer $transfer,
        int $productId,
        int $quantity,
        int $fromOutletId,
        int $toOutletId
    ): void {
        $sourceProduct = Product::withoutGlobalScopes()
            ->where('id', $productId)
            ->where('outlet_id', $fromOutletId)
            ->lockForUpdate()
            ->first();

        if (!$sourceProduct) {
            throw new \RuntimeException('One or more products were not found at the source store.');
        }

        if ($sourceProduct->stock_quantity < $quantity) {
            throw new \RuntimeException(
                "Insufficient stock for {$sourceProduct->name}. Available: {$sourceProduct->stock_quantity}."
            );
        }

        $destinationProduct = Product::withoutGlobalScopes()
            ->where('outlet_id', $toOutletId)
            ->where('sku', $sourceProduct->sku)
            ->lockForUpdate()
            ->first();

        if (!$destinationProduct) {
            $destinationProduct = Product::withoutGlobalScopes()->create([
                'name' => $sourceProduct->name,
                'sku' => $sourceProduct->sku,
                'barcode' => $sourceProduct->barcode,
                'price' => $sourceProduct->price,
                'cost' => $sourceProduct->cost,
                'stock_quantity' => 0,
                'low_stock_threshold' => $sourceProduct->low_stock_threshold,
                'category' => $sourceProduct->category,
                'category_id' => $sourceProduct->category_id,
                'image' => $sourceProduct->image,
                'is_active' => $sourceProduct->is_active,
                'is_favorite' => false,
                'outlet_id' => $toOutletId,
            ]);
        }

        $sourceProduct->decrement('stock_quantity', $quantity);
        $destinationProduct->increment('stock_quantity', $quantity);

        StockTransferItem::create([
            'stock_transfer_id' => $transfer->id,
            'product_id' => $sourceProduct->id,
            'destination_product_id' => $destinationProduct->id,
            'quantity' => $quantity,
        ]);
    }

    private function resolvedOutletId(Request $request): ?int
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return (int) ($request->query('outlet_id') ?: session('active_outlet_id', \App\Models\Outlet::first()?->id ?? 1));
        }

        return $user->outlet_id ? (int) $user->outlet_id : null;
    }

    private function canTransferFrom(int $outletId): bool
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return true;
        }

        return (int) $user->outlet_id === $outletId;
    }
}
