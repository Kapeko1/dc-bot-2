@extends('layout')

@section('title', $player->name . ' - Player Stats')

@section('content')
@php
    use App\Helpers\ItemHelper;
@endphp
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('killboard.index') }}" class="text-sm text-gray-400 hover:text-gray-300 mb-2 inline-block">
                &larr; Back to Kill Feed
            </a>
            <h1 class="text-3xl font-bold text-white">{{ $player->name }}</h1>
            <p class="mt-2 text-gray-400">Player Statistics</p>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Kills -->
        <div class="bg-gray-900 rounded-lg border border-gray-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Kills</p>
                    <p class="mt-2 text-3xl font-bold text-green-400">{{ number_format($stats['total_kills']) }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-500/10 flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                {{ number_format($stats['total_kill_fame']) }} total fame
            </p>
        </div>

        <!-- Total Deaths -->
        <div class="bg-gray-900 rounded-lg border border-gray-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Deaths</p>
                    <p class="mt-2 text-3xl font-bold text-red-400">{{ number_format($stats['total_deaths']) }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-red-500/10 flex items-center justify-center">
                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                {{ number_format($stats['total_death_fame']) }} total fame
            </p>
        </div>

        <!-- K/D Ratio -->
        <div class="bg-gray-900 rounded-lg border border-gray-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">K/D Ratio</p>
                    <p class="mt-2 text-3xl font-bold text-blue-400">
                        {{ $stats['total_deaths'] > 0 ? number_format($stats['total_kills'] / $stats['total_deaths'], 2) : $stats['total_kills'] }}
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-500/10 flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Highest Fame Kill -->
        <div class="bg-gray-900 rounded-lg border border-gray-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Highest Fame Kill</p>
                    <p class="mt-2 text-3xl font-bold bg-gradient-to-r from-yellow-500 to-orange-500 bg-clip-text text-transparent">
                        {{ $stats['highest_fame_kill'] ? number_format($stats['highest_fame_kill']->total_fame) : '0' }}
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-yellow-500/10 flex items-center justify-center">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
            </div>
            @if($stats['highest_fame_kill'])
                <p class="mt-2 text-xs text-gray-500 truncate">
                    vs {{ $stats['highest_fame_kill']->victim_name }}
                </p>
            @endif
        </div>
    </div>

    <!-- Weapon Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Most Used Weapon (Kills) -->
        <div class="bg-gray-900 rounded-lg border border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Weapons Used (Kills)</h2>
            @if($stats['weapon_kills_breakdown']->count() > 0)
                <div class="space-y-3">
                    @foreach($stats['weapon_kills_breakdown'] as $weapon => $count)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <x-item-icon :item="$weapon" size="lg" />
                                    <span class="text-sm font-medium text-gray-300">{{ ItemHelper::formatItemName($weapon) }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $count }} kills</span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full"
                                     style="width: {{ ($count / $stats['weapon_kills_breakdown']->first()) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No kill data available</p>
            @endif
        </div>

        <!-- Most Used Weapon (Deaths) -->
        <div class="bg-gray-900 rounded-lg border border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Weapons Used (Deaths)</h2>
            @if($stats['weapon_deaths_breakdown']->count() > 0)
                <div class="space-y-3">
                    @foreach($stats['weapon_deaths_breakdown'] as $weapon => $count)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <x-item-icon :item="$weapon" size="lg" />
                                    <span class="text-sm font-medium text-gray-300">{{ ItemHelper::formatItemName($weapon) }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $count }} deaths</span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-2">
                                <div class="bg-gradient-to-r from-red-500 to-rose-500 h-2 rounded-full"
                                     style="width: {{ ($count / $stats['weapon_deaths_breakdown']->first()) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No death data available</p>
            @endif
        </div>
    </div>

    <!-- Recent Activity Tabs -->
    <div class="bg-gray-900 rounded-lg border border-gray-800 overflow-hidden">
        <div class="border-b border-gray-800">
            <nav class="flex -mb-px">
                <button onclick="showTab('kills')"
                        id="kills-tab"
                        class="tab-button flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors border-green-500 text-green-400">
                    Recent Kills ({{ $kills->count() }})
                </button>
                <button onclick="showTab('deaths')"
                        id="deaths-tab"
                        class="tab-button flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-300 hover:border-gray-300">
                    Recent Deaths ({{ $deaths->count() }})
                </button>
            </nav>
        </div>

        <!-- Kills Tab Content -->
        <div id="kills-content" class="tab-content p-6">
            <div class="space-y-3">
                @forelse($kills->take(10) as $kill)
                    <div class="border-l-4 border-green-500 bg-gray-800/50 rounded-r-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($kill->victim_weapon)
                                        <x-item-icon :item="$kill->victim_weapon" size="lg" />
                                    @endif
                                    <span class="text-white font-medium">{{ $kill->victim_name }}</span>
                                    @if($kill->victim_guild)
                                        <span class="text-xs text-gray-500">[{{ $kill->victim_guild }}]</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>{{ $kill->killed_at?->diffForHumans() }}</span>
                                    <span>IP: {{ $kill->killer_ip }} vs {{ $kill->victim_ip }}</span>
                                    @if($kill->participants_count > 1)
                                        <span>{{ $kill->participants_count }} participants</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-lg font-bold text-green-400">
                                    {{ number_format($kill->total_fame) }}
                                </div>
                                <div class="text-xs text-gray-500">Fame</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No kills recorded yet</p>
                @endforelse
            </div>
        </div>

        <!-- Deaths Tab Content -->
        <div id="deaths-content" class="tab-content p-6 hidden">
            <div class="space-y-3">
                @forelse($deaths->take(10) as $death)
                    <div class="border-l-4 border-red-500 bg-gray-800/50 rounded-r-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-sm text-gray-400">Killed by:</span>
                                    @if($death->killer_weapon)
                                        <x-item-icon :item="$death->killer_weapon" size="lg" />
                                    @endif
                                    <span class="text-white font-medium">{{ $death->killer_name }}</span>
                                    @if($death->killer_guild)
                                        <span class="text-xs text-gray-500">[{{ $death->killer_guild }}]</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>{{ $death->killed_at?->diffForHumans() }}</span>
                                    <span>IP: {{ $death->victim_ip }} vs {{ $death->killer_ip }}</span>
                                    @if($death->participants_count > 1)
                                        <span>{{ $death->participants_count }} participants</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-lg font-bold text-red-400">
                                    {{ number_format($death->total_fame) }}
                                </div>
                                <div class="text-xs text-gray-500">Fame</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No deaths recorded yet</p>
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
        button.classList.remove('border-green-500', 'text-green-400', 'border-red-500', 'text-red-400');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active styles to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    if (tabName === 'kills') {
        activeTab.classList.add('border-green-500', 'text-green-400');
    } else {
        activeTab.classList.add('border-red-500', 'text-red-400');
    }
}
</script>
@endsection
