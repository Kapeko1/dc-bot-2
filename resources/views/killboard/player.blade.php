@extends('layout')

@section('title', $player->name . ' - Player Stats')

@section('content')
@php
    use App\Helpers\ItemHelper;
@endphp
<div class="space-y-8">
    <!-- Header with Heraldic Design -->
    <div class="relative">
        <a href="{{ route('killboard.index') }}"
           class="inline-flex items-center gap-2 font-[Space_Grotesk] text-sm text-[#D4AF37] hover:text-[#E8DCC8] mb-6 transition-colors group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Return to Kill Log
        </a>

        <div class="flex items-start gap-6">
            <!-- Heraldic shield emblem -->
            <div class="relative w-20 h-24 flex-shrink-0">
                <div class="absolute inset-0 bg-gradient-to-br from-[#DC143C] to-[#8B0000] border-2 border-[#D4AF37]" style="clip-path: polygon(50% 0%, 100% 25%, 100% 85%, 50% 100%, 0% 85%, 0% 25%)"></div>
                <div class="absolute inset-2 bg-[#0A0A0A] flex items-center justify-center" style="clip-path: polygon(50% 0%, 100% 25%, 100% 85%, 50% 100%, 0% 85%, 0% 25%)">
                    <span class="font-[Cinzel] text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-b from-[#D4AF37] to-[#DC143C]">
                        {{ substr($player->name, 0, 1) }}
                    </span>
                </div>
                <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-16 h-0.5 bg-gradient-to-r from-transparent via-[#D4AF37] to-transparent"></div>
            </div>

            <div class="flex-1">
                <div class="flex items-center gap-4 mb-2">
                    <h1 class="font-[Cinzel] text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#E8DCC8] via-[#F4EFE3] to-[#D4AF37] tracking-wide">
                        {{ $player->name }}
                    </h1>
                    <div class="w-1 h-8 bg-gradient-to-b from-[#DC143C] to-transparent transform -skew-x-12"></div>
                </div>
                <p class="font-[Space_Grotesk] text-sm text-[#D4AF37] uppercase tracking-[0.3em]">
                    Player Stats
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics Grid - Dramatic Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Kills Card -->
        <div class="group relative bg-gradient-to-br from-[#2D2D2D] to-[#1A0A0A] border-2 border-[#10B981] p-6 overflow-hidden animate-on-scroll">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#10B981]/20 to-transparent blur-2xl group-hover:w-40 group-hover:h-40 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="font-[Space_Grotesk] text-xs font-semibold text-[#10B981] uppercase tracking-widest mb-3">Kills</p>
                        <p class="font-[Cinzel] text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#10B981] via-[#34D399] to-[#059669]">
                            {{ number_format($stats['total_kills']) }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-[#10B981]/10 border border-[#10B981]/30 flex items-center justify-center transform -rotate-12 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="w-7 h-7 text-[#10B981]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L3 7v6c0 5.5 3.8 10.7 9 12 5.2-1.3 9-6.5 9-12V7l-9-5zm-1 15l-4-4 1.4-1.4L11 14.2l5.6-5.6L18 10l-7 7z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-[#10B981] to-transparent"></div>
                    <span class="font-[JetBrains_Mono] text-xs text-[#10B981]/70 tracking-wider">
                        {{ number_format($stats['total_kill_fame']) }} FAME
                    </span>
                </div>
            </div>
        </div>

        <!-- Total Deaths Card -->
        <div class="group relative bg-gradient-to-br from-[#2D2D2D] to-[#1A0A0A] border-2 border-[#DC143C] p-6 overflow-hidden animate-on-scroll stagger-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#DC143C]/20 to-transparent blur-2xl group-hover:w-40 group-hover:h-40 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="font-[Space_Grotesk] text-xs font-semibold text-[#DC143C] uppercase tracking-widest mb-3">Deaths</p>
                        <p class="font-[Cinzel] text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#DC143C] via-[#EF4444] to-[#991B1B]">
                            {{ number_format($stats['total_deaths']) }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-[#DC143C]/10 border border-[#DC143C]/30 flex items-center justify-center transform -rotate-12 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="w-7 h-7 text-[#DC143C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-[#DC143C] to-transparent"></div>
                    <span class="font-[JetBrains_Mono] text-xs text-[#DC143C]/70 tracking-wider">
                        {{ number_format($stats['total_death_fame']) }} LOST
                    </span>
                </div>
            </div>
        </div>

        <!-- K/D Ratio - Balance Card -->
        <div class="group relative bg-gradient-to-br from-[#2D2D2D] to-[#1A0A0A] border-2 border-[#4A4A4A] p-6 overflow-hidden animate-on-scroll stagger-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#E8DCC8]/10 to-transparent blur-2xl group-hover:w-40 group-hover:h-40 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="font-[Space_Grotesk] text-xs font-semibold text-[#E8DCC8]/70 uppercase tracking-widest mb-3">Ratio</p>
                        <p class="font-[Cinzel] text-4xl font-bold text-[#E8DCC8]">
                            {{ $stats['total_deaths'] > 0 ? number_format($stats['total_kills'] / $stats['total_deaths'], 2) : $stats['total_kills'] }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-[#E8DCC8]/10 border border-[#E8DCC8]/20 flex items-center justify-center transform -rotate-12 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="w-7 h-7 text-[#E8DCC8]/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-[#E8DCC8]/30 to-transparent"></div>
                    <span class="font-[JetBrains_Mono] text-xs text-[#E8DCC8]/50 tracking-wider">
                        K/D BALANCE
                    </span>
                </div>
            </div>
        </div>

        <!-- Highest Fame - Legendary Card -->
        <div class="group relative bg-gradient-to-br from-[#2D2D2D] to-[#1A0A0A] border-2 border-[#D4AF37] p-6 overflow-hidden animate-on-scroll stagger-3">
            <div class="absolute inset-0 bg-gradient-to-br from-[#D4AF37]/5 to-transparent"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#D4AF37]/30 to-transparent blur-2xl group-hover:w-40 group-hover:h-40 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="font-[Space_Grotesk] text-xs font-semibold text-[#D4AF37] uppercase tracking-widest mb-3">Legendary</p>
                        <p class="font-[Cinzel] text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#D4AF37] via-[#F4EFE3] to-[#D4AF37]">
                            {{ $stats['highest_fame_kill'] ? number_format($stats['highest_fame_kill']->total_fame) : '0' }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-[#D4AF37]/10 border border-[#D4AF37]/30 flex items-center justify-center transform -rotate-12 group-hover:rotate-12 group-hover:scale-110 transition-all duration-500">
                        <svg class="w-7 h-7 text-[#D4AF37]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7-6.3-4.6-6.3 4.6 2.3-7-6-4.6h7.6z"/>
                        </svg>
                    </div>
                </div>
                @if($stats['highest_fame_kill'])
                    <div class="flex items-center gap-2">
                        <div class="h-px flex-1 bg-gradient-to-r from-[#D4AF37] to-transparent"></div>
                        <span class="font-[JetBrains_Mono] text-xs text-[#D4AF37]/70 tracking-wider truncate">
                            VS {{ strtoupper($stats['highest_fame_kill']->victim_name) }}
                        </span>
                    </div>
                @else
                    <div class="h-px bg-gradient-to-r from-[#D4AF37]/30 to-transparent"></div>
                @endif
            </div>
        </div>
    </div>

    <!-- Weapon Arsenal Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Weapons Used (Kills) -->
        <div class="relative bg-gradient-to-br from-[#2D2D2D]/80 to-[#1A0A0A]/80 border-l-4 border-[#10B981] p-6 combat-texture animate-on-scroll stagger-4">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#10B981]/10 to-transparent blur-2xl"></div>
            <h2 class="relative z-10 font-[Cinzel] text-xl font-bold text-[#10B981] mb-6 tracking-wide flex items-center gap-3">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L3 7v6c0 5.5 3.8 10.7 9 12 5.2-1.3 9-6.5 9-12V7l-9-5z"/>
                </svg>
                Weapons Used (Kills)
            </h2>
            @if($stats['weapon_kills_breakdown']->count() > 0)
                <div class="space-y-4">
                    @foreach($stats['weapon_kills_breakdown'] as $weapon => $count)
                        <div class="group relative">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <x-item-icon :item="$weapon" size="lg" />
                                    <span class="font-[Space_Grotesk] text-sm font-medium text-[#E8DCC8] group-hover:text-[#10B981] transition-colors">
                                        {{ ItemHelper::formatItemName($weapon) }}
                                    </span>
                                </div>
                                <span class="font-[JetBrains_Mono] text-sm text-[#10B981]/70">{{ $count }}</span>
                            </div>
                            <div class="relative h-3 bg-[#0A0A0A]/50 border border-[#4A4A4A]/30 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-[#10B981] to-[#059669] transition-all duration-700 ease-out"
                                     style="width: {{ ($count / $stats['weapon_kills_breakdown']->first()) * 100 }}%">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="font-[Space_Grotesk] text-[#E8DCC8]/40 text-sm text-center py-8">No kills recorded yet</p>
            @endif
        </div>

        <!-- Weapons Used (Deaths) -->
        <div class="relative bg-gradient-to-br from-[#2D2D2D]/80 to-[#1A0A0A]/80 border-l-4 border-[#DC143C] p-6 combat-texture animate-on-scroll stagger-5">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-[#DC143C]/10 to-transparent blur-2xl"></div>
            <h2 class="relative z-10 font-[Cinzel] text-xl font-bold text-[#DC143C] mb-6 tracking-wide flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Weapons Used (Deaths)
            </h2>
            @if($stats['weapon_deaths_breakdown']->count() > 0)
                <div class="space-y-4">
                    @foreach($stats['weapon_deaths_breakdown'] as $weapon => $count)
                        <div class="group relative">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <x-item-icon :item="$weapon" size="lg" />
                                    <span class="font-[Space_Grotesk] text-sm font-medium text-[#E8DCC8] group-hover:text-[#DC143C] transition-colors">
                                        {{ ItemHelper::formatItemName($weapon) }}
                                    </span>
                                </div>
                                <span class="font-[JetBrains_Mono] text-sm text-[#DC143C]/70">{{ $count }}</span>
                            </div>
                            <div class="relative h-3 bg-[#0A0A0A]/50 border border-[#4A4A4A]/30 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-[#DC143C] to-[#8B0000] transition-all duration-700 ease-out"
                                     style="width: {{ ($count / $stats['weapon_deaths_breakdown']->first()) * 100 }}%">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="font-[Space_Grotesk] text-[#E8DCC8]/40 text-sm text-center py-8">No deaths recorded yet</p>
            @endif
        </div>
    </div>

    <!-- Recent Battle Records - Tabbed Interface -->
    <div class="relative bg-gradient-to-r from-[#2D2D2D]/80 via-[#1A0A0A]/80 to-[#2D2D2D]/80 border-2 border-[#4A4A4A] overflow-hidden">
        <!-- Tab Headers -->
        <div class="relative border-b-2 border-[#4A4A4A]">
            <nav class="flex">
                <button onclick="showTab('kills')"
                        id="kills-tab"
                        class="tab-button relative flex-1 py-5 font-[Space_Grotesk] text-sm font-semibold tracking-wider uppercase transition-all duration-300 border-[#10B981] text-[#10B981]">
                    <span class="relative z-10">Kills ({{ $kills->count() }})</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#10B981]/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#10B981] to-transparent"></div>
                </button>
                <button onclick="showTab('deaths')"
                        id="deaths-tab"
                        class="tab-button relative flex-1 py-5 font-[Space_Grotesk] text-sm font-semibold tracking-wider uppercase transition-all duration-300 border-transparent text-[#E8DCC8]/50 hover:text-[#DC143C]">
                    <span class="relative z-10">Deaths ({{ $deaths->count() }})</span>
                </button>
            </nav>
        </div>

        <!-- Kills Tab Content -->
        <div id="kills-content" class="tab-content p-6 bg-gradient-to-b from-[#0A0A0A]/50 to-transparent">
            <div class="space-y-3">
                @forelse($kills->take(10) as $kill)
                    <div class="group relative border-l-4 border-[#10B981] bg-[#2D2D2D]/50 hover:bg-[#2D2D2D]/80 p-5 transition-all duration-300">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    @if($kill->victim_weapon)
                                        <x-item-icon :item="$kill->victim_weapon" size="lg" />
                                    @endif
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-[Space_Grotesk] text-[#E8DCC8] font-semibold">{{ $kill->victim_name }}</span>
                                        @if($kill->victim_guild)
                                            <span class="font-[JetBrains_Mono] text-xs text-[#E8DCC8]/50 border border-[#4A4A4A] px-2 py-0.5 bg-[#0A0A0A]/50">[{{ $kill->victim_guild }}]</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 font-[JetBrains_Mono] text-xs text-[#E8DCC8]/40">
                                    <span>{{ $kill->killed_at?->diffForHumans() }}</span>
                                    <span>IP: {{ $kill->killer_ip }} ⚔ {{ $kill->victim_ip }}</span>
                                    @if($kill->participants_count > 1)
                                        <span>PLAYERS: {{ $kill->participants_count }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-[Cinzel] text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#10B981] to-[#059669]">
                                    {{ number_format($kill->total_fame) }}
                                </div>
                                <div class="font-[Space_Grotesk] text-xs text-[#10B981]/70 uppercase tracking-wider">Fame</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="font-[Space_Grotesk] text-[#E8DCC8]/40 text-center py-12">No kills recorded yet</p>
                @endforelse
            </div>
        </div>

        <!-- Deaths Tab Content -->
        <div id="deaths-content" class="tab-content p-6 bg-gradient-to-b from-[#0A0A0A]/50 to-transparent hidden">
            <div class="space-y-3">
                @forelse($deaths->take(10) as $death)
                    <div class="group relative border-l-4 border-[#DC143C] bg-[#2D2D2D]/50 hover:bg-[#2D2D2D]/80 p-5 transition-all duration-300">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="font-[Space_Grotesk] text-xs text-[#DC143C]/80 uppercase tracking-wider">Slain by:</span>
                                    @if($death->killer_weapon)
                                        <x-item-icon :item="$death->killer_weapon" size="lg" />
                                    @endif
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-[Space_Grotesk] text-[#E8DCC8] font-semibold">{{ $death->killer_name }}</span>
                                        @if($death->killer_guild)
                                            <span class="font-[JetBrains_Mono] text-xs text-[#E8DCC8]/50 border border-[#4A4A4A] px-2 py-0.5 bg-[#0A0A0A]/50">[{{ $death->killer_guild }}]</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 font-[JetBrains_Mono] text-xs text-[#E8DCC8]/40">
                                    <span>{{ $death->killed_at?->diffForHumans() }}</span>
                                    <span>IP: {{ $death->victim_ip }} ⚔ {{ $death->killer_ip }}</span>
                                    @if($death->participants_count > 1)
                                        <span>PLAYERS: {{ $death->participants_count }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-[Cinzel] text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-[#DC143C] to-[#991B1B]">
                                    {{ number_format($death->total_fame) }}
                                </div>
                                <div class="font-[Space_Grotesk] text-xs text-[#DC143C]/70 uppercase tracking-wider">Lost</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="font-[Space_Grotesk] text-[#E8DCC8]/40 text-center py-12">No deaths recorded yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active styles from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-[#10B981]', 'text-[#10B981]', 'border-[#DC143C]', 'text-[#DC143C]');
        button.classList.add('border-transparent', 'text-[#E8DCC8]/50');
        // Remove active background
        const bgDiv = button.querySelector('div:not(.absolute.bottom-0)');
        if (bgDiv) bgDiv.classList.add('hidden');
        const lineDiv = button.querySelector('.absolute.bottom-0');
        if (lineDiv) lineDiv.classList.add('hidden');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active styles to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.remove('border-transparent', 'text-[#E8DCC8]/50');

    if (tabName === 'kills') {
        activeTab.classList.add('border-[#10B981]', 'text-[#10B981]');
    } else {
        activeTab.classList.add('border-[#DC143C]', 'text-[#DC143C]');
    }

    // Show active background and line
    const bgDiv = activeTab.querySelector('div:not(.absolute.bottom-0)');
    if (bgDiv) bgDiv.classList.remove('hidden');
    const lineDiv = activeTab.querySelector('.absolute.bottom-0');
    if (lineDiv) {
        lineDiv.classList.remove('hidden');
        if (tabName === 'deaths') {
            lineDiv.classList.remove('from-transparent', 'via-[#10B981]', 'to-transparent');
            lineDiv.classList.add('from-transparent', 'via-[#DC143C]', 'to-transparent');
        }
    }
}
</script>
@endsection
