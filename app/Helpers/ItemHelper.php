<?php

namespace App\Helpers;

class ItemHelper
{
    /**
     * Convert Albion item type to a readable name
     * Example: T6_2H_DAGGERPAIR@1 -> Tier 6 Dagger Pair +1
     */
    public static function formatItemName(?string $itemType): ?string
    {
        if (!$itemType) {
            return null;
        }

        // Extract tier
        preg_match('/^T(\d+)_/', $itemType, $tierMatches);
        $tier = $tierMatches[1] ?? '';

        // Extract enchantment
        preg_match('/@(\d+)$/', $itemType, $enchantMatches);
        $enchantment = $enchantMatches[1] ?? '';

        // Get item name part (remove tier and enchantment)
        $name = preg_replace('/^T\d+_/', '', $itemType);
        $name = preg_replace('/@\d+$/', '', $name);

        // Remove prefixes like 2H_, MAIN_, OFF_
        $name = preg_replace('/^(2H|MAIN|OFF|HEAD|ARMOR|SHOES|CAPE|BAG)_/', '', $name);

        // Replace underscores with spaces and convert to title case
        $name = str_replace('_', ' ', $name);
        $name = ucwords(strtolower($name));

        // Build final name with better spacing
        $parts = [];
        if ($tier) {
            $parts[] = "T{$tier}";
        }
        if ($name) {
            $parts[] = $name;
        }

        $formatted = implode(' ', $parts);

        // Add enchantment as suffix
        if ($enchantment && $enchantment !== '0') {
            $formatted .= " +{$enchantment}";
        }

        return trim($formatted);
    }
}
