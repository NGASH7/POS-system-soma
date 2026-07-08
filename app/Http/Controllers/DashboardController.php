<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $period = $request->query('period', 'today');
            $now = Carbon::now();
            
            $dateCondition = function($query) use ($period, $now) {
                $query->where('status', 'completed');
                if ($period === 'today') {
                    $query->whereDate('created_at', $now->today());
                } elseif ($period === 'week') {
                    $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                } elseif ($period === 'month') {
                    $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                } elseif ($period === 'year') {
                    $query->whereYear('created_at', $now->year);
                }
            };

            // Sales Query
            $salesQuery = Sale::where('status', 'completed');
            $dateCondition($salesQuery);
            
            // Metrics
            $totalSales = $salesQuery->sum('total') ?? 0;
            $totalTransactions = $salesQuery->count();
            $avgSale = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
            
            // Items Sold
            $itemsSold = SaleItem::whereHas('sale', function($q) use ($period, $now) {
                $q->where('status', 'completed');
                if ($period === 'today') $q->whereDate('created_at', $now->today());
                elseif ($period === 'week') $q->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                elseif ($period === 'month') $q->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                elseif ($period === 'year') $q->whereYear('created_at', $now->year);
            })->sum('quantity') ?? 0;
            
            // Gross Profit
            $itemsWithProduct = SaleItem::with('product')
                ->whereHas('sale', function($q) use ($period, $now) {
                    $q->where('status', 'completed');
                    if ($period === 'today') $q->whereDate('created_at', $now->today());
                    elseif ($period === 'week') $q->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    elseif ($period === 'month') $q->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                    elseif ($period === 'year') $q->whereYear('created_at', $now->year);
                })
                ->get();
                
            $totalCost = $itemsWithProduct->sum(function($item) {
                return ($item->product ? $item->product->cost : 0) * $item->quantity;
            });
            $grossProfit = $totalSales - $totalCost;
            
            // Top Products
            $topProducts = SaleItem::with(['product', 'product.category'])
                ->whereHas('sale', function($q) use ($period, $now) {
                    $q->where('status', 'completed');
                    if ($period === 'today') $q->whereDate('created_at', $now->today());
                    elseif ($period === 'week') $q->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    elseif ($period === 'month') $q->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                    elseif ($period === 'year') $q->whereYear('created_at', $now->year);
                })
                ->selectRaw('product_id, sum(quantity) as total_qty, sum(total) as total_sales')
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();
            
            // Recent Sales
            $recentSales = Sale::with('customer')
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Low Stock Products
            $lowStockProducts = Product::where('is_active', 1)
                ->where('stock_quantity', '<=', 10)
                ->orderBy('stock_quantity', 'asc')
                ->limit(5)
                ->get();

            // Sales Chart Data
            $salesChartData = $this->getSalesChartData($period, $now);
            
            // Payment Chart Data
            $paymentChartData = $this->getPaymentChartData($period, $now);

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
                'paymentChartData'
            ));
            
        } catch (\Exception $e) {
            return "Dashboard Error: " . $e->getMessage() . "<br>File: " . $e->getFile() . "<br>Line: " . $e->getLine();
        }
    }
    
    /**
     * Get sales chart data based on period
     */
    private function getSalesChartData($period, $now)
    {
        $query = Sale::where('status', 'completed');
        
        if ($period === 'today') {
            $query->whereDate('created_at', $now->today());
            $salesData = $query->selectRaw('HOUR(created_at) as label, sum(total) as total')
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
        
        if ($period !== 'all') {
            if ($period === 'week') {
                $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            } elseif ($period === 'month') {
                $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
            } elseif ($period === 'year') {
                $query->whereYear('created_at', $now->year);
            }
        }
        
        $format = $period === 'year' ? '%Y-%m' : '%Y-%m-%d';
        $salesData = $query->selectRaw('DATE_FORMAT(created_at, "' . $format . '") as label, sum(total) as total')
            ->groupBy('label')->orderBy('label')->get();
            
        return [
            'labels' => $salesData->pluck('label')->toArray(),
            'data' => $salesData->pluck('total')->toArray()
        ];
    }
    
    /**
     * Get payment chart data
     */
    private function getPaymentChartData($period, $now)
    {
        $query = Sale::where('status', 'completed');
        
        if ($period === 'today') {
            $query->whereDate('created_at', $now->today());
        } elseif ($period === 'week') {
            $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', $now->year);
        }
        
        $paymentData = $query->selectRaw('payment_method, sum(total) as total')
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

    // ====== Report Methods ======
    
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
        
        $sales = Sale::with('user', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $summary = [
            'total_sales' => $sales->sum('total'),
            'total_transactions' => $sales->count(),
            'average_sale' => $sales->count() > 0 ? $sales->sum('total') / $sales->count() : 0,
            'cash_sales' => $sales->where('payment_method', 'cash')->sum('total'),
            'card_sales' => $sales->where('payment_method', 'card')->sum('total'),
            'mobile_money_sales' => $sales->where('payment_method', 'mobile_money')->sum('total'),
        ];
        
        return view('reports.sales', compact('sales', 'summary', 'startDate', 'endDate'));
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