<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_name',
        'url',
        'user_id',
    ];

    // relation : Formation appartient à un User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
