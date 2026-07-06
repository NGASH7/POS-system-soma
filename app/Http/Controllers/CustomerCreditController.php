<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerCreditController extends Controller
{
    public function index()
    {
        $credits = CustomerCredit::with('customer', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $summary = [
            'total_credit' => CustomerCredit::where('status', '!=', 'completed')->sum('balance'),
            'active_credits' => CustomerCredit::where('status', 'active')->count(),
            'overdue_credits' => CustomerCredit::where('status', 'overdue')->count(),
            'total_customers_with_credit' => CustomerCredit::where('status', '!=', 'completed')->distinct('customer_id')->count()
        ];

        return view('credits.index', compact('credits', 'summary'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('credits.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date|after:today',
            'type' => 'required|in:layaway,direct_credit,invoice',
            'notes' => 'nullable|string'
        ]);

        $reference = 'CR-' . date('Ymd') . '-' . str_pad((CustomerCredit::count() + 1), 4, '0', STR_PAD_LEFT);

        $credit = CustomerCredit::create([
            'customer_id' => $validated['customer_id'],
            'user_id' => Auth::id(),
            'reference' => $reference,
            'type' => $validated['type'],
            'total_amount' => $validated['total_amount'],
            'paid_amount' => 0,
            'balance' => $validated['total_amount'],
            'due_date' => $validated['due_date'],
            'status' => 'active',
            'notes' => $validated['notes']
        ]);

        // Update customer credit totals
        $customer = Customer::find($validated['customer_id']);
        $customer->increment('total_credit', $validated['total_amount']);
        $customer->increment('available_credit', $validated['total_amount']);

        return redirect()->route('credits.index')
            ->with('success', "Credit created successfully! Reference: {$reference}");
    }

    public function show(CustomerCredit $credit)
    {
        $credit->load('customer', 'user');
        return view('credits.show', compact('credit'));
    }

    public function edit(CustomerCredit $credit)
    {
        $customers = Customer::orderBy('name')->get();
        return view('credits.edit', compact('credit', 'customers'));
    }

    public function update(Request $request, CustomerCredit $credit)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'type' => 'required|in:layaway,direct_credit,invoice',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,overdue,completed,cancelled'
        ]);

        // If status changed to completed, adjust customer credit totals
        if ($validated['status'] === 'completed' && $credit->status !== 'completed') {
            $customer = Customer::find($credit->customer_id);
            $customer->decrement('total_credit', $credit->balance);
            $customer->decrement('available_credit', $credit->balance);
        }

        $credit->update($validated);

        return redirect()->route('credits.index')
            ->with('success', "Credit updated successfully!");
    }

    public function destroy(CustomerCredit $credit)
    {
        if ($credit->status !== 'completed' && $credit->balance > 0) {
            return redirect()->route('credits.index')
                ->with('error', 'Cannot delete credit with outstanding balance');
        }

        $credit->delete();

        return redirect()->route('credits.index')
            ->with('success', 'Credit deleted successfully!');
    }

    public function payment(Request $request, CustomerCredit $credit)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $credit->balance,
            'payment_method' => 'required|in:cash,card,mobile_money',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $newPaidAmount = $credit->paid_amount + $validated['amount'];
            $newBalance = $credit->balance - $validated['amount'];

            $paymentHistory = $credit->payment_history ?? [];
            $paymentHistory[] = [
                'amount' => $validated['amount'],
                'method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? '',
                'date' => now()->toDateTimeString(),
                'user' => Auth::user()->name
            ];

            $credit->update([
                'paid_amount' => $newPaidAmount,
                'balance' => $newBalance,
                'status' => $newBalance <= 0 ? 'completed' : $credit->status,
                'payment_history' => $paymentHistory
            ]);

            // Update customer credit totals
            $customer = Customer::find($credit->customer_id);
            $customer->decrement('total_credit', $validated['amount']);
            $customer->decrement('available_credit', $validated['amount']);

            // Create a sale record for this payment
            Sale::create([
                'invoice_no' => 'PAY-' . $credit->reference,
                'user_id' => Auth::id(),
                'customer_id' => $credit->customer_id,
                'terminal_id' => session()->get('terminal_id', 'TERM-01'),
                'subtotal' => $validated['amount'],
                'discount' => 0,
                'tax' => 0,
                'total' => $validated['amount'],
                'paid' => $validated['amount'],
                'change_due' => 0,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'sale_date' => now(),
                'notes' => "Payment towards {$credit->reference}"
            ]);

            DB::commit();

            return redirect()->route('credits.show', $credit)
                ->with('success', "Payment of KES " . number_format($validated['amount'], 2) . " recorded successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function getCustomerCredits($customerId)
    {
        $credits = CustomerCredit::where('customer_id', $customerId)
            ->whereIn('status', ['active', 'overdue'])
            ->orderBy('due_date')
            ->get();

        return response()->json([
            'success' => true,
            'credits' => $credits
        ]);
    }

    public function customerSummary($customerId)
    {
        $customer = Customer::with(['credits' => function($query) {
            $query->where('status', '!=', 'completed');
        }])->findOrFail($customerId);

        $credits = $customer->credits;
        $totalBalance = $credits->sum('balance');

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'total_balance' => $totalBalance,
            'credits' => $credits
        ]);
    }
}