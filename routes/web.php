<?php

use App\Http\Controllers\ItemIconController;
use App\Http\Controllers\KillboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KillboardController::class, 'index'])->name('killboard.index');
Route::get('/player/{albionId}', [KillboardController::class, 'player'])->name('killboard.player');
Route::get('/item-icon/{itemType}', [ItemIconController::class, 'show'])->name('item.icon');
