<?php

namespace App\Traits;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToOutlet
{
    /**
     * Boot the trait to add the global scope.
     */
    public static function bootBelongsToOutlet()
    {
        // Global scope to filter by outlet
        static::addGlobalScope('outlet', function (Builder $builder) {
            if (auth()->hasUser()) {
                $user = auth()->user();
                
                // Admin logic: filter if a specific outlet is selected in session
                if ($user->isAdmin() && session()->has('active_outlet_id')) {
                    $builder->where('outlet_id', session('active_outlet_id'));
                } 
                // Standard user logic: strictly filter by their assigned outlet
                elseif (!$user->isAdmin()) {
                    $builder->where('outlet_id', $user->outlet_id);
                }
            }
        });

        // Automatically assign the correct outlet when creating new records
        static::creating(function ($model) {
            if (auth()->hasUser()) {
                $user = auth()->user();
                
                if ($user->isAdmin()) {
                    // Admins assign to the active session outlet, or default to 1 (Main)
                    $model->outlet_id = session('active_outlet_id', 1);
                } else {
                    // Employees assign to their assigned outlet
                    $model->outlet_id = $user->outlet_id;
                }
            } else {
                // Console/Seeders fallback
                if (empty($model->outlet_id)) {
                    $model->outlet_id = 1;
                }
            }
        });
    }

    /**
     * Define the relationship to the Outlet model.
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
