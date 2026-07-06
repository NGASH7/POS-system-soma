<?php

namespace App\Models;

use App\Traits\BelongsToOutlet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use BelongsToOutlet;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function isEmployee()
    {
        return $this->role === 'employee';
    }
}
