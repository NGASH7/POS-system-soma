<?php

namespace App\Models;

use App\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Discount extends Model
{
    use HasFactory, BelongsToOutlet;

    protected $fillable = [
        'outlet_id',
        'user_id',
        'name',
        'code',
        'type',
        'value',
        'min_spend',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'ends_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class)->withoutGlobalScopes();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
    }

    /**
     * Scope to only include active discounts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include currently valid discounts (active, not expired, within date range).
     */
    public function scopeValidNow(Builder $query): Builder
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    /**
     * Check if discount is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if ($this->ends_at && $this->ends_at->isPast()) {
            return true;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return true;
        }

        return false;
    }

    /**
     * Get computed status string: 'active', 'inactive', or 'expired'.
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'scheduled';
        }

        return 'active';
    }

    /**
     * Get human-readable formatted discount value (e.g. "15%" or "KES 500.00").
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return rtrim(rtrim(number_format($this->value, 2), '0'), '.') . '% OFF';
        }

        return 'KES ' . number_format($this->value, 2) . ' OFF';
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        if ($this->min_spend && $subtotal < (float)$this->min_spend) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $discountAmount = ($subtotal * (float)$this->value) / 100;
            if ($this->max_discount && $discountAmount > (float)$this->max_discount) {
                $discountAmount = (float)$this->max_discount;
            }
            return min($discountAmount, $subtotal);
        }

        // Fixed amount discount
        return min((float)$this->value, $subtotal);
    }
}
