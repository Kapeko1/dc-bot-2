@props(['equipment', 'inventory' => null, 'title' => 'Equipment'])

@php
    // Helper function to check if item is valid
    $isValidItem = function($item) {
        return $item &&
               is_array($item) &&
               isset($item['Type']) &&
               !empty($item['Type']) &&
               $item['Type'] !== null;
    };

    // Group items by slot for equipment
    $slots = [
        'MainHand' => $equipment['MainHand'] ?? null,
        'OffHand' => $equipment['OffHand'] ?? null,
        'Head' => $equipment['Head'] ?? null,
        'Armor' => $equipment['Armor'] ?? null,
        'Shoes' => $equipment['Shoes'] ?? null,
        'Bag' => $equipment['Bag'] ?? null,
        'Cape' => $equipment['Cape'] ?? null,
        'Mount' => $equipment['Mount'] ?? null,
        'Potion' => $equipment['Potion'] ?? null,
        'Food' => $equipment['Food'] ?? null,
    ];

    // Filter out empty slots
    $equippedItems = collect($slots)->filter($isValidItem);

    // Get inventory items (non-equipped)
    $inventoryItems = collect($inventory ?? [])->filter($isValidItem);
@endphp

<div class="space-y-3">
    @if($equippedItems->count() > 0)
        <div>
            <h4 class="text-xs font-semibold text-gray-400 mb-2 uppercase">{{ $title }}</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($equippedItems as $slot => $item)
                    <div class="group relative">
                        <x-item-icon :item="$item['Type']" size="lg" />
                        @if(isset($item['Count']) && $item['Count'] > 1)
                            <span class="absolute bottom-0 right-0 bg-gray-900 text-white text-xs px-1 rounded">{{ $item['Count'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($inventoryItems->count() > 0)
        <div>
            <h4 class="text-xs font-semibold text-gray-400 mb-2 uppercase">Inventory ({{ $inventoryItems->count() }} items)</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($inventoryItems->take(24) as $item)
                    <div class="group relative">
                        <x-item-icon :item="$item['Type']" size="lg" />
                        @if(isset($item['Count']) && $item['Count'] > 1)
                            <span class="absolute bottom-0 right-0 bg-gray-900 text-white text-xs px-1 rounded">{{ $item['Count'] }}</span>
                        @endif
                    </div>
                @endforeach
                @if($inventoryItems->count() > 24)
                    <div class="flex items-center justify-center text-gray-600 text-xs px-2">
                        +{{ $inventoryItems->count() - 24 }} more
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($equippedItems->count() === 0 && $inventoryItems->count() === 0)
        <p class="text-xs text-gray-600">No items</p>
    @endif
</div>
