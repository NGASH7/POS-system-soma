<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with(['customer', 'user'])
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->customer_id, function($q) use ($request) {
                return $q->where('customer_id', $request->customer_id);
            })
            ->when($request->search, function($q) use ($request) {
                return $q->where('quotation_no', 'like', "%{$request->search}%");
            })
            ->orderBy('created_at', 'desc');

        $quotations = $query->paginate(20); // <-- FIXED: Define $quotations here

        $customers = Customer::orderBy('name')->get();

        $summary = [
            'total' => Quotation::count(),
            'draft' => Quotation::where('status', 'draft')->count(),
            'sent' => Quotation::where('status', 'sent')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'converted' => Quotation::where('status', 'converted')->count(),
            'expired' => Quotation::where('status', 'expired')->count(),
        ];

        return view('quotations.index', compact('quotations', 'customers', 'summary'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->take(50)
            ->get();
        
        // Generate quotation number
        $quotationNo = 'QTN-' . date('Ymd') . '-' . str_pad(Quotation::count() + 1, 4, '0', STR_PAD_LEFT);
        
        return view('quotations.create', compact('customers', 'products', 'quotationNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'quotation_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:quotation_date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|json',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();

        try {
            $quotation = Quotation::create([
                'quotation_no' => $request->quotation_no,
                'customer_id' => $validated['customer_id'],
                'user_id' => Auth::id(),
                'outlet_id' => Auth::user()->outlet_id ?? 1,
                'quotation_date' => $validated['quotation_date'],
                'expiry_date' => $validated['expiry_date'],
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'],
                'total' => $validated['total'],
                'notes' => $validated['notes'],
                'terms' => $validated['terms'],
                'status' => 'draft',
                'items' => json_decode($validated['items'], true)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quotation created successfully!',
                'quotation_id' => $quotation->id,
                'quotation_no' => $quotation->quotation_no
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quotation creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quotation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'user']);
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return redirect()->route('quotations.index')
                ->with('error', 'Cannot edit a converted quotation.');
        }

        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->take(50)->get();
        return view('quotations.edit', compact('quotation', 'customers', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update a converted quotation.'
            ], 422);
        }

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'quotation_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|json',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();

        try {
            $quotation->update([
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'expiry_date' => $validated['expiry_date'],
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'],
                'total' => $validated['total'],
                'notes' => $validated['notes'],
                'terms' => $validated['terms'],
                'items' => json_decode($validated['items'], true)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quotation updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quotation update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update quotation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return redirect()->back()
                ->with('error', 'Cannot delete a converted quotation.');
        }

        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully!');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,approved,converted,expired,cancelled'
        ]);

        if ($validated['status'] === 'converted') {
            return redirect()->back()
                ->with('error', 'Use the convert action to convert to sale.');
        }

        $quotation->update(['status' => $validated['status']]);

        return redirect()->route('quotations.index')
            ->with('success', "Quotation status updated to " . ucfirst($validated['status']));
    }

    public function convertToSale(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return redirect()->back()
                ->with('error', 'Quotation already converted.');
        }

        DB::beginTransaction();

        try {
            // Check if items have stock
            $items = $quotation->items;
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$item['product_name']}. Available: " . ($product->stock_quantity ?? 0));
                }
            }

            // Create sale
            $invoiceNo = 'SALE-' . date('Ymd') . '-' . str_pad(Sale::count() + 1, 4, '0', STR_PAD_LEFT);
            
            $sale = Sale::create([
                'invoice_no' => $invoiceNo,
                'user_id' => Auth::id(),
                'customer_id' => $quotation->customer_id,
                'terminal_id' => session()->get('terminal_id', 'TERM-01'),
                'subtotal' => $quotation->subtotal,
                'discount' => $quotation->discount,
                'tax' => $quotation->tax,
                'total' => $quotation->total,
                'paid' => 0,
                'change_due' => 0,
                'payment_method' => 'pending',
                'status' => 'pending',
                'sale_date' => now(),
                'notes' => "Converted from quotation: {$quotation->quotation_no}"
            ]);

            // Create sale items
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity']
                ]);

                Product::where('id', $item['product_id'])->decrement('stock_quantity', $item['quantity']);
            }

            // Update quotation
            $quotation->update([
                'status' => 'converted',
                'converted_at' => now(),
                'converted_sale_id' => $sale->id
            ]);

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', "Quotation converted to sale successfully! Invoice: {$invoiceNo}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quotation conversion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to convert quotation: ' . $e->getMessage());
        }
    }

    public function printQuotation(Quotation $quotation)
    {
        $quotation->load(['customer', 'user']);
        return view('quotations.print', compact('quotation'));
    }
}