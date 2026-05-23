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

<div class="space-y-4">
    @if($equippedItems->count() > 0)
        <div>
            <h4 class="font-[Space_Grotesk] text-xs font-bold text-[#E8DCC8]/70 mb-3 uppercase tracking-wider flex items-center gap-2">
                <div class="w-1 h-3 bg-gradient-to-b from-[#D4AF37] to-transparent"></div>
                {{ $title }}
            </h4>
            <div class="flex flex-wrap gap-2.5">
                @foreach($equippedItems as $slot => $item)
                    <div class="relative">
                        <div class="relative bg-[#0A0A0A] border border-[#4A4A4A] hover:border-[#D4AF37] transition-all duration-300 p-1">
                            <x-item-icon :item="$item['Type']" size="lg" />
                            @if(isset($item['Count']) && $item['Count'] > 1)
                                <span class="absolute -bottom-0.5 -right-0.5 bg-[#0A0A0A] text-[#E8DCC8] font-[JetBrains_Mono] text-[0.6rem] font-semibold px-1 py-0.5 border border-[#4A4A4A]">
                                    {{ $item['Count'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($inventoryItems->count() > 0)
        <div>
            <h4 class="font-[Space_Grotesk] text-xs font-bold text-[#E8DCC8]/70 mb-3 uppercase tracking-wider flex items-center gap-2">
                <div class="w-1 h-3 bg-gradient-to-b from-[#D4AF37] to-transparent"></div>
                Inventory
                <span class="font-[JetBrains_Mono] text-[0.65rem] text-[#D4AF37] ml-1">({{ $inventoryItems->count() }})</span>
            </h4>
            <div class="flex flex-wrap gap-2">
                @foreach($inventoryItems->take(24) as $item)
                    <div class="relative">
                        <div class="relative bg-[#0A0A0A] border border-[#4A4A4A]/50 hover:border-[#D4AF37] transition-all duration-300 p-1">
                            <x-item-icon :item="$item['Type']" size="lg" />
                            @if(isset($item['Count']) && $item['Count'] > 1)
                                <span class="absolute -bottom-0.5 -right-0.5 bg-[#0A0A0A] text-[#E8DCC8] font-[JetBrains_Mono] text-[0.6rem] font-semibold px-1 py-0.5 border border-[#4A4A4A]">
                                    {{ $item['Count'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($inventoryItems->count() > 24)
                    <div class="flex items-center justify-center px-3 py-2 bg-[#2D2D2D]/50 border border-[#4A4A4A]">
                        <span class="font-[JetBrains_Mono] text-xs text-[#E8DCC8]/50">
                            +{{ $inventoryItems->count() - 24 }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($equippedItems->count() === 0 && $inventoryItems->count() === 0)
        <div class="py-8 text-center">
            <p class="font-[Space_Grotesk] text-xs text-[#E8DCC8]/30 uppercase tracking-wider">No equipment found</p>
        </div>
    @endif
</div>
