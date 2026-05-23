<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function kills(): HasMany
    {
        return $this->hasMany(ProcessedKill::class, 'albion_player_id', 'albion_id');
    }

    public function deaths(): HasMany
    {
        return $this->hasMany(ProcessedDeath::class, 'albion_player_id', 'albion_id');
    }
}
