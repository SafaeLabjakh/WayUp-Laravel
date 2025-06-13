<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'acquired',
        'user_id',
    ];

    // relation : Skill appartient à un User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
