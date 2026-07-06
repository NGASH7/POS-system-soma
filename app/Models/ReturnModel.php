<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'return_no',
        'original_sale_id',
        'user_id',
        'customer_id',
        'refund_amount',
        'refund_method',
        'return_type',
        'reason',
        'notes',
        'status',
        'items',
        'exchange_items'
    ];

    protected $casts = [
        'items' => 'array',
        'exchange_items' => 'array',
        'refund_amount' => 'decimal:2'
    ];

    // Simple relationships - NO circular references
    public function originalSale()
    {
        return $this->belongsTo(Sale::class, 'original_sale_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
