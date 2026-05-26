<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessedDeath extends Model
{
    protected $fillable = [
        'event_id',
        'albion_player_id',
        'event_data',
        'killer_name',
        'victim_name',
        'killer_guild',
        'victim_guild',
        'total_fame',
        'killer_weapon',
        'victim_weapon',
        'killed_at',
        'killer_ip',
        'victim_ip',
        'participants_count',
        'kill_area',
    ];

    protected $casts = [
        'event_data' => 'array',
        'killed_at' => 'datetime',
        'total_fame' => 'integer',
        'killer_ip' => 'integer',
        'victim_ip' => 'integer',
        'participants_count' => 'integer',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'albion_player_id', 'albion_id');
    }
}
