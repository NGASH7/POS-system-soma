<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'category_id',
        'user_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'receipt_image',
        'vendor',
        'status',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'is_recurring' => 'boolean',
        'recurring_end_date' => 'date'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute()
    {
        return 'KES ' . number_format($this->amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800'
        ];
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}