<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_no',
        'user_id',
        'customer_id',
        'terminal_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid',
        'change_due',
        'payment_method',
        'status',
        'notes',
        'sale_date',
        'is_return'
    ];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'change_due' => 'decimal:2',
        'sale_date' => 'datetime',
        'is_return' => 'boolean'
    ];
    
    // Simple relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
