<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CustomerCredit;
use App\Models\Expense;
use App\Models\ReturnModel;
use App\Models\CashDrawerSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyRegisterController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

        // Get cash drawer session
        $cashSession = CashDrawerSession::whereDate('opened_at', $date)
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        // Sales Summary
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->where('payment_method', '!=', 'return')
            ->get();

        $totalSales = $sales->sum('paid');
        $totalTransactions = $sales->count();

        // Sales by Payment Method
        $salesByPayment = $sales->groupBy('payment_method')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('paid')
                ];
            });

        // Items Sold Today
        $itemsSold = SaleItem::whereHas('sale', function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->where('payment_method', '!=', 'return');
        })->with('product')
        ->get();

        $itemsSoldSummary = $itemsSold->groupBy('product_id')
            ->map(function($group) {
                $product = $group->first()->product;
                return [
                    'product_name' => $product ? $product->name : 'Unknown',
                    'quantity' => $group->sum('quantity'),
                    'total' => $group->sum('total')
                ];
            })
            ->sortByDesc('quantity')
            ->take(20);

        // Credit Sales Today
        $creditSales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_method', 'credit')
            ->where('status', 'completed')
            ->get();

        $totalCreditSales = $creditSales->sum('total');
        $creditTransactions = $creditSales->count();

        // Credit Customers
        $creditCustomers = CustomerCredit::whereDate('created_at', $date)
            ->where('status', 'active')
            ->with('customer')
            ->get();

        // Returns Today
        $returns = ReturnModel::whereBetween('created_at', [$startDate, $endDate])
            ->with('originalSale')
            ->get();

        $totalReturns = $returns->sum('refund_amount');
        $returnCount = $returns->count();

        // Expenses Today
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $expenseCount = $expenses->count();

        // Expenses by Category
        $expensesByCategory = $expenses->groupBy('category_id')
            ->map(function($group) {
                return [
                    'category_name' => $group->first()->category->name ?? 'Uncategorized',
                    'total' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            });

        // Recent Activity (last 20 transactions)
        $recentActivity = collect();

        // Add sales to activity
        foreach ($sales->take(10) as $sale) {
            $recentActivity->push((object)[
                'type' => 'Sale',
                'invoice' => $sale->invoice_no,
                'amount' => $sale->total,
                'customer' => $sale->customer->name ?? 'Walk-in',
                'payment' => $sale->payment_method,
                'time' => $sale->created_at,
                'items' => $sale->items->count()
            ]);
        }

        // Add credit sales to activity
        foreach ($creditSales->take(5) as $sale) {
            $recentActivity->push((object)[
                'type' => 'Credit Sale',
                'invoice' => $sale->invoice_no,
                'amount' => $sale->total,
                'customer' => $sale->customer->name ?? 'Unknown',
                'payment' => 'Credit',
                'time' => $sale->created_at,
                'items' => $sale->items->count()
            ]);
        }

        // Add returns to activity
        foreach ($returns->take(5) as $return) {
            $recentActivity->push((object)[
                'type' => 'Return',
                'invoice' => $return->return_no,
                'amount' => -$return->refund_amount,
                'customer' => $return->customer->name ?? 'Unknown',
                'payment' => 'Refund',
                'time' => $return->created_at,
                'items' => count($return->items ?? [])
            ]);
        }

        // Sort activity by time (latest first)
        $recentActivity = $recentActivity->sortByDesc('time')->take(20);

        // Today's Summary
        $summary = [
            'date' => $date,
            'total_sales' => $totalSales,
            'total_transactions' => $totalTransactions,
            'total_credit_sales' => $totalCreditSales,
            'credit_transactions' => $creditTransactions,
            'total_returns' => $totalReturns,
            'return_count' => $returnCount,
            'total_expenses' => $totalExpenses,
            'expense_count' => $expenseCount,
            'net_sales' => $totalSales - $totalReturns - $totalExpenses,
            'items_sold_count' => $itemsSold->sum('quantity'),
            'cash_sales' => $salesByPayment['cash']['total'] ?? 0,
            'card_sales' => $salesByPayment['card']['total'] ?? 0,
            'mobile_sales' => $salesByPayment['mobile_money']['total'] ?? 0,
        ];

        // Cash Drawer Status
        $cashDrawerStatus = CashDrawerSession::whereDate('opened_at', $date)
            ->where('status', 'open')
            ->first();

        return view('daily-register.index', compact(
            'summary',
            'salesByPayment',
            'itemsSoldSummary',
            'creditCustomers',
            'expenses',
            'expensesByCategory',
            'recentActivity',
            'cashDrawerStatus',
            'date'
        ));
    }

    public function open(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0'
        ]);

        // Check if there's already an open session
        $existing = CashDrawerSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'A register session is already open.');
        }

        CashDrawerSession::create([
            'user_id' => auth()->id(),
            'opening_balance' => $request->opening_balance,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return redirect()->route('daily-register.index')->with('success', 'Register opened successfully.');
    }

    public function close(Request $request)
    {
        $request->validate([
            'closing_balance' => 'required|numeric|min:0'
        ]);

        $session = CashDrawerSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$session) {
            return redirect()->back()->with('error', 'No open register session found.');
        }

        $session->update([
            'closing_balance' => $request->closing_balance,
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => $request->notes
        ]);

        return redirect()->route('daily-register.index')->with('success', 'Register closed successfully.');
    }

    public function print($date = null)
    {
        if (!$date) {
            $date = Carbon::today()->toDateString();
        }

        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

        // Fetch all data for the date
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->where('payment_method', '!=', 'return')
            ->get();

        $returns = ReturnModel::whereBetween('created_at', [$startDate, $endDate])->get();
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();
        $creditSales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_method', 'credit')
            ->where('status', 'completed')
            ->get();

        $totalSales = $sales->sum('total');
        $totalReturns = $returns->sum('refund_amount');
        $totalExpenses = $expenses->sum('amount');
        $totalCreditSales = $creditSales->sum('total');

        return view('daily-register.print', compact(
            'date',
            'sales',
            'returns',
            'expenses',
            'creditSales',
            'totalSales',
            'totalReturns',
            'totalExpenses',
            'totalCreditSales'
        ));
    }
}