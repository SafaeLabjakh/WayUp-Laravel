<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = ['formationName', 'url', 'userId'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
