<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ReturnModel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $period = $request->query('period', 'today');
            $now = Carbon::now();
            
            // Helper function to get date conditions
            $getDateCondition = function($period, $now) {
                $condition = [];
                if ($period === 'today') {
                    $condition['start'] = $now->copy()->startOfDay();
                    $condition['end'] = $now->copy()->endOfDay();
                } elseif ($period === 'week') {
                    $condition['start'] = $now->copy()->startOfWeek();
                    $condition['end'] = $now->copy()->endOfWeek();
                } elseif ($period === 'month') {
                    $condition['start'] = $now->copy()->startOfMonth();
                    $condition['end'] = $now->copy()->endOfMonth();
                } elseif ($period === 'year') {
                    $condition['start'] = $now->copy()->startOfYear();
                    $condition['end'] = $now->copy()->endOfYear();
                } else {
                    $condition['start'] = $now->copy()->subYears(5);
                    $condition['end'] = $now->copy();
                }
                return $condition;
            };

            $dateRange = $getDateCondition($period, $now);
            
            // ========== SALES (NET OF RETURNS) ==========
            $salesQuery = Sale::where('status', 'completed')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            
            $totalSales = $salesQuery->sum('paid') ?? 0;
            $totalTransactions = $salesQuery->count();
            $avgSale = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
            
            // ========== ITEMS SOLD ==========
            $itemsSold = SaleItem::whereHas('sale', function($q) use ($dateRange) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })->sum('quantity') ?? 0;
            
            // ========== GROSS PROFIT ==========
            $itemsWithProduct = SaleItem::with('product')
                ->whereHas('sale', function($q) use ($dateRange) {
                    $q->where('status', 'completed')
                      ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->get();
                
            $totalCost = $itemsWithProduct->sum(function($item) {
                return ($item->product ? $item->product->cost : 0) * $item->quantity;
            });
            $grossProfit = $totalSales - $totalCost;
            
            // ========== TOP PRODUCTS ==========
            $topProducts = SaleItem::with(['product', 'product.category'])
                ->whereHas('sale', function($q) use ($dateRange) {
                    $q->where('status', 'completed')
                      ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->selectRaw('product_id, sum(quantity) as total_qty, sum(total) as total_sales')
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();
            
            // ========== RECENT SALES ==========
            $recentSales = Sale::with('customer')
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // ========== LOW STOCK PRODUCTS ==========
            $lowStockProducts = Product::where('is_active', 1)
                ->where('stock_quantity', '<=', 10)
                ->orderBy('stock_quantity', 'asc')
                ->limit(5)
                ->get();

            // ========== SALES CHART (EXCLUDING RETURNS) ==========
            $salesChartData = $this->getSalesChartData($dateRange, $period);
            
            // ========== PAYMENT CHART (EXCLUDING RETURNS) ==========
            $paymentChartData = $this->getPaymentChartData($dateRange);

            // ========== RETURNS SUMMARY ==========
            $returnsQuery = ReturnModel::whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            
            $totalReturns = $returnsQuery->sum('refund_amount') ?? 0;
            $returnCount = $returnsQuery->count();
            
            // ========== NET SALES ==========
            $netSales = $totalSales - $totalReturns;

            return view('dashboard', compact(
                'totalSales',
                'totalTransactions',
                'avgSale',
                'itemsSold',
                'grossProfit',
                'topProducts',
                'recentSales',
                'lowStockProducts',
                'period',
                'salesChartData',
                'paymentChartData',
                'totalReturns',
                'returnCount',
                'netSales'
            ));
            
        } catch (\Exception $e) {
            return "Dashboard Error: " . $e->getMessage() . "<br>File: " . $e->getFile() . "<br>Line: " . $e->getLine();
        }
    }
    
    /**
     * Get sales chart data
     */
    private function getSalesChartData($dateRange, $period)
    {
        $query = Sale::where('status', 'completed')
                     ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        
        if ($period === 'today') {
            $salesData = $query->selectRaw('HOUR(created_at) as label, sum(paid) as total')
                ->groupBy('label')->orderBy('label')->get();
                
            $labels = [];
            $data = [];
            for ($i = 0; $i < 24; $i++) {
                $match = $salesData->firstWhere('label', $i);
                $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $data[] = $match ? $match->total : 0;
            }
            return ['labels' => $labels, 'data' => $data];
        }
        
        $format = $period === 'year' ? '%Y-%m' : '%Y-%m-%d';
        $salesData = $query->selectRaw('DATE_FORMAT(created_at, "' . $format . '") as label, sum(paid) as total')
            ->groupBy('label')->orderBy('label')->get();
            
        return [
            'labels' => $salesData->pluck('label')->toArray(),
            'data' => $salesData->pluck('total')->toArray()
        ];
    }
    
    /**
     * Get payment chart data (EXCLUDING RETURNS)
     */
    private function getPaymentChartData($dateRange)
    {
        $paymentData = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('payment_method, sum(paid) as total')
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();
            
        $labels = $paymentData->pluck('payment_method')->map(function($method) {
            $method = ucfirst(str_replace('_', ' ', $method));
            return $method === 'Mobile money' ? 'Mobile Money' : $method;
        })->toArray();
            
        return [
            'labels' => $labels,
            'data' => $paymentData->pluck('total')->toArray()
        ];
    }

    // ====== REPORT METHODS ======
    
    /**
     * Sales Report
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // ========== REGULAR SALES ==========
        $salesQuery = Sale::with('user', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed');
            
        $totalSales = clone $salesQuery;
        $totalSales = $totalSales->sum('paid');
        $totalTransactions = clone $salesQuery;
        $totalTransactions = $totalTransactions->count();

        $sales = $salesQuery->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // ========== RETURNS ==========
        $returns = ReturnModel::with('user', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalReturns = $returns->sum('refund_amount');
        $returnCount = $returns->count();
        
        // ========== SUMMARY ==========
        // Note: sum('paid') naturally includes negative amounts from return sales.
        $summary = [
            'total_sales' => $totalSales,
            'total_transactions' => $totalTransactions,
            'average_sale' => $totalTransactions > 0 ? $totalSales / $totalTransactions : 0,
            'cash_sales' => Sale::whereBetween('created_at', [$startDate, $endDate->endOfDay()])->where('status', 'completed')->where('payment_method', 'cash')->sum('paid'),
            'card_sales' => Sale::whereBetween('created_at', [$startDate, $endDate->endOfDay()])->where('status', 'completed')->where('payment_method', 'card')->sum('paid'),
            'mobile_money_sales' => Sale::whereBetween('created_at', [$startDate, $endDate->endOfDay()])->where('status', 'completed')->where('payment_method', 'mobile_money')->sum('paid'),
            'credit_sales' => Sale::whereBetween('created_at', [$startDate, $endDate->endOfDay()])->where('status', 'completed')->where('payment_method', 'credit')->sum('paid'),
            'total_returns' => $totalReturns,
            'return_count' => $returnCount,
            'net_sales' => $totalSales
        ];
        
        return view('reports.sales', compact('sales', 'returns', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Inventory Report
     */
    public function inventoryReport(Request $request)
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(20);
        
        $totalValue = $products->sum(function($product) {
            return $product->stock_quantity * $product->cost;
        });
        
        $totalRetailValue = $products->sum(function($product) {
            return $product->stock_quantity * $product->price;
        });
        
        return view('reports.inventory', compact('products', 'totalValue', 'totalRetailValue'));
    }

    /**
     * Low Stock Alert (AJAX)
     */
    public function lowStockAlert()
    {
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->get();
        
        return response()->json([
            'count' => $lowStockProducts->count(),
            'products' => $lowStockProducts
        ]);
    }
}