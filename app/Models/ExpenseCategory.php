<?php

namespace App\Models;

use App\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseCategory extends Model
{
    use HasFactory, BelongsToOutlet;

    protected $guarded = [];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
