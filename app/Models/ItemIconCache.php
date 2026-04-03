<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemIconCache extends Model
{
    protected $table = 'item_icons_cache';

    protected $fillable = [
        'item_type',
        'image_data',
    ];

    protected $casts = [
        'image_data' => 'string',
    ];
}
