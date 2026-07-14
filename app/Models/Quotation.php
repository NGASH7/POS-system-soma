<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_no',
        'customer_id',
        'user_id',
        'outlet_id',
        'quotation_date',
        'expiry_date',
        'subtotal',
        'discount',
        'discount_type',
        'tax',
        'total',
        'notes',
        'terms',
        'status',
        'items',
        'converted_at',
        'converted_sale_id'
    ];

    protected $casts = [
        'items' => 'array',
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'converted_at' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function convertedSale()
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast() && $this->status !== 'converted';
    }

    public function getItemsListAttribute()
    {
        return $this->items ? collect($this->items) : collect();
    }

    public function getFormattedTotalAttribute()
    {
        return 'KES ' . number_format($this->total, 2);
    }

    public function getProgressAttribute()
    {
        $statuses = [
            'draft' => 'bg-gray-100 text-gray-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'converted' => 'bg-purple-100 text-purple-800',
            'expired' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800'
        ];
        return $statuses[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}