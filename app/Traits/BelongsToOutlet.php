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
                
                if ($user->isAdmin()) {
                    $activeOutletId = session('active_outlet_id');
                    if ($activeOutletId) {
                        $builder->where('outlet_id', $activeOutletId);
                    } else {
                        // Fallback to first available outlet if no session is set
                        $firstOutletId = Outlet::first()?->id;
                        if ($firstOutletId) {
                            $builder->where('outlet_id', $firstOutletId);
                        }
                    }
                } elseif ($user->outlet_id) {
                    $builder->where('outlet_id', $user->outlet_id);
                }
            }
        });

        // Automatically assign the correct outlet when creating new records
        static::creating(function ($model) {
            if (!empty($model->outlet_id)) {
                return;
            }

            if (auth()->hasUser()) {
                $user = auth()->user();
                
                if ($user->isAdmin()) {
                    $model->outlet_id = session('active_outlet_id') ?: Outlet::first()?->id;
                } else {
                    $model->outlet_id = $user->outlet_id;
                }
            } else {
                // Console/Seeders/Tests fallback
                $model->outlet_id = Outlet::first()?->id;
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
