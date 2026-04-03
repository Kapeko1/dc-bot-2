<?php

namespace App\Services;

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

            $iconUrl = "https://render.albiononline.com/v1/item/{$item['Type']}.png?size=64";
            $iconData = Http::get($iconUrl)->body();

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
}
