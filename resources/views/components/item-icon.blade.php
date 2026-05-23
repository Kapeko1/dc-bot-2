@props(['item', 'size' => 'md', 'showName' => false])

@php
    use App\Helpers\ItemHelper;

    $sizeClasses = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
    ];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];

    // Format item name nicely
    $itemName = ItemHelper::formatItemName($item);
@endphp

@if($item)
    <div class="inline-flex items-center gap-2">
        <div class="relative {{ $sizeClass }} flex-shrink-0 bg-gray-800/50 rounded border border-gray-700/50 flex items-center justify-center overflow-hidden backdrop-blur-sm">
            <img src="{{ route('item.icon', $item) }}"
                 alt="{{ $itemName }}"
                 class="w-full h-full object-contain transition-opacity duration-200"
                 title="{{ $itemName }}"
                 loading="lazy"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                 onload="this.style.opacity='1'; this.parentElement.classList.remove('bg-gray-800/50'); this.parentElement.classList.add('bg-gray-900');">
            <div class="hidden text-[10px] text-gray-500 text-center leading-tight items-center justify-center w-full h-full p-0.5" style="word-break: break-all;">
                {{ substr(str_replace(['T4_', 'T5_', 'T6_', 'T7_', 'T8_', '_LEVEL', '@'], '', $item ?? ''), 0, 12) }}
            </div>
        </div>
        @if($showName)
            <span class="text-sm text-gray-300">{{ $itemName }}</span>
        @endif
    </div>
@else
    <div class="{{ $sizeClass }} bg-gray-800 rounded border border-gray-700 flex items-center justify-center">
        <span class="text-xs text-gray-600">?</span>
    </div>
@endif
