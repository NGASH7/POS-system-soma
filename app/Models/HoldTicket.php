<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldTicket extends Model
{
    use HasFactory;

    protected $table = 'held_tickets';

    protected $fillable = [
        'ticket_number',
        'reference_note',
        'user_id',
        'customer_id',
        'cart_data',
        'subtotal',
        'tax',
        'total_amount',
        'items_count',
        'status'
    ];

    protected $casts = [
        'cart_data' => 'array',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'items_count' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
