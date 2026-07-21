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

        // Fix summary key names to match the view
        $summary = [
            'total_outstanding' => CustomerCredit::whereIn('status', ['active', 'overdue'])->sum('balance'),
            'active_count' => CustomerCredit::where('status', 'active')->count(),
            'overdue_total' => CustomerCredit::where('status', 'overdue')->sum('balance'),
            'overdue_count' => CustomerCredit::where('status', 'overdue')->count(),
            'completed_count' => CustomerCredit::where('status', 'completed')->count(),
            'customers_with_credit' => CustomerCredit::whereIn('status', ['active', 'overdue'])->distinct('customer_id')->count()
        ];

        // Get customers for filter
        $customers = Customer::orderBy('name')->get();

        return view('credits.index', compact('credits', 'summary', 'customers'));
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

        $reference = $this->generateCreditReference();

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
        $customer = Customer::withoutGlobalScopes()->find($validated['customer_id']);
        if ($customer) {
            $customer->increment('total_credit', $validated['total_amount']);
            $customer->increment('available_credit', $validated['total_amount']);
        }

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
            $customer = Customer::withoutGlobalScopes()->find($credit->customer_id);
            if ($customer) {
                $customer->decrement('total_credit', $credit->balance);
                $customer->decrement('available_credit', $credit->balance);
            }
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
            'payment_method' => 'required|in:cash,card,mobile_money,credit',
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
            $customer = Customer::withoutGlobalScopes()->find($credit->customer_id);
            if ($customer) {
                $customer->decrement('total_credit', $validated['amount']);
                $customer->decrement('available_credit', $validated['amount']);
            }

            // Create a UNIQUE sale record for this payment
            // Generate unique invoice number: PAY-{credit_ref}-{timestamp}
            $uniqueInvoiceNo = 'PAY-' . $credit->reference . '-' . date('YmdHis') . '-' . rand(100, 999);
            
            // Check if invoice_no already exists (just to be safe)
            $attempts = 0;
            while (Sale::where('invoice_no', $uniqueInvoiceNo)->exists() && $attempts < 10) {
                $uniqueInvoiceNo = 'PAY-' . $credit->reference . '-' . date('YmdHis') . '-' . rand(100, 999);
                $attempts++;
            }

            Sale::create([
                'invoice_no' => $uniqueInvoiceNo,  // Now unique
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
                'notes' => "Payment towards {$credit->reference}",
                'outlet_id' => Auth::user()->outlet_id ?? 1
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

    public function getBalance($id)
    {
        try {
            $credit = CustomerCredit::findOrFail($id);
            return response()->json([
                'balance' => $credit->balance,
                'reference' => $credit->reference
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Credit not found'], 404);
        }
    }

    /**
     * Generate credit reference with SOMA brand and branch prefix
     */
    private function generateCreditReference()
    {
        $branchCode = $this->getBranchCode();
        $date = date('Ymd');
        
        $reference = 'SOMA-' . $branchCode . '-CR-' . $date . '-' . str_pad(CustomerCredit::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Check uniqueness
        $attempts = 0;
        while (CustomerCredit::where('reference', $reference)->exists() && $attempts < 100) {
            $number = intval(substr($reference, -4)) + 1;
            $reference = 'SOMA-' . $branchCode . '-CR-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            $attempts++;
        }
        
        return $reference;
    }

    /**
     * Get the branch code for credit reference
     */
    private function getBranchCode()
    {
        try {
            $outlet = \App\Models\Outlet::find(auth()->user()->outlet_id);
            if ($outlet) {
                $cleanName = preg_replace('/[^a-zA-Z]/', '', $outlet->name);
                $code = strtoupper(substr($cleanName, 0, 3));
                if (strlen($code) < 2) {
                    $code = 'OUT';
                }
                return $code;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Could not get outlet for branch code: ' . $e->getMessage());
        }
        return 'SOM';
    }
}