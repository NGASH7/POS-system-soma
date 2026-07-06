<?php

namespace App\Models;

use App\Traits\BelongsToOutlet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    use BelongsToOutlet;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'loyalty_card',
        'points',
        'total_spent',
        'total_credit',
        'available_credit',
        'credit_limit',
        'credit_approved_until'
    ];
    
    protected $casts = [
        'points' => 'decimal:2',
        'total_spent' => 'decimal:2'
    ];
    
    // Relationships
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
