<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'reference',
        'type',
        'total_amount',
        'paid_amount',
        'balance',
        'due_date',
        'status',
        'notes',
        'items',
        'payment_history'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
        'items' => 'array',
        'payment_history' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    public function getFormattedBalanceAttribute()
    {
        return 'KES ' . number_format($this->balance, 2);
    }

    /**
     * Get the progress percentage of payment
     */
    public function getProgressPercentage()
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return round(($this->paid_amount / $this->total_amount) * 100, 1);
    }

    /**
     * Get the progress percentage as an attribute
     */
    public function getProgressPercentageAttribute()
    {
        return $this->getProgressPercentage();
    }

    /**
     * Check if the credit is fully paid
     */
    public function isFullyPaid()
    {
        return $this->balance <= 0;
    }

    /**
     * Record a payment
     */
    public function recordPayment($amount, $method, $notes = null, $user = null)
    {
        $paymentHistory = $this->payment_history ?? [];
        $paymentHistory[] = [
            'amount' => $amount,
            'method' => $method,
            'notes' => $notes ?? '',
            'date' => now()->toDateTimeString(),
            'user' => $user ?? auth()->user()->name
        ];

        $newPaidAmount = $this->paid_amount + $amount;
        $newBalance = $this->balance - $amount;

        $this->update([
            'paid_amount' => $newPaidAmount,
            'balance' => $newBalance,
            'status' => $newBalance <= 0 ? 'completed' : $this->status,
            'payment_history' => $paymentHistory
        ]);

        return $this;
    }
}