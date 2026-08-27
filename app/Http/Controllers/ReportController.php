<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Exports\ProfitLossExport;
use App\Exports\TaxReportExport;
use App\Exports\ProductSellExport;
use App\Exports\SellPaymentExport;
use App\Exports\PurchaseSaleExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Profit & Loss Report
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        // Get sales data
        $sales = Sale::with('items.product')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed')
            ->get();
        
        $totalRevenue = $sales->sum('paid');
        $totalCost = 0;
        
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalCost += (($item->product?->cost ?? 0) * $item->quantity);
            }
        }
        
        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        
        // Daily breakdown
        $dailyData = [];
        for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 day')) {
            $daySales = $sales->where('created_at', '>=', $date->copy()->startOfDay())
                ->where('created_at', '<=', $date->copy()->endOfDay());
            
            $dailyRevenue = $daySales->sum('paid');
            $dailyCost = 0;
            
            foreach ($daySales as $sale) {
                foreach ($sale->items as $item) {
                    $dailyCost += (($item->product?->cost ?? 0) * $item->quantity);
                }
            }
            
            $dailyData[] = [
                'date' => $date->copy()->format('Y-m-d'),
                'revenue' => $dailyRevenue,
                'cost' => $dailyCost,
                'profit' => $dailyRevenue - $dailyCost
            ];
        }
        
        return view('reports.profit-loss', compact(
            'startDate', 'endDate', 'totalRevenue', 'totalCost', 
            'grossProfit', 'profitMargin', 'dailyData'
        ));
    }
    
    /**
     * Tax Report
     */
    public function taxReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed')
            ->get();
        
        $totalSales = $sales->sum('total');
        $totalTax = $sales->sum('tax');
        $totalTaxableSales = $sales->sum('subtotal');
        
        // Monthly breakdown
        $monthlyData = Sale::whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(subtotal) as taxable_amount'),
                DB::raw('SUM(tax) as tax_amount'),
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('reports.tax', compact(
            'startDate', 'endDate', 'totalSales', 'totalTax', 
            'totalTaxableSales', 'monthlyData'
        ));
    }
    
    /**
     * Employee Performance Report
     */
    public function employeePerformance(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        $employees = User::with(['sales' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
                ->where('status', 'completed');
        }])->get();
        
        $performanceData = [];
        foreach ($employees as $employee) {
            $sales = $employee->sales;
            $totalSales = $sales->sum('paid');
            $transactionCount = $sales->count();
            $averageSale = $transactionCount > 0 ? $totalSales / $transactionCount : 0;
            
            $performanceData[] = [
                'employee' => $employee,
                'total_sales' => $totalSales,
                'transaction_count' => $transactionCount,
                'average_sale' => $averageSale,
                'sales' => $sales
            ];
        }
        
        // Sort by total sales
        usort($performanceData, function($a, $b) {
            return $b['total_sales'] <=> $a['total_sales'];
        });
        
        return view('reports.employee-performance', compact(
            'startDate', 'endDate', 'performanceData'
        ));
    }
    
    /**
     * M-Pesa Transactions Report
     */
    public function mpesaTransactions(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        $status = $request->get('status', 'all');
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $query = Transaction::where('provider', 'mpesa')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()]);
            
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate summaries
        $summaryQuery = Transaction::where('provider', 'mpesa')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()]);
            
        $totalTransactions = (clone $summaryQuery)->count();
        $completedTransactions = clone $summaryQuery;
        $completedTransactions = $completedTransactions->where('status', 'completed');
        
        $totalVolume = $completedTransactions->sum('amount');
        $completedCount = $completedTransactions->count();
        
        $successRate = $totalTransactions > 0 ? round(($completedCount / $totalTransactions) * 100, 1) : 0;

        $summary = [
            'total_volume' => $totalVolume,
            'total_transactions' => $totalTransactions,
            'success_rate' => $successRate,
        ];

        return view('reports.mpesa-transactions', compact('transactions', 'startDate', 'endDate', 'status', 'summary'));
    }

    /**
     * Product Sell Report
     */
    public function productSell(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        $categoryId = $request->get('category_id');
        $sortBy = $request->get('sort_by', 'quantity');

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Get products with their sales data
        $productsQuery = Product::with(['category'])
            ->whereHas('saleItems', function($query) use ($startDate, $endDate) {
                $query->whereHas('sale', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
                      ->where('status', 'completed');
                });
            });

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        // Get products with aggregated data
        $products = $productsQuery->get()->map(function($product) use ($startDate, $endDate) {
            $saleItems = $product->saleItems()
                ->whereHas('sale', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
                          ->where('status', 'completed');
                })
                ->get();

            $totalQuantity = $saleItems->sum('quantity');
            $totalRevenue = $saleItems->sum(function($item) {
                return $item->price * $item->quantity;
            });
            $totalCost = $saleItems->sum(function($item) {
                return ($item->product->cost ?? 0) * $item->quantity;
            });
            $totalProfit = $totalRevenue - $totalCost;

            return (object) [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'category' => $product->category,
                'total_quantity' => $totalQuantity,
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
            ];
        })->filter(function($product) {
            return $product->total_quantity > 0;
        });

        // Sort products
        switch ($sortBy) {
            case 'revenue':
                $products = $products->sortByDesc('total_revenue');
                break;
            case 'profit':
                $products = $products->sortByDesc('total_profit');
                break;
            case 'name':
                $products = $products->sortBy('name');
                break;
            default: // quantity
                $products = $products->sortByDesc('total_quantity');
        }

        // Summary
        $summary = [
            'total_products' => $products->count(),
            'total_units' => $products->sum('total_quantity'),
            'total_revenue' => $products->sum('total_revenue'),
            'total_profit' => $products->sum('total_profit'),
        ];

        // Get categories for filter
        $categories = Category::all();

        // Paginate manually
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $paginatedProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($currentPage, $perPage),
            $products->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('reports.product-sell', compact(
            'products', 'paginatedProducts', 'summary', 
            'categories', 'startDate', 'endDate', 'sortBy'
        ));
    }

    /**
     * Sell Payment Report
     */
    public function sellPayment(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        $paymentMethod = $request->get('payment_method');

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Get sales with payment data
        $salesQuery = Sale::with(['user', 'customer'])
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed');

        if ($paymentMethod) {
            $salesQuery->where('payment_method', $paymentMethod);
        }

        $sales = $salesQuery->get();

        // Calculate payment method breakdown
        $paymentBreakdown = collect(['cash', 'card', 'mobile_money', 'credit'])->mapWithKeys(function($method) use ($sales) {
            $methodSales = $sales->where('payment_method', $method);
            return [
                $method => [
                    'method' => $method,
                    'count' => $methodSales->count(),
                    'total_amount' => $methodSales->sum('paid'),
                    'average_amount' => $methodSales->avg('paid'),
                ]
            ];
        });

        // Daily breakdown
        $dailyData = [];
        for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 day')) {
            $daySales = $sales->where('created_at', '>=', $date->copy()->startOfDay())
                ->where('created_at', '<=', $date->copy()->endOfDay());

            $dailyData[] = [
                'date' => $date->copy()->format('Y-m-d'),
                'transactions' => $daySales->count(),
                'cash' => $daySales->where('payment_method', 'cash')->sum('paid'),
                'card' => $daySales->where('payment_method', 'card')->sum('paid'),
                'mobile_money' => $daySales->where('payment_method', 'mobile_money')->sum('paid'),
                'credit' => $daySales->where('payment_method', 'credit')->sum('paid'),
                'total' => $daySales->sum('paid'),
            ];
        }

        // Summary
        $summary = [
            'total_transactions' => $sales->count(),
            'total_amount' => $sales->sum('paid'),
            'average_amount' => $sales->avg('paid'),
            'cash_amount' => $sales->where('payment_method', 'cash')->sum('paid'),
            'card_amount' => $sales->where('payment_method', 'card')->sum('paid'),
            'mobile_money_amount' => $sales->where('payment_method', 'mobile_money')->sum('paid'),
            'credit_amount' => $sales->where('payment_method', 'credit')->sum('paid'),
        ];

        // Get recent transactions for table
        $recentSales = $sales->sortByDesc('created_at')->take(50);

        return view('reports.sell-payment', compact(
            'sales', 'recentSales', 'paymentBreakdown', 
            'dailyData', 'summary', 'startDate', 'endDate', 'paymentMethod'
        ));
    }

    /**
     * Purchase and Sale Report
     */
    public function purchaseSale(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        $reportType = $request->get('report_type', 'daily');

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Get all sales
        $sales = Sale::with(['items.product', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed')
            ->get();

        // Get purchase data - try different column names
        $purchases = $this->getPurchaseData($startDate, $endDate);

        $totalPurchases = $purchases->sum('total_amount');
        $totalSales = $sales->sum('paid');
        
        // Calculate cost of goods sold
        $totalCostOfGoods = $this->calculateCostOfGoods($purchases);
        $grossProfit = $totalSales - $totalCostOfGoods;

        // Prepare consolidated data based on report type
        $data = $this->prepareConsolidatedData($sales, $purchases, $startDate, $endDate, $reportType);

        // Summary cards
        $summary = [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'total_cost_of_goods' => $totalCostOfGoods,
            'gross_profit' => $grossProfit,
            'profit_margin' => $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0,
            'total_invoices' => $sales->count(),
            'total_units_sold' => $sales->sum(function($sale) {
                return $sale->items->sum('quantity');
            }),
        ];

        return view('reports.purchase-sale', compact(
            'data', 'summary', 'startDate', 'endDate', 'reportType'
        ));
    }

    /**
     * Get purchase data with flexible column names
     */
    private function getPurchaseData($startDate, $endDate)
    {
        // First, check what columns exist in purchase_items table
        try {
            $columns = DB::getSchemaBuilder()->getColumnListing('purchase_items');
            
            // Determine which cost column exists
            $costColumn = null;
            $possibleColumns = ['cost_price', 'price', 'cost', 'unit_cost', 'purchase_price', 'buying_price'];
            
            foreach ($possibleColumns as $column) {
                if (in_array($column, $columns)) {
                    $costColumn = $column;
                    break;
                }
            }

            if ($costColumn) {
                // Use the found column
                return DB::table('purchase_items')
                    ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                    ->whereBetween('purchases.created_at', [$startDate, $endDate->endOfDay()])
                    ->where('purchases.status', 'completed')
                    ->select(
                        'purchases.created_at',
                        'purchase_items.product_id',
                        'purchase_items.quantity',
                        DB::raw("purchase_items.{$costColumn} as cost_price"),
                        'purchases.total_amount'
                    )
                    ->get();
            }
        } catch (\Exception $e) {
            // If there's an error, fall back to getting data without cost
        }

        // Fallback: Get purchases without the cost column
        try {
            $purchases = DB::table('purchase_items')
                ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->whereBetween('purchases.created_at', [$startDate, $endDate->endOfDay()])
                ->where('purchases.status', 'completed')
                ->select(
                    'purchases.created_at',
                    'purchase_items.product_id',
                    'purchase_items.quantity',
                    'purchases.total_amount'
                )
                ->get();

            // Add cost price from products table
            return $purchases->map(function($item) {
                $product = Product::find($item->product_id);
                $item->cost_price = $product ? $product->cost : 0;
                return $item;
            });
        } catch (\Exception $e) {
            // Ultimate fallback: return empty collection with default values
            return collect([]);
        }
    }

    /**
     * Calculate cost of goods sold from purchases
     */
    private function calculateCostOfGoods($purchases)
    {
        $totalCost = 0;
        
        foreach ($purchases as $purchase) {
            $costPrice = isset($purchase->cost_price) ? $purchase->cost_price : 0;
            $totalCost += $costPrice * $purchase->quantity;
        }
        
        return $totalCost;
    }

    /**
     * Prepare consolidated data for purchase and sale report
     */
    private function prepareConsolidatedData($sales, $purchases, $startDate, $endDate, $reportType)
    {
        $data = [];
        
        if ($reportType == 'daily') {
            for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 day')) {
                $daySales = $sales->where('created_at', '>=', $date->copy()->startOfDay())
                    ->where('created_at', '<=', $date->copy()->endOfDay());
                
                $dayPurchases = $purchases->where('created_at', '>=', $date->copy()->startOfDay())
                    ->where('created_at', '<=', $date->copy()->endOfDay());

                $dayCost = $this->calculateCostOfGoods($dayPurchases);

                $data[] = [
                    'period' => $date->copy()->format('Y-m-d'),
                    'sales_count' => $daySales->count(),
                    'sales_amount' => $daySales->sum('paid'),
                    'purchases_count' => $dayPurchases->count(),
                    'purchases_amount' => $dayPurchases->sum('total_amount'),
                    'cost_of_goods' => $dayCost,
                    'profit' => $daySales->sum('paid') - $dayCost,
                ];
            }
        } elseif ($reportType == 'weekly') {
            $current = clone $startDate;
            while ($current <= $endDate) {
                $weekEnd = (clone $current)->endOfWeek();
                if ($weekEnd > $endDate) {
                    $weekEnd = clone $endDate;
                }

                $weekSales = $sales->where('created_at', '>=', $current->startOfDay())
                    ->where('created_at', '<=', $weekEnd->endOfDay());
                
                $weekPurchases = $purchases->where('created_at', '>=', $current->startOfDay())
                    ->where('created_at', '<=', $weekEnd->endOfDay());

                $weekCost = $this->calculateCostOfGoods($weekPurchases);

                $data[] = [
                    'period' => $current->format('M d') . ' - ' . $weekEnd->format('M d, Y'),
                    'sales_count' => $weekSales->count(),
                    'sales_amount' => $weekSales->sum('paid'),
                    'purchases_count' => $weekPurchases->count(),
                    'purchases_amount' => $weekPurchases->sum('total_amount'),
                    'cost_of_goods' => $weekCost,
                    'profit' => $weekSales->sum('paid') - $weekCost,
                ];

                $current = (clone $weekEnd)->modify('+1 day');
            }
        } else { // monthly
            $current = clone $startDate;
            while ($current <= $endDate) {
                $monthEnd = (clone $current)->endOfMonth();
                if ($monthEnd > $endDate) {
                    $monthEnd = clone $endDate;
                }

                $monthSales = $sales->where('created_at', '>=', $current->startOfDay())
                    ->where('created_at', '<=', $monthEnd->endOfDay());
                
                $monthPurchases = $purchases->where('created_at', '>=', $current->startOfDay())
                    ->where('created_at', '<=', $monthEnd->endOfDay());

                $monthCost = $this->calculateCostOfGoods($monthPurchases);

                $data[] = [
                    'period' => $current->format('F Y'),
                    'sales_count' => $monthSales->count(),
                    'sales_amount' => $monthSales->sum('paid'),
                    'purchases_count' => $monthPurchases->count(),
                    'purchases_amount' => $monthPurchases->sum('total_amount'),
                    'cost_of_goods' => $monthCost,
                    'profit' => $monthSales->sum('paid') - $monthCost,
                ];

                $current = (clone $monthEnd)->modify('+1 day');
            }
        }

        return $data;
    }

    /**
     * Export methods
     */
    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        return Excel::download(new SalesExport($startDate, $endDate), 'sales-report.xlsx');
    }
    
    public function exportProfitLoss(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        return Excel::download(new ProfitLossExport($startDate, $endDate), 'profit-loss.xlsx');
    }
    
    public function exportTaxReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        return Excel::download(new TaxReportExport($startDate, $endDate), 'tax-report.xlsx');
    }

    public function exportProductSell(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        return Excel::download(new ProductSellExport($startDate, $endDate), 'product-sell-report.xlsx');
    }

    public function exportSellPayment(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        $paymentMethod = $request->get('payment_method');
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        return Excel::download(new SellPaymentExport($startDate, $endDate, $paymentMethod), 'sell-payment-report.xlsx');
    }

    public function exportPurchaseSale(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        $reportType = $request->get('report_type', 'daily');
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        return Excel::download(new PurchaseSaleExport($startDate, $endDate, $reportType), 'purchase-sale-report.xlsx');
    }
    
    public function exportPdf($reportType, Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        switch ($reportType) {
            case 'sales':
                $data = $this->getSalesData($startDate, $endDate);
                $pdf = Pdf::loadView('exports.sales-pdf', $data);
                break;
            case 'profit-loss':
                $data = $this->getProfitLossData($startDate, $endDate);
                $pdf = Pdf::loadView('exports.profit-loss-pdf', $data);
                break;
            default:
                abort(404);
        }
        
        return $pdf->download("{$reportType}-report.pdf");
    }
    
    private function getSalesData($startDate, $endDate)
    {
        $sales = Sale::with('user', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed')
            ->get();
        
        return compact('sales', 'startDate', 'endDate');
    }
    
    private function getProfitLossData($startDate, $endDate)
    {
        $sales = Sale::with('items.product')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed')
            ->get();
        
        $totalRevenue = $sales->sum('total');
        $totalCost = 0;
        
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalCost += (($item->product?->cost ?? 0) * $item->quantity);
            }
        }
        
        return compact('totalRevenue', 'totalCost', 'startDate', 'endDate');
    }
}