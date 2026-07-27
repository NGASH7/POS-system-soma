<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_no',
        'product_id',
        'user_id',
        'outlet_id',
        'quantity',
        'type',
        'reason',
        'reason_description',
        'old_stock',
        'new_stock',
        'adjustment_date',
        'status',
        'completed_at',
        'notes'
    ];

    protected $casts = [
        'old_stock' => 'decimal:2',
        'new_stock' => 'decimal:2',
        'quantity' => 'integer',
        'adjustment_date' => 'date',
        'completed_at' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function getReasonLabelAttribute()
    {
        $labels = [
            'spoilage' => 'Spoilage',
            'damage' => 'Damage',
            'theft' => 'Theft',
            'return' => 'Return to Supplier',
            'correction' => 'Stock Correction',
            'expired' => 'Expired',
            'lost' => 'Lost',
            'other' => 'Other'
        ];
        return $labels[$this->reason] ?? $this->reason;
    }

    public function getTypeBadgeAttribute()
    {
        return $this->type === 'increase' 
            ? 'bg-green-100 text-green-800' 
            : 'bg-red-100 text-red-800';
    }

    public function getFormattedQuantityAttribute()
    {
        return ($this->type === 'increase' ? '+' : '-') . $this->quantity;
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}