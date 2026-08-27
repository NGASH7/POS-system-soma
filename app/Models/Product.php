<?php

namespace App\Models;

use App\Traits\BelongsToOutlet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use BelongsToOutlet;
    
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'price',
        'cost',
        'stock_quantity',
        'low_stock_threshold',
        'category',
        'category_id',
        'brand_id',
        'image',
        'is_active',
        'is_favorite',
        'outlet_id',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
        'is_favorite' => 'boolean'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
