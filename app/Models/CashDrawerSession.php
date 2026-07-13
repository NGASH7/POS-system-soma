<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDrawerSession extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'opening_balance',
        'closing_balance',
        'opened_at',
        'closed_at',
        'status',
        'notes'
    ];
    
    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function isOpen()
    {
        return $this->status === 'open';
    }
    
    public function isClosed()
    {
        return $this->status === 'closed';
    }
}