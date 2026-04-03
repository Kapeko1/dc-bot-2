<?php

namespace App\Services;

use App\Models\ItemIconCache;
use Illuminate\Support\Facades\Http;

class InventoryImageService
{
    private int $iconSize = 64;
    private int $columns = 10;
    private int $padding = 5;

    public function generate(array $inventory): ?string
    {
        $items = array_filter($inventory);
        if (empty($items)) {
            return null;
        }

        $total = count($items);
        $rows = (int)ceil($total / $this->columns);
        $width = $this->columns * ($this->iconSize + $this->padding) - $this->padding;
        $height = $rows * ($this->iconSize + $this->padding) - $this->padding;

        $canvas = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($canvas, 73, 109, 137);
        imagefill($canvas, 0, 0, $bg);

        $white = imagecolorallocate($canvas, 255, 255, 255);
        $fontPath = resource_path('fonts/ARIALBD.TTF');

        $index = 0;
        foreach ($items as $item) {
            if (!isset($item['Type'])) {
                continue;
            }

            $col = $index % $this->columns;
            $row = (int)floor($index / $this->columns);
            $x = $col * ($this->iconSize + $this->padding);
            $y = $row * ($this->iconSize + $this->padding);

            $iconData = $this->getIconData($item['Type']);

            if ($iconData) {
                $icon = imagecreatefromstring($iconData);
                if ($icon) {
                    imagecopyresampled(
                        $canvas,
                        $icon,
                        $x,
                        $y,
                        0,
                        0,
                        $this->iconSize,
                        $this->iconSize,
                        imagesx($icon),
                        imagesy($icon)
                    );
                    imagedestroy($icon);
                }
            }

            $count = $item['Count'] ?? 1;
            if ($count > 1 && file_exists($fontPath)) {
                imagettftext(
                    $canvas,
                    14,
                    0,
                    $x + $this->iconSize - 21,
                    $y + $this->iconSize - 5,
                    $white,
                    $fontPath,
                    (string)$count
                );
            }

            $index++;
        }

        ob_start();
        imagepng($canvas);
        $imageData = ob_get_clean();
        imagedestroy($canvas);

        return base64_encode($imageData);
    }

    private function getIconData(string $itemType): ?string
    {
        // Check cache first
        $cached = ItemIconCache::where('item_type', $itemType)->first();
        if ($cached) {
            return base64_decode($cached->image_data);
        }

        // Fetch from API
        $iconUrl = "https://render.albiononline.com/v1/item/{$itemType}.png?size=64";

        try {
            $response = Http::timeout(5)->retry(2, 100)->get($iconUrl);

            if ($response->successful()) {
                $iconData = $response->body();

                if ($iconData) {
                    // Cache it
                    ItemIconCache::create([
                        'item_type' => $itemType,
                        'image_data' => base64_encode($iconData),
                    ]);

                    return $iconData;
                }
            }
        } catch (\Exception $e) {
            // Failed to fetch - return null
            return null;
        }

        return null;
    }
}
