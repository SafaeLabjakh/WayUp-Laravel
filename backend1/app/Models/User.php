<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'metier_sugg',
        'email',
        'password',
        'test_done',
    ];

    protected $hidden = ['password'];

    public function formations()
    {
        return $this->hasMany(Formation::class, 'userId');
    }
}
