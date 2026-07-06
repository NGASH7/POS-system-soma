<?php

namespace App\Models;

use App\Traits\BelongsToOutlet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use BelongsToOutlet;
    
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
