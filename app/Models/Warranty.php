<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_no',
        'product_id',
        'sale_id',
        'customer_id',
        'user_id',
        'outlet_id',
        'purchase_date',
        'expiry_date',
        'serial_number',
        'batch_number',
        'status',
        'warranty_type',
        'warranty_duration_months',
        'terms',
        'notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'warranty_duration_months' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

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

    public function claims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->expiry_date->isFuture();
    }

    public function isExpired()
    {
        return $this->expiry_date->isPast() || $this->status === 'expired';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'bg-green-100 text-green-800',
            'expired' => 'bg-red-100 text-red-800',
            'claimed' => 'bg-yellow-100 text-yellow-800',
            'replaced' => 'bg-blue-100 text-blue-800',
            'void' => 'bg-gray-100 text-gray-800'
        ];
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'manufacturer' => 'Manufacturer Warranty',
            'store' => 'Store Warranty',
            'extended' => 'Extended Warranty'
        ];
        return $labels[$this->warranty_type] ?? $this->warranty_type;
    }
}