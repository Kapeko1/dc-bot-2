@extends('layout')

@section('title', 'Kill Log - DC Killboard')

@section('content')
<div class="space-y-8">
    <!-- Header with dramatic styling -->
    <div class="relative">
        <div class="absolute -left-4 top-0 bottom-0 w-1 bg-gradient-to-b from-[#DC143C] via-[#D4AF37] to-transparent transform -skew-y-12"></div>
        <div class="pl-6">
            <h1 class="font-[Cinzel] text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#DC143C] via-[#EF4444] to-[#D4AF37] tracking-wide slash-accent animate-on-scroll">
                KILL LOG
            </h1>
        </div>
    </div>

    <!-- Filters with refined brutalist design -->
    <div class="relative metal-gradient border-2 border-[#DC143C]/30 p-6 animate-on-scroll stagger-2 combat-texture overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#DC143C]/20 to-transparent blur-2xl"></div>
        <form method="GET" action="{{ route('killboard.index') }}" class="relative z-10 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="player" class="block font-[Space_Grotesk] text-xs font-semibold text-[#D4AF37] mb-2 tracking-widest uppercase">Search Player</label>
                <input type="text"
                       name="player"
                       id="player"
                       value="{{ $playerFilter }}"
                       placeholder="Enter player name..."
                       class="w-full px-4 py-3 bg-[#0A0A0A]/80 border-2 border-[#4A4A4A] text-[#E8DCC8] placeholder-[#E8DCC8]/30 font-[Space_Grotesk] focus:border-[#DC143C] focus:ring-2 focus:ring-[#DC143C]/50 transition-all duration-300">
            </div>

            <div class="w-full sm:w-56">
                <label for="sort" class="block font-[Space_Grotesk] text-xs font-semibold text-[#D4AF37] mb-2 tracking-widest uppercase">Sort By</label>
                <select name="sort"
                        id="sort"
                        class="w-full px-4 py-3 bg-[#0A0A0A]/80 border-2 border-[#4A4A4A] text-[#E8DCC8] font-[Space_Grotesk] focus:border-[#DC143C] focus:ring-2 focus:ring-[#DC143C]/50 transition-all duration-300">
                    <option value="recent" {{ $sortBy === 'recent' ? 'selected' : '' }}>Most Recent</option>
                    <option value="fame" {{ $sortBy === 'fame' ? 'selected' : '' }}>Highest Glory</option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="relative px-6 py-3 bg-gradient-to-r from-[#DC143C] to-[#8B0000] text-[#E8DCC8] font-[Space_Grotesk] font-semibold tracking-wide uppercase border-2 border-[#DC143C] hover:border-[#D4AF37] transition-all duration-300 overflow-hidden group">
                    <span class="relative z-10">Apply</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#D4AF37] to-[#8B7500] transform translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                </button>
                @if($playerFilter || $sortBy !== 'recent')
                    <a href="{{ route('killboard.index') }}"
                       class="px-6 py-3 bg-[#2D2D2D] hover:bg-[#4A4A4A] text-[#E8DCC8] font-[Space_Grotesk] font-medium tracking-wide uppercase border-2 border-[#4A4A4A] hover:border-[#DC143C]/50 transition-all duration-300">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tracked Players with medieval badge design -->
    <div class="relative bg-gradient-to-r from-[#2D2D2D]/50 via-[#1A0A0A]/50 to-[#2D2D2D]/50 border-l-4 border-[#D4AF37] p-6 animate-on-scroll stagger-3">
        <h2 class="font-[Cinzel] text-sm font-bold text-[#D4AF37] mb-4 tracking-[0.3em] uppercase">Tracked Players</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($players as $player)
                <a href="{{ route('killboard.player', $player->albion_id) }}"
                   class="group relative px-4 py-2 bg-[#0A0A0A] border border-[#4A4A4A] hover:border-[#DC143C] font-[Space_Grotesk] font-medium text-sm text-[#E8DCC8]/80 hover:text-[#DC143C] transition-all duration-300 overflow-hidden">
                    <span class="relative z-10">{{ $player->name }}</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#DC143C]/10 to-transparent transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-[#DC143C] to-[#D4AF37] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Events List - Battle Records -->
    <div class="space-y-4">
        @forelse($events as $event)
            <div class="group relative bg-gradient-to-r from-[#2D2D2D]/80 via-[#1A0A0A]/80 to-[#2D2D2D]/80 border-l-4 {{ $event->event_type === 'kill' ? 'border-[#10B981]' : 'border-[#DC143C]' }} hover:border-[#D4AF37] transition-all duration-500 overflow-hidden cursor-pointer animate-on-scroll combat-texture" onclick="toggleDetails('event-{{ $event->id }}')" style="animation-delay: {{ $loop->index * 0.05 }}s">
                <!-- Dramatic diagonal accent -->
                <div class="absolute top-0 right-0 w-32 h-full bg-gradient-to-l from-{{ $event->event_type === 'kill' ? '[#10B981]' : '[#DC143C]' }}/10 to-transparent transform skew-x-12 group-hover:w-48 transition-all duration-500"></div>

                <div class="relative z-10 p-5">
                    <div class="flex items-start justify-between gap-6">
                        <!-- Main Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-4 mb-4">
                                @if($event->event_type === 'kill')
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-[#10B981] blur-md opacity-50"></div>
                                        <span class="relative inline-flex items-center px-3 py-1.5 bg-[#10B981] text-[#E8DCC8] font-[Space_Grotesk] text-xs font-bold tracking-widest uppercase border border-[#10B981]">
                                            ⚔ KILL
                                        </span>
                                    </div>
                                @else
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-[#DC143C] blur-md opacity-50"></div>
                                        <span class="relative inline-flex items-center px-3 py-1.5 bg-[#DC143C] text-[#E8DCC8] font-[Space_Grotesk] text-xs font-bold tracking-widest uppercase border border-[#DC143C]">
                                            ☠ DEATH
                                        </span>
                                    </div>
                                @endif
                                <span class="font-[JetBrains_Mono] text-xs text-[#E8DCC8]/50 tracking-wide">
                                    {{ $event->killed_at?->diffForHumans() ?? 'Unknown time' }}
                                </span>
                                <span class="ml-auto font-[Space_Grotesk] text-xs text-[#D4AF37]/70 uppercase tracking-wider details-toggle-text group-hover:text-[#D4AF37] transition-colors">
                                    Expand ▼
                                </span>
                            </div>

                            <div class="space-y-3">
                                <!-- Killer -->
                                <div class="flex items-center gap-3">
                                    <span class="font-[Space_Grotesk] text-xs text-[#D4AF37] uppercase tracking-widest min-w-[60px]">Victor:</span>
                                    @if($event->killer_weapon)
                                        <div class="flex-shrink-0">
                                            <x-item-icon :item="$event->killer_weapon" size="lg" />
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-[Space_Grotesk] text-[#E8DCC8] font-semibold text-base">{{ $event->killer_name ?? 'Unknown' }}</span>
                                        @if($event->killer_guild)
                                            <span class="font-[JetBrains_Mono] text-xs text-[#E8DCC8]/50 border border-[#4A4A4A] px-2 py-0.5 bg-[#0A0A0A]/50">[{{ $event->killer_guild }}]</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- VS Divider -->
                                <div class="flex items-center gap-3 pl-[60px]">
                                    <div class="h-px flex-1 bg-gradient-to-r from-[#DC143C] via-[#4A4A4A] to-transparent"></div>
                                    <span class="font-[Cinzel] text-xs text-[#DC143C] font-bold">VS</span>
                                    <div class="h-px flex-1 bg-gradient-to-l from-[#DC143C] via-[#4A4A4A] to-transparent"></div>
                                </div>

                                <!-- Victim -->
                                <div class="flex items-center gap-3">
                                    <span class="font-[Space_Grotesk] text-xs text-[#DC143C]/80 uppercase tracking-widest min-w-[60px]">Victim:</span>
                                    @if($event->victim_weapon)
                                        <div class="flex-shrink-0">
                                            <x-item-icon :item="$event->victim_weapon" size="lg" />
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-[Space_Grotesk] text-[#E8DCC8]/90 font-medium text-base">{{ $event->victim_name ?? 'Unknown' }}</span>
                                        @if($event->victim_guild)
                                            <span class="font-[JetBrains_Mono] text-xs text-[#E8DCC8]/50 border border-[#4A4A4A] px-2 py-0.5 bg-[#0A0A0A]/50">[{{ $event->victim_guild }}]</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Battle Info -->
                                <div class="flex items-center gap-6 font-[JetBrains_Mono] text-xs text-[#E8DCC8]/40 mt-3 pl-[60px]">
                                    <span class="tracking-wider">IP: {{ $event->killer_ip }} ⚔ {{ $event->victim_ip }}</span>
                                    @if($event->participants_count > 1)
                                        <span class="tracking-wider">PLAYERS: {{ $event->participants_count }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Fame Badge - Ornate Design -->
                        <div class="flex flex-col items-end ml-6">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-[#D4AF37] to-[#8B7500] blur-lg opacity-30"></div>
                                <div class="relative bg-gradient-to-br from-[#2D2D2D] to-[#1A0A0A] border-2 border-[#D4AF37] p-4 transform -rotate-3">
                                    <div class="transform rotate-3">
                                        <div class="font-[Cinzel] text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#D4AF37] via-[#F4EFE3] to-[#8B7500] leading-none">
                                            {{ number_format($event->total_fame) }}
                                        </div>
                                        <div class="font-[Space_Grotesk] text-[0.65rem] text-[#D4AF37] uppercase tracking-[0.2em] mt-1 text-center">Fame</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expandable Details - Arsenal Display -->
                @if($event->event_data)
                    <div id="event-{{ $event->id }}" class="hidden border-t-2 border-[#DC143C]/20">
                        <div class="bg-gradient-to-b from-[#0A0A0A]/90 to-[#1A0A0A]/90 p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Killer Equipment -->
                                <div class="relative">
                                    <div class="absolute -left-2 top-0 bottom-0 w-1 bg-gradient-to-b from-[#10B981] to-transparent"></div>
                                    <h3 class="font-[Cinzel] text-base font-bold text-[#10B981] mb-4 tracking-wide uppercase border-b border-[#10B981]/30 pb-2">
                                        {{ $event->killer_name ?? 'Killer' }}'s Arsenal
                                    </h3>
                                    <x-equipment-grid
                                        :equipment="$event->event_data['Killer']['Equipment'] ?? []"
                                        :inventory="$event->event_data['Killer']['Inventory'] ?? []"
                                        title="Equipment"
                                    />
                                </div>

                                <!-- Victim Equipment -->
                                <div class="relative">
                                    <div class="absolute -left-2 top-0 bottom-0 w-1 bg-gradient-to-b from-[#DC143C] to-transparent"></div>
                                    <h3 class="font-[Cinzel] text-base font-bold text-[#DC143C] mb-4 tracking-wide uppercase border-b border-[#DC143C]/30 pb-2">
                                        {{ $event->victim_name ?? 'Victim' }}'s Arsenal
                                    </h3>
                                    <x-equipment-grid
                                        :equipment="$event->event_data['Victim']['Equipment'] ?? []"
                                        :inventory="$event->event_data['Victim']['Inventory'] ?? []"
                                        title="Equipment"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="relative bg-gradient-to-r from-[#2D2D2D]/50 via-[#1A0A0A]/50 to-[#2D2D2D]/50 border-2 border-[#4A4A4A] p-16 text-center">
                <div class="absolute inset-0 combat-texture opacity-20"></div>
                <div class="relative z-10">
                    <!-- Empty state icon -->
                    <div class="inline-flex items-center justify-center w-24 h-24 mb-6">
                        <svg class="w-full h-full text-[#4A4A4A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="font-[Cinzel] text-xl font-bold text-[#E8DCC8]/60 mb-2 tracking-wide">
                        No Events Found
                    </h3>
                    <p class="font-[Space_Grotesk] text-sm text-[#E8DCC8]/40 max-w-md mx-auto">
                        Adjust your filters or wait for new kills to be tracked.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    @if($events->count() >= 50)
        <div class="text-center py-6">
            <div class="inline-flex items-center gap-3 px-6 py-3 bg-[#2D2D2D]/50 border border-[#4A4A4A]">
                <div class="flex gap-1">
                    <div class="w-1.5 h-1.5 bg-[#D4AF37] transform rotate-45"></div>
                    <div class="w-1.5 h-1.5 bg-[#DC143C] transform rotate-45"></div>
                    <div class="w-1.5 h-1.5 bg-[#D4AF37] transform rotate-45"></div>
                </div>
                <span class="font-[Space_Grotesk] text-sm text-[#E8DCC8]/50 tracking-wide">
                    Displaying 50 most recent battle records
                </span>
                <div class="flex gap-1">
                    <div class="w-1.5 h-1.5 bg-[#D4AF37] transform rotate-45"></div>
                    <div class="w-1.5 h-1.5 bg-[#DC143C] transform rotate-45"></div>
                    <div class="w-1.5 h-1.5 bg-[#D4AF37] transform rotate-45"></div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function toggleDetails(elementId) {
    const element = document.getElementById(elementId);
    const button = event.currentTarget;
    const toggleText = button.querySelector('.details-toggle-text');

    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        toggleText.textContent = 'Collapse ▲';
        // Add reveal animation
        element.style.animation = 'diagonal-slash 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards';
    } else {
        element.classList.add('hidden');
        toggleText.textContent = 'Expand ▼';
    }
}
</script>
@endsection
