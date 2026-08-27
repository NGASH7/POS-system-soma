<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_no',
        'warranty_id',
        'user_id',
        'claim_date',
        'issue_description',
        'status',
        'resolution',
        'resolution_notes',
        'resolved_date'
    ];

    protected $casts = [
        'claim_date' => 'date',
        'resolved_date' => 'date'
    ];

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'completed' => 'bg-green-100 text-green-800'
        ];
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getResolutionLabelAttribute()
    {
        $labels = [
            'repair' => 'Repaired',
            'replace' => 'Replaced',
            'refund' => 'Refunded',
            'none' => 'None'
        ];
        return $labels[$this->resolution] ?? $this->resolution;
    }
}