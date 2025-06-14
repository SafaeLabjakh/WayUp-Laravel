<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
        use HasApiTokens, Notifiable;

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
    public function skills()
    {
        return $this->hasMany(Skill::class);
       
    }
}
