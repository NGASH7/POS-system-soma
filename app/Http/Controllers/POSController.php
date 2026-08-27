<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\HoldTicket;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use FelixMuhoro\Mpesa\Facades\Mpesa;

class POSController extends Controller
{
    public function index()
    {
        // Debug: Check if cart exists in session
        $cart = session()->get('pos_cart', []);
        Log::info('POS Index - Current cart:', ['cart' => $cart, 'cart_count' => count($cart)]);
        
        $query = Product::where('is_active', true)
                        ->where('stock_quantity', '>', 0);
                        
        if (request()->filled('category_id')) {
            $query->where('category_id', request('category_id'));
        }
        
        if (request()->boolean('favorites')) {
            $topProductIds = DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->whereMonth('sales.created_at', now()->month)
                ->whereYear('sales.created_at', now()->year)
                ->select('product_id')
                ->groupBy('product_id')
                ->orderByRaw('SUM(quantity) DESC')
                ->limit(20)
                ->pluck('product_id');

            $query->whereIn('id', $topProductIds);
        }
        
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        
        $customers = Customer::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $availableDiscounts = Discount::validNow()->orderBy('name')->get();
        
        return view('pos.index', compact('products', 'customers', 'categories', 'availableDiscounts'));
    }

    public function posList(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'items'])
            ->where('is_return', false);

        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Invoice / customer search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        // Summary KPIs for filtered period
        $summaryQuery = clone $query;
        $totalRevenue  = (clone $summaryQuery)->sum('total');
        $totalDiscount = (clone $summaryQuery)->sum('discount');
        $totalTax      = (clone $summaryQuery)->sum('tax');
        $totalCount    = (clone $summaryQuery)->count();

        $sales = $query->orderBy('sale_date', 'desc')->paginate(25)->withQueryString();

        return view('pos.list', compact('sales', 'totalRevenue', 'totalDiscount', 'totalTax', 'totalCount'));
    }

    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);
            
            $product = Product::findOrFail($request->product_id);
            
            // Check if product has stock
            if($product->stock_quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is out of stock'
                ], 422);
            }
            
            // Get cart from session
            $cart = session()->get('pos_cart', []);
            
            if(isset($cart[$product->id])) {
                // Check if adding one more exceeds stock
                if($cart[$product->id]['quantity'] + 1 > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot add more. Only {$product->stock_quantity} in stock"
                    ], 422);
                }
                $cart[$product->id]['quantity']++;
            } else {
                $cart[$product->id] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float)$product->price,
                    'quantity' => 1,
                    'sku' => $product->sku,
                    'stock' => $product->stock_quantity
                ];
            }
            
            session()->put('pos_cart', $cart);
            
            Log::info('Product added to cart', ['product_id' => $product->id, 'cart_count' => count($cart)]);
            
            return response()->json([
                'success' => true,
                'cart' => $this->formatCart($cart),
                'totals' => $this->calculateTotals($cart),
                'message' => "{$product->name} added to cart"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error adding to cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updateCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required',
                'action' => 'required|in:increase,decrease,remove'
            ]);
            
            $cart = session()->get('pos_cart', []);
            
            if(!isset($cart[$request->product_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in cart'
                ], 404);
            }
            
            $product = Product::find($request->product_id);
            
            if($request->action == 'increase') {
                // Check stock before increasing
                if($product && $cart[$request->product_id]['quantity'] + 1 > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot increase. Only {$product->stock_quantity} in stock"
                    ], 422);
                }
                $cart[$request->product_id]['quantity']++;
            } elseif($request->action == 'decrease') {
                $cart[$request->product_id]['quantity']--;
                if($cart[$request->product_id]['quantity'] <= 0) {
                    unset($cart[$request->product_id]);
                }
            } elseif($request->action == 'remove') {
                unset($cart[$request->product_id]);
            }
            
            session()->put('pos_cart', $cart);
            
            return response()->json([
                'success' => true,
                'cart' => $this->formatCart($cart),
                'totals' => $this->calculateTotals($cart)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }
    
    public function getCart()
    {
        try {
            $cart = session()->get('pos_cart', []);
            
            Log::info('Getting cart contents', ['cart_items' => count($cart)]);
            
            return response()->json([
                'success' => true,
                'cart' => $this->formatCart($cart),
                'totals' => $this->calculateTotals($cart)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'cart' => [],
                'totals' => ['subtotal' => 0, 'tax' => 0, 'total' => 0]
            ]);
        }
    }
    
    public function clearCart()
    {
        try {
            session()->forget('pos_cart');
            session()->forget('pos_discount');
            
            Log::info('Cart cleared');
            
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart'
            ], 500);
        }
    }
    
    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'payment_method' => 'required|in:cash,card,mobile_money,credit',
                'paid_amount' => 'required_if:payment_method,cash|numeric|min:0',
                'customer_id' => 'required_if:payment_method,credit|nullable|exists:customers,id',
                'mobile_provider' => 'required_if:payment_method,mobile_money|in:mpesa,airtel_money,tkash',
                'phone_number' => 'required_if:payment_method,mobile_money|string'
            ]);
            
            // Get cart from SESSION
            $cart = session()->get('pos_cart');
            
            // Debug: Log cart contents
            Log::info('Checkout - Cart contents:', ['cart' => $cart, 'cart_is_empty' => empty($cart)]);
            
            if(empty($cart) || count($cart) == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty. Please add items before checkout.'
                ], 422);
            }
            
            $totals = $this->calculateTotals($cart);
            
            // For mobile money, initiate STK Push instead of completing immediately
            if ($request->payment_method === 'mobile_money') {
                return $this->initiateMobilePayment($request, $cart, $totals);
            }
            
            // For cash payment, check if paid amount is sufficient
            if($request->payment_method === 'cash' && $request->paid_amount < $totals['total']) {
                return response()->json([
                    'success' => false,
                    'message' => sprintf('Insufficient payment amount. Total: $%.2f, Paid: $%.2f', $totals['total'], $request->paid_amount)
                ], 422);
            }
            
            // Process cash, card, or credit payment
            return $this->processCashOrCardPayment($request, $cart, $totals);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'cart' => session()->get('pos_cart')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Initiate mobile money payment with real M-Pesa STK Push
     */
    private function initiateMobilePayment($request, $cart, $totals)
    {
        try {
            $phoneNumber = $this->formatPhoneNumber($request->phone_number);
            
            Log::info('Initiating mobile payment:', [
                'original_phone' => $request->phone_number,
                'formatted_phone' => $phoneNumber,
                'amount' => $totals['total']
            ]);
            
            if (!$phoneNumber || strlen($phoneNumber) !== 12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number. Must be 12 digits including country code (2547XXXXXXXX)'
                ], 422);
            }

            // Generate unique reference
            $reference = 'TXN-' . time() . '-' . rand(1000, 9999);
            
            // Store transaction pending confirmation
            $transaction = Transaction::create([
                'reference' => $reference,
                'amount' => $totals['total'],
                'phone' => $phoneNumber,
                'status' => 'pending',
                'provider' => $request->mobile_provider,
                'cart_data' => json_encode($cart),
                'payment_data' => json_encode([
                    'customer_id' => $request->customer_id,
                    'payment_method' => 'mobile_money',
                    'mobile_provider' => $request->mobile_provider
                ]),
                'user_id' => Auth::id()
            ]);

            // Process based on provider
            if ($request->mobile_provider === 'mpesa') {
                try {
                    // Real M-Pesa STK Push
                    $stkPushResponse = Mpesa::stkPush(
                        phone: $phoneNumber,
                        amount: (int) round($totals['total']),
                        reference: $reference,
                        description: 'Payment for order ' . $reference
                    );

                    Log::info('M-Pesa STK Push Response:', ['response' => $stkPushResponse]);

                    if ($stkPushResponse && $stkPushResponse->accepted()) {
                        $transaction->update([
                            'provider_reference' => $stkPushResponse->checkoutRequestId,
                            'status' => 'processing'
                        ]);

                        return response()->json([
                            'success' => true,
                            'requires_payment' => true,
                            'message' => 'STK Push sent to ' . $phoneNumber . '. Enter your PIN to complete payment.',
                            'transaction_id' => $transaction->id,
                            'reference' => $reference
                        ]);
                    } else {
                        $transaction->update(['status' => 'failed']);
                        $errorMessage = $stkPushResponse->responseDescription
                            ?: 'Failed to initiate payment. Please try again.';
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'M-Pesa Error: ' . $errorMessage
                        ], 500);
                    }
                } catch (\Exception $e) {
                    Log::error('M-Pesa STK Push Exception: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    $transaction->update(['status' => 'failed']);
                    
                    if (strpos($e->getMessage(), 'Could not retrieve M-Pesa OAuth token') !== false) {
                        return response()->json([
                            'success' => false,
                            'message' => 'M-Pesa is not properly configured. Please check your credentials or use Cash payment.'
                        ], 500);
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment initiation failed: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // For Airtel Money or other providers
            if ($request->mobile_provider === 'airtel_money') {
                Log::info('Airtel Money payment initiated', [
                    'transaction_id' => $transaction->id,
                    'phone' => $phoneNumber,
                    'amount' => $totals['total']
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Airtel Money integration coming soon. Please use M-Pesa or Cash.'
                ], 501);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unsupported mobile money provider'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Mobile payment initiation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle M-Pesa STK Push Callback
     */
    public function mpesaCallback(Request $request)
    {
        try {
            // Log EVERYTHING for debugging
            Log::info('M-Pesa Callback RAW REQUEST:', [
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'content' => $request->getContent(),
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            $callbackData = json_decode($request->getContent(), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON in M-Pesa callback: ' . json_last_error_msg());
                return response()->json(['message' => 'Invalid JSON'], 400);
            }
            
            Log::info('M-Pesa Callback parsed:', $callbackData);

            $resultCode = $callbackData['Body']['stkCallback']['ResultCode'] ?? null;
            $resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'] ?? '';
            $checkoutRequestId = $callbackData['Body']['stkCallback']['CheckoutRequestID'] ?? null;
            
            if (!$checkoutRequestId) {
                Log::error('No CheckoutRequestID in callback');
                return response()->json(['message' => 'Missing CheckoutRequestID'], 400);
            }

            // Find transaction by provider reference
            $transaction = Transaction::where('provider_reference', $checkoutRequestId)->first();

            if (!$transaction) {
                Log::error('Transaction not found for checkout ID: ' . $checkoutRequestId);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Extract metadata
            $mpesaReceiptNumber = null;
            $amount = null;
            $phoneNumber = null;
            
            if (isset($callbackData['Body']['stkCallback']['CallbackMetadata']['Item'])) {
                foreach ($callbackData['Body']['stkCallback']['CallbackMetadata']['Item'] as $item) {
                    if ($item['Name'] == 'Amount') {
                        $amount = $item['Value'];
                    } elseif ($item['Name'] == 'MpesaReceiptNumber') {
                        $mpesaReceiptNumber = $item['Value'];
                    } elseif ($item['Name'] == 'PhoneNumber') {
                        $phoneNumber = $item['Value'];
                    }
                }
            }

            if ($resultCode == 0) {
                // Payment successful - process the sale
                $cart = json_decode($transaction->cart_data, true);
                $paymentData = json_decode($transaction->payment_data, true);
                $totals = $this->calculateTotals($cart);

                DB::beginTransaction();

                try {
                    // Check stock
                    foreach ($cart as $id => $item) {
                        $product = Product::lockForUpdate()->find($id);
                        if (!$product || $product->stock_quantity < $item['quantity']) {
                            throw new \Exception("Insufficient stock for {$item['name']}");
                        }
                    }

                    // Create sale
                    $invoiceNo = $this->generateInvoiceNo();
                    $sale = Sale::create([
                        'invoice_no' => $invoiceNo,
                        'user_id' => $transaction->user_id,
                        'customer_id' => $paymentData['customer_id'] ?? null,
                        'terminal_id' => session()->get('terminal_id', 'TERM-01'),
                        'subtotal' => $totals['subtotal'],
                        'discount' => 0,
                        'tax' => $totals['tax'],
                        'total' => $totals['total'],
                        'paid' => $totals['total'],
                        'change_due' => 0,
                        'payment_method' => 'mobile_money',
                        'status' => 'completed',
                        'sale_date' => now(),
                        'mpesa_receipt' => $mpesaReceiptNumber
                    ]);

                    // Create sale items and update stock
                    foreach ($cart as $id => $item) {
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $id,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'total' => $item['price'] * $item['quantity']
                        ]);

                        Product::where('id', $id)->decrement('stock_quantity', $item['quantity']);
                    }

                    // Update customer
                    if ($paymentData['customer_id'] ?? null) {
                        $customer = Customer::withoutGlobalScopes()->find($paymentData['customer_id']);
                        if ($customer) {
                            $customer->increment('total_spent', $totals['total']);
                            $customer->increment('points', floor($totals['total'] / 10));
                        }
                    }

                    // Update transaction
                    $transaction->update([
                        'status' => 'completed',
                        'sale_id' => $sale->id,
                        'completed_at' => now()
                    ]);

                    DB::commit();

                    Log::info('M-Pesa payment completed', [
                        'sale_id' => $sale->id,
                        'invoice' => $invoiceNo,
                        'mpesa_receipt' => $mpesaReceiptNumber
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    $transaction->update(['status' => 'failed']);
                    Log::error('Failed to process sale after M-Pesa payment: ' . $e->getMessage());
                }
            } else {
                // Payment failed
                $transaction->update([
                    'status' => 'failed',
                    'error_message' => $resultDesc
                ]);
                Log::warning('M-Pesa payment failed', [
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc
                ]);
            }

            return response()->json(['message' => 'Callback processed successfully']);

        } catch (\Exception $e) {
            Log::error('M-Pesa callback error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing callback'], 500);
        }
    }

    /**
     * Process cash or card payment
     */
    private function processCashOrCardPayment($request, $cart, $totals)
    {
        DB::beginTransaction();

        try {
            // Check stock before sale
            foreach ($cart as $id => $item) {
                $product = Product::lockForUpdate()->find($id);
                if (!$product || $product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$item['name']}. Available: " . ($product->stock_quantity ?? 0));
                }
            }

            // Generate invoice number
            $invoiceNo = $this->generateInvoiceNo();

            // Calculate paid amount
            $paid = $request->payment_method === 'credit' ? 0 : ($request->paid_amount ?? $totals['total']);
            $change_due = $request->payment_method === 'credit' ? 0 : (($request->paid_amount ?? $totals['total']) - $totals['total']);

            $appliedDiscount = $totals['applied_discount'] ?? session()->get('pos_discount', null);

            // Create sale
            $sale = Sale::create([
                'invoice_no' => $invoiceNo,
                'user_id' => Auth::id(),
                'customer_id' => $request->customer_id,
                'terminal_id' => session()->get('terminal_id', 'TERM-01'),
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'discount_type' => $appliedDiscount['type'] ?? null,
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'paid' => $paid,
                'change_due' => $change_due,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'sale_date' => now()
            ]);

            if (!empty($appliedDiscount['id'])) {
                Discount::where('id', $appliedDiscount['id'])->increment('used_count');
            }

            session()->forget('pos_discount');

            // Create customer credit if payment method is credit
            if ($request->payment_method === 'credit') {
                \App\Models\CustomerCredit::create([
                    'customer_id' => $request->customer_id,
                    'user_id' => Auth::id(),
                    'reference' => 'CR-' . $invoiceNo,
                    'type' => 'direct_credit',
                    'total_amount' => $totals['total'],
                    'paid_amount' => 0,
                    'balance' => $totals['total'],
                    'due_date' => now()->addDays(30),
                    'status' => 'active',
                    'notes' => 'Generated from POS Sale ' . $invoiceNo,
                ]);
            }

            Log::info('Sale created', ['sale_id' => $sale->id, 'invoice_no' => $invoiceNo]);

            // Create sale items and update stock
            foreach ($cart as $id => $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => 0,
                    'total' => $item['price'] * $item['quantity']
                ]);

                Product::where('id', $id)->decrement('stock_quantity', $item['quantity']);
                Log::info('Stock updated', ['product_id' => $id, 'quantity_decreased' => $item['quantity']]);
            }

            // Update customer total spent if customer exists
            if ($request->customer_id) {
                $customer = Customer::withoutGlobalScopes()->find($request->customer_id);
                if ($customer) {
                    $customer->increment('total_spent', $totals['total']);
                    $customer->increment('points', floor($totals['total'] / 10));
                    
                    if ($request->payment_method === 'credit') {
                        $customer->increment('total_credit', $totals['total']);
                        $customer->increment('available_credit', $totals['total']);
                    }
                    
                    Log::info('Customer updated', ['customer_id' => $request->customer_id, 'points_earned' => floor($totals['total'] / 10)]);
                }
            }

            DB::commit();
            session()->forget('pos_cart');

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'invoice_no' => $invoiceNo,
                'change_due' => ($request->paid_amount ?? $totals['total']) - $totals['total'],
                'receipt_url' => route('pos.receipt', $sale->id),
                'message' => 'Sale completed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cash/Card payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process completed mobile payment and create sale
     */
    public function processMobilePayment($transactionId)
    {
        $transaction = Transaction::find($transactionId);
        
        if (!$transaction || $transaction->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid transaction'
            ], 400);
        }

        $cart = json_decode($transaction->cart_data, true);
        $paymentData = json_decode($transaction->payment_data, true);
        $totals = $this->calculateTotals($cart);

        DB::beginTransaction();

        try {
            // Check stock
            foreach ($cart as $id => $item) {
                $product = Product::lockForUpdate()->find($id);
                if (!$product || $product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$item['name']}");
                }
            }

            // Create sale
            $invoiceNo = $this->generateInvoiceNo();
            $sale = Sale::create([
                'invoice_no' => $invoiceNo,
                'user_id' => $transaction->user_id,
                'customer_id' => $paymentData['customer_id'] ?? null,
                'terminal_id' => session()->get('terminal_id', 'TERM-01'),
                'subtotal' => $totals['subtotal'],
                'discount' => 0,
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'paid' => $totals['total'],
                'change_due' => 0,
                'payment_method' => 'mobile_money',
                'status' => 'completed',
                'sale_date' => now()
            ]);

            // Create sale items and update stock
            foreach ($cart as $id => $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity']
                ]);

                Product::where('id', $id)->decrement('stock_quantity', $item['quantity']);
            }

            // Update customer
            if ($paymentData['customer_id'] ?? null) {
                $customer = Customer::withoutGlobalScopes()->find($paymentData['customer_id']);
                if ($customer) {
                    $customer->increment('total_spent', $totals['total']);
                    $customer->increment('points', floor($totals['total'] / 10));
                }
            }

            // Update transaction
            $transaction->update([
                'status' => 'completed',
                'sale_id' => $sale->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'invoice_no' => $invoiceNo,
                'receipt_url' => route('pos.receipt', $sale->id),
                'message' => 'Payment completed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $transaction->update(['status' => 'failed']);
            Log::error('Mobile payment processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check transaction status
     */
    public function checkTransactionStatus($transactionId)
    {
        $transaction = Transaction::find($transactionId);
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'sale_id' => $transaction->sale_id,
            'reference' => $transaction->reference,
            'message' => $transaction->status === 'completed' ? 'Payment completed' : 'Payment pending'
        ]);
    }

    /**
     * Format phone number to international format (254XXXXXXXXX)
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading 0
        if (substr($phone, 0, 1) == '0') {
            $phone = '254' . substr($phone, 1);
        } 
        // Add 254 if not present
        elseif (substr($phone, 0, 3) != '254') {
            $phone = '254' . $phone;
        }
        
        Log::info('Formatted phone number:', ['original' => $phone, 'formatted' => $phone]);
        
        return $phone;
    }
    
    public function receipt($id)
    {
        try {
            $sale = Sale::with('items.product', 'user', 'customer')->findOrFail($id);
            return view('pos.receipt', compact('sale'));
        } catch (\Exception $e) {
            Log::error('Receipt error: ' . $e->getMessage());
            abort(404, 'Receipt not found');
        }
    }
    
    public function printReceipt($id)
    {
        try {
            $sale = Sale::with('items.product', 'user', 'customer')->findOrFail($id);

            if (request()->boolean('embed') || request()->ajax()) {
                return view('pos.receipt-embed', compact('sale'));
            }

            return view('pos.receipt', compact('sale'));
            
        } catch (\Exception $e) {
            Log::error('Print receipt error: ' . $e->getMessage());
            
            if(request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load receipt'
                ], 500);
            }
            
            abort(404, 'Receipt not found');
        }
    }
    
    /**
     * View receipt in popup or full page
     */
    public function viewReceipt($id)
    {
        try {
            $sale = Sale::with('items.product', 'user', 'customer')->findOrFail($id);
            
            if (request()->boolean('embed') || request()->ajax()) {
                return view('pos.receipt-embed', compact('sale'));
            }

            return view('pos.receipt', compact('sale'));
            
        } catch (\Exception $e) {
            Log::error('View receipt error: ' . $e->getMessage());
            
            if(request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load receipt'
                ], 500);
            }
            
            abort(404, 'Receipt not found');
        }
    }
    
    private function formatCart($cart)
    {
        if(empty($cart)) {
            return [];
        }
        
        return collect($cart)->map(function($item, $id) {
            return [
                'id' => (int)$id,
                'name' => $item['name'],
                'quantity' => (int)$item['quantity'],
                'price' => (float)$item['price'],
                'total' => (float)($item['price'] * $item['quantity'])
            ];
        })->values()->toArray();
    }
    
    private function getCartGrossTotal($cart)
    {
        $grossTotal = 0;
        foreach ($cart as $item) {
            $grossTotal += $item['price'] * $item['quantity'];
        }
        return (float)$grossTotal;
    }

    private function calculateDiscountAmount($cart, $discountData)
    {
        if (empty($cart) || empty($discountData)) {
            return 0.0;
        }

        $grossTotal = $this->getCartGrossTotal($cart);
        if ($grossTotal <= 0) {
            return 0.0;
        }

        if (!empty($discountData['id'])) {
            $discountModel = Discount::find($discountData['id']);
            if ($discountModel) {
                return $discountModel->calculateDiscount($grossTotal);
            }
        }

        $type = $discountData['type'] ?? 'fixed';
        $value = (float)($discountData['value'] ?? 0);

        if ($type === 'percentage') {
            $amount = ($grossTotal * $value) / 100;
            if (!empty($discountData['max_discount']) && $amount > (float)$discountData['max_discount']) {
                $amount = (float)$discountData['max_discount'];
            }
            return min($amount, $grossTotal);
        }

        return min($value, $grossTotal);
    }

    private function calculateTotals($cart, $discountData = null)
    {
        if (empty($cart)) {
            return [
                'gross_total' => 0.0,
                'discount' => 0.0,
                'subtotal' => 0.0,
                'tax' => 0.0,
                'total' => 0.0,
                'applied_discount' => null,
            ];
        }

        if ($discountData === null) {
            $discountData = session()->get('pos_discount', null);
        }

        $grossTotal = $this->getCartGrossTotal($cart);
        $discountAmount = $this->calculateDiscountAmount($cart, $discountData);

        // 16% VAT is inclusive in product prices
        $total = max(0.0, $grossTotal - $discountAmount);
        $subtotal = $total > 0 ? round($total / 1.16, 2) : 0.0;
        $tax = $total > 0 ? round($total - $subtotal, 2) : 0.0;

        return [
            'gross_total' => (float)$grossTotal,
            'discount' => (float)$discountAmount,
            'subtotal' => (float)$subtotal,
            'tax' => (float)$tax,
            'total' => (float)$total,
            'applied_discount' => $discountData ? array_merge($discountData, ['amount' => $discountAmount]) : null,
        ];
    }

    public function applyDiscount(Request $request)
    {
        try {
            $cart = session()->get('pos_cart', []);
            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty. Add products before applying a discount.'
                ], 422);
            }

            $grossTotal = $this->getCartGrossTotal($cart);
            $discountData = null;

            // 1. By discount model ID
            if ($request->filled('discount_id')) {
                $discount = Discount::validNow()->find($request->discount_id);
                if (!$discount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The selected discount is invalid or expired.'
                    ], 422);
                }

                if ($discount->min_spend && $grossTotal < (float)$discount->min_spend) {
                    return response()->json([
                        'success' => false,
                        'message' => "Minimum spend of KES " . number_format($discount->min_spend, 2) . " required for this discount (Current: KES " . number_format($grossTotal, 2) . ")."
                    ], 422);
                }

                $discountData = [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'code' => $discount->code,
                    'type' => $discount->type,
                    'value' => (float)$discount->value,
                    'max_discount' => (float)$discount->max_discount,
                    'is_custom' => false,
                ];
            }
            // 2. By promo/coupon code
            elseif ($request->filled('code')) {
                $code = strtoupper(trim($request->code));
                $discount = Discount::validNow()->where('code', $code)->first();
                if (!$discount) {
                    return response()->json([
                        'success' => false,
                        'message' => "Coupon code '{$code}' is invalid or expired."
                    ], 422);
                }

                if ($discount->min_spend && $grossTotal < (float)$discount->min_spend) {
                    return response()->json([
                        'success' => false,
                        'message' => "Minimum spend of KES " . number_format($discount->min_spend, 2) . " required for this coupon (Current: KES " . number_format($grossTotal, 2) . ")."
                    ], 422);
                }

                $discountData = [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'code' => $discount->code,
                    'type' => $discount->type,
                    'value' => (float)$discount->value,
                    'max_discount' => (float)$discount->max_discount,
                    'is_custom' => false,
                ];
            }
            // 3. Custom discount
            elseif ($request->filled('custom_value')) {
                $type = $request->get('custom_type', 'fixed');
                $value = (float)$request->custom_value;

                if ($value <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Discount value must be greater than 0.'
                    ], 422);
                }

                if ($type === 'percentage' && $value > 100) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Percentage discount cannot exceed 100%.'
                    ], 422);
                }

                $name = $request->get('custom_name') ? trim($request->custom_name) : ($type === 'percentage' ? "{$value}% Custom Discount" : "KES {$value} Custom Discount");

                $discountData = [
                    'id' => null,
                    'name' => $name,
                    'code' => null,
                    'type' => $type,
                    'value' => $value,
                    'max_discount' => null,
                    'is_custom' => true,
                ];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a discount coupon or custom value.'
                ], 422);
            }

            session()->put('pos_discount', $discountData);
            $totals = $this->calculateTotals($cart, $discountData);

            return response()->json([
                'success' => true,
                'message' => "Discount '{$discountData['name']}' applied! Saved KES " . number_format($totals['discount'], 2),
                'totals' => $totals,
                'cart' => $this->formatCart($cart),
            ]);

        } catch (\Exception $e) {
            Log::error('Error applying discount: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply discount: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeDiscount()
    {
        try {
            session()->forget('pos_discount');
            $cart = session()->get('pos_cart', []);
            $totals = $this->calculateTotals($cart);

            return response()->json([
                'success' => true,
                'message' => 'Discount removed.',
                'totals' => $totals,
                'cart' => $this->formatCart($cart),
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing discount: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove discount'
            ], 500);
        }
    }
    
    /**
     * Generate unique invoice number with SOMA brand and branch prefix
     * Format: SOMA-BRANCH-YYYYMMDD-XXXX
     * Example: SOMA-WES-20260713-0001
     */
    private function generateInvoiceNo()
    {
        return app(\App\Services\InvoiceNumberService::class)->generate();
    }
    
    public function searchProduct(Request $request)
    {
        try {
            $search = $request->get('q');
            
            if(empty($search)) {
                return response()->json([]);
            }
            
            $products = Product::where('is_active', true)
                              ->where('stock_quantity', '>', 0)
                              ->where(function($query) use ($search) {
                                  $query->where('name', 'like', "%{$search}%")
                                        ->orWhere('sku', 'like', "%{$search}%")
                                        ->orWhere('barcode', 'like', "%{$search}%");
                              })
                              ->limit(10)
                              ->get();
            
            return response()->json($products);
            
        } catch (\Exception $e) {
            Log::error('Product search error: ' . $e->getMessage());
            return response()->json([]);
        }
    }
    
    public function holdTicket(Request $request)
    {
        try {
            $cart = session()->get('pos_cart', []);

            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot hold an empty cart.'
                ], 422);
            }

            $totals = $this->calculateTotals($cart);
            $itemsCount = collect($cart)->sum('quantity');

            $ticketNo = 'HLD-' . date('Ymd') . '-' . str_pad(HoldTicket::count() + 1, 4, '0', STR_PAD_LEFT);

            $heldTicket = HoldTicket::create([
                'ticket_number' => $ticketNo,
                'reference_note' => $request->get('reference_note'),
                'user_id' => Auth::id() ?? (\App\Models\User::first()->id ?? 1),
                'customer_id' => $request->get('customer_id'),
                'cart_data' => $cart,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'total_amount' => $totals['total'],
                'items_count' => $itemsCount,
                'status' => 'held'
            ]);

            // Clear active POS cart after holding ticket
            session()->forget('pos_cart');

            $openHeldCount = HoldTicket::where('status', 'held')->count();

            return response()->json([
                'success' => true,
                'ticket_number' => $ticketNo,
                'held_count' => $openHeldCount,
                'message' => "Ticket {$ticketNo} held successfully!"
            ]);

        } catch (\Exception $e) {
            Log::error('Error holding ticket: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to hold ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHeldTickets()
    {
        try {
            $heldTickets = HoldTicket::with('customer')
                ->where('status', 'held')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'tickets' => $heldTickets,
                'count' => $heldTickets->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching held tickets: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'tickets' => [],
                'count' => 0
            ]);
        }
    }

    public function resumeTicket($id)
    {
        try {
            $ticket = HoldTicket::where('status', 'held')->findOrFail($id);

            // Put ticket cart into POS cart session
            session()->put('pos_cart', $ticket->cart_data);

            // Mark ticket as resumed
            $ticket->update(['status' => 'resumed']);

            $cart = session()->get('pos_cart', []);

            return response()->json([
                'success' => true,
                'cart' => $this->formatCart($cart),
                'totals' => $this->calculateTotals($cart),
                'customer_id' => $ticket->customer_id,
                'reference_note' => $ticket->reference_note,
                'message' => "Ticket {$ticket->ticket_number} resumed!"
            ]);

        } catch (\Exception $e) {
            Log::error('Error resuming ticket: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resume ticket'
            ], 500);
        }
    }

    public function cancelTicket($id)
    {
        try {
            $ticket = HoldTicket::findOrFail($id);
            $ticket->update(['status' => 'cancelled']);

            $openHeldCount = HoldTicket::where('status', 'held')->count();

            return response()->json([
                'success' => true,
                'held_count' => $openHeldCount,
                'message' => "Ticket {$ticket->ticket_number} cancelled"
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling ticket: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel ticket'
            ], 500);
        }
    }

    // Additional helper method to get cart count (useful for header badges)
    public function getCartCount()
    {
        $cart = session()->get('pos_cart', []);
        $count = 0;
        
        foreach($cart as $item) {
            $count += $item['quantity'];
        }
        
        return response()->json([
            'count' => $count
        ]);
    }
}