<?php

namespace App\Http\Controllers;

use App\Models\ItemIconCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ItemIconController extends Controller
{
    public function show(string $itemType)
    {
        // Check cache first (including failed lookups)
        $cached = ItemIconCache::where('item_type', $itemType)->first();

        if ($cached) {
            // If it's a failed lookup marker (empty data), return 404 quickly
            if (empty($cached->image_data) || $cached->image_data === 'FAILED') {
                abort(404);
            }

            $imageData = base64_decode($cached->image_data);
            return response($imageData)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=31536000'); // Cache for 1 year
        }

        // Fetch from Albion render API
        $iconUrl = "https://render.albiononline.com/v1/item/{$itemType}.png?size=64";

        try {
            $response = Http::timeout(3)->retry(1, 100)->get($iconUrl);

            if ($response->successful()) {
                $imageData = $response->body();

                // Cache it
                ItemIconCache::create([
                    'item_type' => $itemType,
                    'image_data' => base64_encode($imageData),
                ]);

                return response($imageData)
                    ->header('Content-Type', 'image/png')
                    ->header('Cache-Control', 'public, max-age=31536000');
            } else {
                // Cache the failure to avoid repeated lookups
                ItemIconCache::create([
                    'item_type' => $itemType,
                    'image_data' => 'FAILED',
                ]);
            }
        } catch (\Exception $e) {
            // Cache the failure
            ItemIconCache::create([
                'item_type' => $itemType,
                'image_data' => 'FAILED',
            ]);
        }

        abort(404);
    }
}
