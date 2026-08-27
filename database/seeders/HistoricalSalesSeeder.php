<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\ReturnModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HistoricalSalesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧹 Cleaning up existing data...');
        
        // Disable foreign key checks to truncate tables safely
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Sale::truncate();
        SaleItem::truncate();
        Transaction::truncate();
        ReturnModel::truncate();
        Customer::truncate();
        \App\Models\CustomerCredit::truncate();
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ensure outlets exist
        $outlets = Outlet::all();
        if ($outlets->isEmpty()) {
            $this->command->info('🏪 Seeding outlets first...');
            $this->call(OutletSeeder::class);
            $outlets = Outlet::all();
        }

        // Ensure users exist
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('👤 Seeding users first...');
            $this->call(UserSeeder::class);
            $users = User::all();
        }

        $this->command->info('👥 Seeding customers for each outlet...');
        $customersByOutlet = [];
        $customersTemplates = [
            ['name' => 'John Doe', 'email_prefix' => 'john.doe'],
            ['name' => 'Jane Smith', 'email_prefix' => 'jane.smith'],
            ['name' => 'Alice Johnson', 'email_prefix' => 'alice.j'],
            ['name' => 'Bob Mwangi', 'email_prefix' => 'bob.m'],
            ['name' => 'Clara Chebet', 'email_prefix' => 'clara.c'],
        ];

        foreach ($outlets as $outlet) {
            $customersByOutlet[$outlet->id] = [];
            foreach ($customersTemplates as $idx => $tpl) {
                $email = $tpl['email_prefix'] . '.' . $outlet->id . '@example.com';
                $phone = '07' . str_pad($outlet->id, 2, '0', STR_PAD_LEFT) . str_pad($idx, 6, '0', STR_PAD_LEFT);
                
                $customersByOutlet[$outlet->id][] = Customer::create([
                    'name' => $tpl['name'] . ' (' . $outlet->name . ')',
                    'email' => $email,
                    'phone' => $phone,
                    'outlet_id' => $outlet->id,
                    'credit_limit' => 50000,
                    'available_credit' => 50000,
                ]);
            }
        }

        $this->command->info('📦 Seeding products for each outlet...');
        $productsSeedList = [
            ['name' => 'Wireless Headphones', 'sku' => 'WH-001', 'price' => 8000, 'cost' => 4500, 'stock_quantity' => 100, 'category' => 'Electronics'],
            ['name' => 'Running Shoes', 'sku' => 'RS-002', 'price' => 4500, 'cost' => 2500, 'stock_quantity' => 100, 'category' => 'Footwear'],
            ['name' => 'Smart Watch', 'sku' => 'SW-003', 'price' => 12000, 'cost' => 7000, 'stock_quantity' => 100, 'category' => 'Electronics'],
            ['name' => 'T-Shirt (Black)', 'sku' => 'TS-004', 'price' => 1500, 'cost' => 800, 'stock_quantity' => 200, 'category' => 'Clothing'],
            ['name' => 'Jeans (Blue)', 'sku' => 'JN-005', 'price' => 3500, 'cost' => 1800, 'stock_quantity' => 150, 'category' => 'Clothing'],
            ['name' => 'Wireless Mouse', 'sku' => 'WM-006', 'price' => 1800, 'cost' => 900, 'stock_quantity' => 100, 'category' => 'Electronics'],
            ['name' => 'Bluetooth Speaker', 'sku' => 'BS-007', 'price' => 3000, 'cost' => 1600, 'stock_quantity' => 80, 'category' => 'Electronics'],
            ['name' => 'Phone Charger', 'sku' => 'PC-008', 'price' => 1000, 'cost' => 450, 'stock_quantity' => 300, 'category' => 'Accessories'],
            ['name' => 'Backpack', 'sku' => 'BP-009', 'price' => 2800, 'cost' => 1400, 'stock_quantity' => 120, 'category' => 'Bags'],
        ];

        foreach ($outlets as $outlet) {
            foreach ($productsSeedList as $prod) {
                Product::create(array_merge($prod, [
                    'outlet_id' => $outlet->id
                ]));
            }
        }

        // Fetch products and group by outlet_id
        $productsByOutlet = Product::all()->groupBy('outlet_id');

        $this->command->info('📈 Generating sales from April 1st, 2026 to August 18th, 2026...');
        
        $startDate = Carbon::parse('2026-04-01');
        $endDate = Carbon::parse('2026-08-18');
        
        $totalSalesCreated = 0;
        $totalItemsCreated = 0;
        $totalReturnsCreated = 0;

        $currentDate = $startDate->copy();
        while ($currentDate->lessThanOrEqualTo($endDate)) {
            $salesCount = rand(2, 6);
            
            for ($i = 1; $i <= $salesCount; $i++) {
                $user = $users->random();
                $outlet = $outlets->random();
                
                // Get products and customers for this outlet
                $outletProducts = $productsByOutlet->get($outlet->id);
                $outletCustomers = $customersByOutlet[$outlet->id];
                
                if (!$outletProducts || $outletProducts->isEmpty()) {
                    continue;
                }

                // 30% chance of walk-in customer (null), otherwise random customer from this outlet
                $customer = (rand(1, 10) <= 3) ? null : $outletCustomers[array_rand($outletCustomers)];
                
                // Select random items for this sale (1 to 4 products from this outlet)
                $itemCount = rand(1, 4);
                $saleProducts = $outletProducts->random(min($itemCount, $outletProducts->count()));
                
                $saleDate = $currentDate->copy()
                    ->hour(rand(8, 20))
                    ->minute(rand(0, 59))
                    ->second(rand(0, 59));

                $grossTotal = 0;
                $itemsToCreate = [];
                
                foreach ($saleProducts as $product) {
                    $qty = rand(1, 3);
                    $price = (float)$product->price;
                    $itemTotal = $price * $qty;
                    $grossTotal += $itemTotal;
                    
                    $itemsToCreate[] = [
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => $price,
                        'discount' => 0,
                        'total' => $itemTotal
                    ];
                }
                
                $discount = (rand(1, 20) === 1) ? (float)rand(50, 200) : 0.0;
                $total = max(0, $grossTotal - $discount);

                // 16% VAT is inclusive in product prices
                $subtotal = $total > 0 ? round($total / 1.16, 2) : 0.0;
                $tax = $total > 0 ? round($total - $subtotal, 2) : 0.0;
                
                // Determine payment method
                $randPay = rand(1, 100);
                if ($randPay <= 50) {
                    $paymentMethod = 'cash';
                    $paid = ceil($total / 50) * 50;
                    $changeDue = $paid - $total;
                } elseif ($randPay <= 80) {
                    $paymentMethod = 'mobile_money';
                    $paid = $total;
                    $changeDue = 0;
                } elseif ($randPay <= 95) {
                    $paymentMethod = 'card';
                    $paid = $total;
                    $changeDue = 0;
                } else {
                    if ($customer) {
                        $paymentMethod = 'credit';
                        $paid = 0;
                        $changeDue = 0;
                    } else {
                        $paymentMethod = 'cash';
                        $paid = $total;
                        $changeDue = 0;
                    }
                }
                
                $invoiceNo = 'SOMA-' . strtoupper(substr($outlet->name, 0, 3)) . '-' . $saleDate->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                
                $mpesaReceipt = null;
                if ($paymentMethod === 'mobile_money') {
                    $mpesaReceipt = 'M' . strtoupper(Str::random(9));
                }

                // Create Sale record
                $sale = Sale::create([
                    'invoice_no' => $invoiceNo,
                    'user_id' => $user->id,
                    'outlet_id' => $outlet->id,
                    'customer_id' => $customer ? $customer->id : null,
                    'terminal_id' => 'TERM-01',
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'discount_type' => $discount > 0 ? 'fixed' : null,
                    'tax' => $tax,
                    'total' => $total,
                    'paid' => $paid,
                    'change_due' => $changeDue,
                    'payment_method' => $paymentMethod,
                    'status' => 'completed',
                    'notes' => 'Mock sales seeder transaction',
                    'mpesa_receipt' => $mpesaReceipt,
                    'sale_date' => $saleDate,
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate
                ]);
                
                $totalSalesCreated++;

                // Create SaleItems
                foreach ($itemsToCreate as $itemData) {
                    $itemData['sale_id'] = $sale->id;
                    $itemData['created_at'] = $saleDate;
                    $itemData['updated_at'] = $saleDate;
                    SaleItem::create($itemData);
                    $totalItemsCreated++;
                }

                // Update customer total_spent and points
                if ($customer) {
                    $customer->increment('total_spent', $total);
                    $customer->increment('points', floor($total / 10));
                    
                    if ($paymentMethod === 'credit') {
                        $customer->increment('total_credit', $total);
                        $customer->decrement('available_credit', $total);
                        
                        // Create customer credit record
                        \App\Models\CustomerCredit::create([
                            'customer_id' => $customer->id,
                            'user_id' => $user->id,
                            'reference' => 'CR-' . $invoiceNo,
                            'type' => 'direct_credit',
                            'total_amount' => $total,
                            'paid_amount' => 0,
                            'balance' => $total,
                            'due_date' => $saleDate->copy()->addDays(30),
                            'status' => 'active',
                            'notes' => 'Generated from POS Sale ' . $invoiceNo,
                            'created_at' => $saleDate,
                            'updated_at' => $saleDate
                        ]);
                    }
                }

                // Create M-Pesa Transaction if mobile_money
                if ($paymentMethod === 'mobile_money') {
                    $reference = 'TXN-' . $saleDate->timestamp . '-' . rand(1000, 9999);
                    $checkoutId = 'ws_CO_' . $saleDate->format('dmyHis') . '_' . rand(1000, 9999);
                    
                    $cartData = [];
                    foreach ($itemsToCreate as $item) {
                        $prodModel = $outletProducts->firstWhere('id', $item['product_id']);
                        $cartData[$item['product_id']] = [
                            'id' => $item['product_id'],
                            'name' => $prodModel ? $prodModel->name : 'Unknown Product',
                            'price' => $item['price'],
                            'quantity' => $item['quantity'],
                            'sku' => $prodModel ? $prodModel->sku : 'SKU',
                        ];
                    }

                    Transaction::create([
                        'reference' => $reference,
                        'provider_reference' => $checkoutId,
                        'amount' => $total,
                        'phone' => $customer ? ($customer->phone ?: '254712345678') : '254712345678',
                        'status' => 'completed',
                        'provider' => 'mpesa',
                        'cart_data' => $cartData,
                        'payment_data' => [
                            'customer_id' => $customer ? $customer->id : null,
                            'payment_method' => 'mobile_money',
                            'mobile_provider' => 'mpesa'
                        ],
                        'user_id' => $user->id,
                        'sale_id' => $sale->id,
                        'completed_at' => $saleDate,
                        'created_at' => $saleDate,
                        'updated_at' => $saleDate
                    ]);
                }

                // Occasional Returns (2% chance)
                if (rand(1, 50) === 1 && $paymentMethod !== 'credit') {
                    $returnDate = $saleDate->copy()->addDays(rand(1, 5));
                    if ($returnDate->lessThanOrEqualTo($endDate)) {
                        $refundAmount = $total;
                        
                        $returnItems = [];
                        foreach ($itemsToCreate as $itm) {
                            $returnItems[] = [
                                'product_id' => $itm['product_id'],
                                'quantity' => $itm['quantity'],
                                'price' => $itm['price']
                            ];
                        }
                        
                        ReturnModel::create([
                            'return_no' => 'RET-' . $returnDate->format('Ymd') . '-' . strtoupper(Str::random(4)),
                            'original_sale_id' => $sale->id,
                            'user_id' => $user->id,
                            'outlet_id' => $outlet->id,
                            'customer_id' => $customer ? $customer->id : null,
                            'refund_amount' => $refundAmount,
                            'refund_method' => $paymentMethod,
                            'return_type' => 'return',
                            'reason' => 'Defective product',
                            'notes' => 'Generated automatically by seeder',
                            'status' => 'completed',
                            'items' => json_encode($returnItems),
                            'created_at' => $returnDate,
                            'updated_at' => $returnDate
                        ]);
                        
                        $sale->update(['is_return' => true]);
                        $totalReturnsCreated++;
                    }
                }
            }
            
            $currentDate->addDay();
        }

        $this->command->info("🎉 Done Seeding!");
        $this->command->info("📊 Sales Created: {$totalSalesCreated}");
        $this->command->info("🛍️ Items Created: {$totalItemsCreated}");
        $this->command->info("🔄 Returns Created: {$totalReturnsCreated}");
    }
}
