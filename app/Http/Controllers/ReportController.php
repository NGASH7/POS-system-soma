<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Exports\ProfitLossExport;
use App\Exports\TaxReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
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
        
        $totalRevenue = $sales->sum('total');
        $totalCost = 0;
        
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $totalCost += ($item->product->cost * $item->quantity);
            }
        }
        
        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        
        // Daily breakdown
        $dailyData = [];
        for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 day')) {
            $daySales = $sales->where('created_at', '>=', $date->copy()->startOfDay())
                ->where('created_at', '<=', $date->copy()->endOfDay());
            
            $dailyRevenue = $daySales->sum('total');
            $dailyCost = 0;
            
            foreach ($daySales as $sale) {
                foreach ($sale->items as $item) {
                    $dailyCost += ($item->product->cost * $item->quantity);
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
            $totalSales = $sales->sum('total');
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
    
    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        return Excel::download(new SalesExport($startDate, $endDate), 'sales-report.xlsx');
    }
    
    public function exportProfitLoss(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        return Excel::download(new ProfitLossExport($startDate, $endDate), 'profit-loss.xlsx');
    }
    
    public function exportTaxReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
        return Excel::download(new TaxReportExport($startDate, $endDate), 'tax-report.xlsx');
    }
    
    public function exportPdf($reportType, Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        
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
                $totalCost += ($item->product->cost * $item->quantity);
            }
        }
        
        return compact('totalRevenue', 'totalCost', 'startDate', 'endDate');
    }
}