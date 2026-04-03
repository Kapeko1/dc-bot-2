<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'albion_id',
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
