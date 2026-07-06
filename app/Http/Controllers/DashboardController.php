<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $period = $request->query('period', 'today');
            $now = Carbon::now();
            
            $query = Sale::where('status', 'completed');
            $itemQuery = \App\Models\SaleItem::whereHas('sale', function($q) use ($period, $now) {
                $q->where('status', 'completed');
                if ($period === 'today') $q->whereDate('created_at', $now->today());
                elseif ($period === 'week') $q->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                elseif ($period === 'month') $q->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                elseif ($period === 'year') $q->whereYear('created_at', $now->year);
            });

            if ($period === 'today') $query->whereDate('created_at', $now->today());
            elseif ($period === 'week') $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            elseif ($period === 'month') $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
            elseif ($period === 'year') $query->whereYear('created_at', $now->year);

            // Metrics
            $totalSales = (clone $query)->sum('total') ?? 0;
            $totalTransactions = (clone $query)->count();
            $avgSale = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
            $itemsSold = (clone $itemQuery)->sum('quantity') ?? 0;
            
            // Gross Profit
            $itemsWithProduct = (clone $itemQuery)->with('product')->get();
            $totalCost = $itemsWithProduct->sum(function($item) {
                return ($item->product ? $item->product->cost : 0) * $item->quantity;
            });
            $grossProfit = $totalSales - $totalCost;
            
            // Top Products
            $topProducts = (clone $itemQuery)->with(['product', 'product.category'])
                ->selectRaw('product_id, sum(quantity) as total_qty, sum(total) as total_sales')
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();
            
            // Recent Sales
            $recentSales = (clone $query)->with('customer')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Low Stock Products
            $lowStockProducts = \App\Models\Product::where('is_active', 1)
                ->where('stock_quantity', '<=', 10)
                ->orderBy('stock_quantity', 'asc')
                ->limit(5)
                ->get();

            // Chart Data: Sales Overview
            $salesChartData = [];
            if ($period === 'today') {
                $salesData = (clone $query)->selectRaw('HOUR(created_at) as label, sum(total) as total')
                    ->groupBy('label')->orderBy('label')->get();
                for ($i = 0; $i < 24; $i++) {
                    $match = $salesData->firstWhere('label', $i);
                    $salesChartData['labels'][] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                    $salesChartData['data'][] = $match ? $match->total : 0;
                }
            } else {
                $format = $period === 'year' || $period === 'all' ? 'Y-m' : 'Y-m-d';
                $salesData = (clone $query)->selectRaw('DATE_FORMAT(created_at, "' . ($format === 'Y-m' ? '%Y-%m' : '%Y-%m-%d') . '") as label, sum(total) as total')
                    ->groupBy('label')->orderBy('label')->get();
                foreach ($salesData as $row) {
                    $salesChartData['labels'][] = $row->label;
                    $salesChartData['data'][] = $row->total;
                }
                if (empty($salesChartData['labels'])) {
                    $salesChartData = ['labels' => [Carbon::today()->format($format)], 'data' => [0]];
                }
            }

            // Chart Data: Payment Methods
            $paymentData = (clone $query)->selectRaw('payment_method, sum(total) as total')
                ->whereNotNull('payment_method')
                ->groupBy('payment_method')
                ->get();
            $paymentChartData = [
                'labels' => $paymentData->pluck('payment_method')->map(fn($v) => ucfirst($v))->toArray(),
                'data' => $paymentData->pluck('total')->toArray()
            ];

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
}
