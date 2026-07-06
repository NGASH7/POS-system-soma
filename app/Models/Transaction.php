<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'provider_reference',
        'amount',
        'phone',
        'status',
        'provider',
        'cart_data',
        'payment_data',
        'user_id',
        'sale_id',
        'error_message',
        'completed_at'
    ];

    protected $casts = [
        'cart_data' => 'array',
        'payment_data' => 'array',
        'amount' => 'decimal:2',
        'completed_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function markAsCompleted($saleId = null)
    {
        $this->update([
            'status' => 'completed',
            'sale_id' => $saleId,
            'completed_at' => now()
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    public function markAsProcessing($providerReference = null)
    {
        $this->update([
            'status' => 'processing',
            'provider_reference' => $providerReference
        ]);
    }
}