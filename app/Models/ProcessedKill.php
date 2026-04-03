<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedKill extends Model
{
    protected $fillable = [
        'event_id',
        'albion_player_id',
    ];
}
