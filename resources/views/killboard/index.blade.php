@extends('layout')

@section('title', 'Kill Log - DC Killboard')

@section('content')
<div class="space-y-8">
    <!-- Header with dramatic styling -->
    <div class="relative">
        <div class="absolute -left-2 sm:-left-4 top-0 bottom-0 w-0.5 sm:w-1 bg-gradient-to-b from-[#DC143C] via-[#D4AF37] to-transparent transform -skew-y-12"></div>
        <div class="pl-4 sm:pl-6">
            <h1 class="font-[Cinzel] text-3xl sm:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#DC143C] via-[#EF4444] to-[#D4AF37] tracking-wide slash-accent animate-on-scroll">
                KILL LOG
            </h1>
        </div>
    </div>

    <!-- Filters with refined brutalist design -->
    <div class="relative metal-gradient border-2 border-[#DC143C]/30 p-4 sm:p-6 animate-on-scroll stagger-2 combat-texture overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#DC143C]/20 to-transparent blur-2xl"></div>
        <form method="GET" action="{{ route('killboard.index') }}" class="relative z-10 flex flex-col sm:flex-row gap-3 sm:gap-4">
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
                    <option value="fame" {{ $sortBy === 'fame' ? 'selected' : '' }}>Highest Fame</option>
                </select>
            </div>

            <div class="flex items-end gap-2 sm:gap-3">
                <button type="submit"
                        class="relative px-4 sm:px-6 py-3 bg-gradient-to-r from-[#DC143C] to-[#8B0000] text-[#E8DCC8] font-[Space_Grotesk] text-sm sm:text-base font-semibold tracking-wide uppercase border-2 border-[#DC143C] hover:border-[#D4AF37] transition-all duration-300 overflow-hidden group">
                    <span class="relative z-10">Apply</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#D4AF37] to-[#8B7500] transform translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                </button>
                @if($playerFilter || $sortBy !== 'recent')
                    <a href="{{ route('killboard.index') }}"
                       class="px-4 sm:px-6 py-3 bg-[#2D2D2D] hover:bg-[#4A4A4A] text-[#E8DCC8] font-[Space_Grotesk] text-sm sm:text-base font-medium tracking-wide uppercase border-2 border-[#4A4A4A] hover:border-[#DC143C]/50 transition-all duration-300">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Daily Statistics Graph -->
    <div class="relative metal-gradient border-2 border-[#4A4A4A] p-4 sm:p-6 animate-on-scroll stagger-3 combat-texture overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#10B981]/10 to-transparent blur-2xl"></div>
        <div class="relative z-10">
            <h2 class="font-[Cinzel] text-base sm:text-xl font-bold text-[#E8DCC8] mb-4 sm:mb-6 tracking-wide flex items-center gap-2 sm:gap-3">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#D4AF37] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm sm:text-xl">Daily Activity (Last 14 Days)</span>
            </h2>
            <div class="bg-[#0A0A0A]/80 border-2 border-[#2D2D2D] p-2 sm:p-4">
                <div class="h-40 sm:h-64">
                    <canvas id="dailyStatsChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracked Players with medieval badge design -->
    <div class="relative bg-gradient-to-r from-[#2D2D2D]/50 via-[#1A0A0A]/50 to-[#2D2D2D]/50 border-l-4 border-[#D4AF37] p-4 sm:p-6 animate-on-scroll stagger-4">
        <h2 class="font-[Cinzel] text-xs sm:text-sm font-bold text-[#D4AF37] mb-3 sm:mb-4 tracking-[0.2em] sm:tracking-[0.3em] uppercase">Tracked Players</h2>
        <div class="flex flex-wrap gap-2 sm:gap-3">
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
            <div class="group relative bg-gradient-to-r from-[#2D2D2D]/90 via-[#1A0A0A]/90 to-[#2D2D2D]/90 border-l-2 sm:border-l-4 {{ $event->event_type === 'kill' ? 'border-[#10B981]' : 'border-[#DC143C]' }} hover:border-[#D4AF37] transition-all duration-500 overflow-hidden cursor-pointer animate-on-scroll combat-texture" onclick="toggleDetails('event-{{ $event->id }}')" style="animation-delay: {{ $loop->index * 0.05 }}s">
                <!-- Dramatic diagonal accent -->
                <div class="absolute top-0 right-0 w-32 h-full bg-gradient-to-l from-{{ $event->event_type === 'kill' ? '[#10B981]' : '[#DC143C]' }}/10 to-transparent transform skew-x-12 group-hover:w-48 transition-all duration-500"></div>

                <div class="relative z-10 p-3 sm:p-5">
                    <div class="flex flex-col sm:flex-row items-start justify-between gap-3 sm:gap-6">
                        <!-- Main Info -->
                        <div class="flex-1 min-w-0 w-full">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 mb-3 sm:mb-4">
                                @if($event->event_type === 'kill')
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-[#10B981] blur-md opacity-50"></div>
                                        <span class="relative inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-[#10B981] text-[#E8DCC8] font-[Space_Grotesk] text-[0.65rem] sm:text-xs font-bold tracking-wide sm:tracking-widest uppercase border border-[#10B981]">
                                            ⚔ KILL
                                        </span>
                                    </div>
                                @else
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-[#DC143C] blur-md opacity-50"></div>
                                        <span class="relative inline-flex items-center px-2 sm:px-3 py-1 sm:py-1.5 bg-[#DC143C] text-[#E8DCC8] font-[Space_Grotesk] text-[0.65rem] sm:text-xs font-bold tracking-wide sm:tracking-widest uppercase border border-[#DC143C]">
                                            ☠ DEATH
                                        </span>
                                    </div>
                                @endif
                                <span class="font-[JetBrains_Mono] text-[0.65rem] sm:text-xs text-[#E8DCC8]/50 tracking-wide">
                                    {{ $event->killed_at?->diffForHumans() ?? 'Unknown time' }}
                                </span>
                            </div>

                            <div class="space-y-2 sm:space-y-3">
                                <!-- Killer -->
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <span class="font-[Space_Grotesk] text-[0.65rem] sm:text-xs text-[#D4AF37] uppercase tracking-wide sm:tracking-widest min-w-[50px] sm:min-w-[60px]">Killer:</span>
                                    @if($event->killer_weapon)
                                        <div class="flex-shrink-0">
                                            <x-item-icon :item="$event->killer_weapon" size="lg" />
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                        <span class="font-[Space_Grotesk] text-[#E8DCC8] font-semibold text-sm sm:text-base">{{ $event->killer_name ?? 'Unknown' }}</span>
                                        @if($event->killer_guild)
                                            <span class="font-[JetBrains_Mono] text-[0.6rem] sm:text-xs text-[#E8DCC8]/50 border border-[#4A4A4A] px-1.5 sm:px-2 py-0.5 bg-[#0A0A0A]/50">[{{ $event->killer_guild }}]</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- VS Divider -->
                                <div class="flex items-center gap-2 sm:gap-3 pl-[50px] sm:pl-[60px]">
                                    <div class="h-px flex-1 bg-gradient-to-r from-[#DC143C] via-[#4A4A4A] to-transparent"></div>
                                    <span class="font-[Cinzel] text-[0.65rem] sm:text-xs text-[#DC143C] font-bold">VS</span>
                                    <div class="h-px flex-1 bg-gradient-to-l from-[#DC143C] via-[#4A4A4A] to-transparent"></div>
                                </div>

                                <!-- Victim -->
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <span class="font-[Space_Grotesk] text-[0.65rem] sm:text-xs text-[#DC143C]/80 uppercase tracking-wide sm:tracking-widest min-w-[50px] sm:min-w-[60px]">Victim:</span>
                                    @if($event->victim_weapon)
                                        <div class="flex-shrink-0">
                                            <x-item-icon :item="$event->victim_weapon" size="lg" />
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                        <span class="font-[Space_Grotesk] text-[#E8DCC8]/90 font-medium text-sm sm:text-base">{{ $event->victim_name ?? 'Unknown' }}</span>
                                        @if($event->victim_guild)
                                            <span class="font-[JetBrains_Mono] text-[0.6rem] sm:text-xs text-[#E8DCC8]/50 border border-[#4A4A4A] px-1.5 sm:px-2 py-0.5 bg-[#0A0A0A]/50">[{{ $event->victim_guild }}]</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Battle Info -->
                                <div class="flex flex-wrap items-center gap-3 sm:gap-6 font-[JetBrains_Mono] text-[0.6rem] sm:text-xs text-[#E8DCC8]/40 mt-2 sm:mt-3 pl-[50px] sm:pl-[60px]">
                                    <span class="tracking-wider">IP: {{ $event->killer_ip }} ⚔ {{ $event->victim_ip }}</span>
                                    @if($event->participants_count > 1)
                                        <span class="tracking-wider">PLAYERS: {{ $event->participants_count }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Fame Badge - Tiered Effects -->
                        @php
                            $fame = $event->total_fame;
                            // Tier 1: < 100k (Standard gold)
                            // Tier 2: 100k - 300k (Animated gold with glow)
                            // Tier 3: 300k+ (Epic multi-color with intense effects)
                            $isTier3 = $fame >= 300000;
                            $isTier2 = $fame >= 100000 && $fame < 300000;
                            $isTier1 = $fame < 100000;
                        @endphp
                        <div class="flex flex-col items-end ml-2 sm:ml-6 flex-shrink-0">
                            <div class="relative">
                                @if($isTier3)
                                    <!-- Tier 3: Epic 300k+ - Intense multi-glow -->
                                    <div class="absolute inset-0 bg-gradient-to-br from-[#FF6B35] via-[#D4AF37] to-[#9333EA] blur-xl opacity-70 animate-pulse"></div>
                                    <div class="absolute -inset-2 bg-gradient-to-br from-[#FF6B35] via-[#D4AF37] to-[#9333EA] blur-2xl opacity-40 animate-pulse" style="animation-delay: 150ms;"></div>
                                    <div class="relative bg-gradient-to-br from-[#2D2D2D] to-[#1A0A0A] border-2 sm:border-4 border-[#D4AF37] p-2 sm:p-4 transform -rotate-3 shadow-2xl shadow-[#D4AF37]/50">
                                        <div class="transform rotate-3">
                                            <div class="font-[Cinzel] text-lg sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#FF6B35] via-[#D4AF37] to-[#9333EA] leading-none animate-pulse">
                                                {{ number_format($event->total_fame) }}
                                            </div>
                                            <div class="font-[Space_Grotesk] text-[0.55rem] sm:text-[0.65rem] text-[#D4AF37] uppercase tracking-[0.15em] sm:tracking-[0.2em] mt-0.5 sm:mt-1 text-center">Fame</div>
                                        </div>
                                    </div>
                                @elseif($isTier2)
                                    <!-- Tier 2: 100k-300k - Animated gold glow -->
                                    <div class="absolute inset-0 bg-gradient-to-br from-[#D4AF37] to-[#8B7500] blur-lg opacity-50 animate-pulse"></div>
                                    <div class="absolute -inset-1 bg-gradient-to-br from-[#D4AF37] to-[#FF6B35] blur-md opacity-30"></div>
                                    <div class="relative bg-gradient-to-br from-[#2D2D2D] to-[#1A0A0A] border-2 border-[#D4AF37] sm:border-4 sm:border-double p-2 sm:p-4 transform -rotate-3 shadow-xl shadow-[#D4AF37]/30">
                                        <div class="transform rotate-3">
                                            <div class="font-[Cinzel] text-lg sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#FFD700] via-[#D4AF37] to-[#FF6B35] leading-none">
                                                {{ number_format($event->total_fame) }}
                                            </div>
                                            <div class="font-[Space_Grotesk] text-[0.55rem] sm:text-[0.65rem] text-[#D4AF37] uppercase tracking-[0.15em] sm:tracking-[0.2em] mt-0.5 sm:mt-1 text-center">Fame</div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Tier 1: < 100k - Standard -->
                                    <div class="absolute inset-0 bg-gradient-to-br from-[#D4AF37] to-[#8B7500] blur-md sm:blur-lg opacity-30"></div>
                                    <div class="relative bg-gradient-to-br from-[#2D2D2D] to-[#1A0A0A] border border-[#D4AF37] sm:border-2 p-2 sm:p-4 transform -rotate-3">
                                        <div class="transform rotate-3">
                                            <div class="font-[Cinzel] text-lg sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#D4AF37] via-[#F4EFE3] to-[#8B7500] leading-none">
                                                {{ number_format($event->total_fame) }}
                                            </div>
                                            <div class="font-[Space_Grotesk] text-[0.55rem] sm:text-[0.65rem] text-[#D4AF37] uppercase tracking-[0.15em] sm:tracking-[0.2em] mt-0.5 sm:mt-1 text-center">Fame</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expandable Details - Arsenal Display -->
                @if($event->event_data)
                    <div id="event-{{ $event->id }}" class="hidden border-t border-[#DC143C]/20 sm:border-t-2">
                        <div class="bg-gradient-to-b from-[#0A0A0A]/90 to-[#1A0A0A]/90 p-3 sm:p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
                                <!-- Killer Equipment -->
                                <div class="relative">
                                    <div class="absolute -left-1.5 sm:-left-2 top-0 bottom-0 w-0.5 sm:w-1 bg-gradient-to-b from-[#10B981] to-transparent"></div>
                                    <h3 class="font-[Cinzel] text-sm sm:text-base font-bold text-[#10B981] mb-3 sm:mb-4 tracking-wide uppercase border-b border-[#10B981]/30 pb-1.5 sm:pb-2">
                                        {{ $event->killer_name ?? 'Killer' }}'s Inv
                                    </h3>
                                    <x-equipment-grid
                                        :equipment="$event->event_data['Killer']['Equipment'] ?? []"
                                        :inventory="$event->event_data['Killer']['Inventory'] ?? []"
                                        title="Equipment"
                                    />
                                </div>

                                <!-- Victim Equipment -->
                                <div class="relative">
                                    <div class="absolute -left-1.5 sm:-left-2 top-0 bottom-0 w-0.5 sm:w-1 bg-gradient-to-b from-[#DC143C] to-transparent"></div>
                                    <h3 class="font-[Cinzel] text-sm sm:text-base font-bold text-[#DC143C] mb-3 sm:mb-4 tracking-wide uppercase border-b border-[#DC143C]/30 pb-1.5 sm:pb-2">
                                        {{ $event->victim_name ?? 'Victim' }}'s Inv
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

    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        // Add reveal animation
        element.style.animation = 'diagonal-slash 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards';
    } else {
        element.classList.add('hidden');
    }
}

// Initialize daily stats chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('dailyStatsChart');
    if (!ctx) return;

    const dates = @json($dailyStats['dates']);
    const kills = @json($dailyStats['kills']);
    const deaths = @json($dailyStats['deaths']);

    // Format dates for display (show day of month)
    const labels = dates.map(date => {
        const d = new Date(date);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Kills',
                    data: kills,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#000',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Deaths',
                    data: deaths,
                    borderColor: '#DC143C',
                    backgroundColor: 'rgba(220, 20, 60, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#DC143C',
                    pointBorderColor: '#000',
                    pointBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#B8AC98',
                        font: {
                            family: "'Space Grotesk', sans-serif",
                            size: 12,
                            weight: 600
                        },
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#0A0A0A',
                    titleColor: '#E8DCC8',
                    bodyColor: '#B8AC98',
                    borderColor: '#4A4A4A',
                    borderWidth: 2,
                    padding: 12,
                    titleFont: {
                        family: "'Cinzel', serif",
                        size: 14,
                        weight: 700
                    },
                    bodyFont: {
                        family: "'Space Grotesk', sans-serif",
                        size: 13
                    },
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#B8AC98',
                        font: {
                            family: "'JetBrains Mono', monospace",
                            size: 11
                        },
                        precision: 0
                    },
                    grid: {
                        color: '#2D2D2D',
                        drawBorder: false
                    },
                    border: {
                        display: false
                    }
                },
                x: {
                    ticks: {
                        color: '#B8AC98',
                        font: {
                            family: "'JetBrains Mono', monospace",
                            size: 10
                        },
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        color: '#2D2D2D',
                        drawBorder: false
                    },
                    border: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endsection
