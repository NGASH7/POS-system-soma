<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReturnModel; // Add this line

class SaleItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'total'
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2'
    ];
    
    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    // Check if this item has been returned
    public function isReturned()
    {
        return $this->sale->isReturn() || 
               ReturnModel::where('original_sale_id', $this->sale_id)
                   ->whereJsonContains('items', $this->id)
                   ->exists();
    }
}