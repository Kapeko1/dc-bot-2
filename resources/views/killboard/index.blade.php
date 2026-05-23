@extends('layout')

@section('title', 'Kill Feed - DC Killboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-white">Kill Feed</h1>
        <p class="mt-2 text-gray-400">Recent kills and deaths from tracked players</p>
    </div>

    <!-- Filters -->
    <div class="bg-gray-900 rounded-lg border border-gray-800 p-4">
        <form method="GET" action="{{ route('killboard.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="player" class="block text-sm font-medium text-gray-400 mb-1">Filter by Player</label>
                <input type="text"
                       name="player"
                       id="player"
                       value="{{ $playerFilter }}"
                       placeholder="Enter player name..."
                       class="w-full rounded-md bg-gray-800 border-gray-700 text-white placeholder-gray-500 focus:border-red-500 focus:ring-red-500">
            </div>

            <div class="w-full sm:w-48">
                <label for="sort" class="block text-sm font-medium text-gray-400 mb-1">Sort By</label>
                <select name="sort"
                        id="sort"
                        class="w-full rounded-md bg-gray-800 border-gray-700 text-white focus:border-red-500 focus:ring-red-500">
                    <option value="recent" {{ $sortBy === 'recent' ? 'selected' : '' }}>Most Recent</option>
                    <option value="fame" {{ $sortBy === 'fame' ? 'selected' : '' }}>Highest Fame</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium transition-colors">
                    Apply Filters
                </button>
                @if($playerFilter || $sortBy !== 'recent')
                    <a href="{{ route('killboard.index') }}"
                       class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md font-medium transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tracked Players Quick Links -->
    <div class="bg-gray-900 rounded-lg border border-gray-800 p-4">
        <h2 class="text-sm font-semibold text-gray-400 mb-3">TRACKED PLAYERS</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($players as $player)
                <a href="{{ route('killboard.player', $player->albion_id) }}"
                   class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-md text-sm font-medium text-gray-300 transition-colors">
                    {{ $player->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Events List -->
    <div class="space-y-3">
        @forelse($events as $event)
            <div class="bg-gray-900 rounded-lg border border-gray-800 hover:border-gray-700 transition-colors overflow-hidden cursor-pointer" onclick="toggleDetails('event-{{ $event->id }}')">
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <!-- Main Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                @if($event->event_type === 'kill')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-500/10 text-green-400 border border-green-500/20">
                                        KILL
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
                                        DEATH
                                    </span>
                                @endif
                                <span class="text-sm text-gray-500">
                                    {{ $event->killed_at?->diffForHumans() ?? 'Unknown time' }}
                                </span>
                                <span class="ml-auto text-xs text-gray-400 details-toggle-text">Show Details ▼</span>
                            </div>

                            <div class="space-y-1">
                                <!-- Killer -->
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400">Killer:</span>
                                    @if($event->killer_weapon)
                                        <x-item-icon :item="$event->killer_weapon" size="lg" />
                                    @endif
                                    <span class="text-white font-medium">{{ $event->killer_name ?? 'Unknown' }}</span>
                                    @if($event->killer_guild)
                                        <span class="text-xs text-gray-500">[{{ $event->killer_guild }}]</span>
                                    @endif
                                </div>

                                <!-- Victim -->
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400">Victim:</span>
                                    @if($event->victim_weapon)
                                        <x-item-icon :item="$event->victim_weapon" size="lg" />
                                    @endif
                                    <span class="text-white font-medium">{{ $event->victim_name ?? 'Unknown' }}</span>
                                    @if($event->victim_guild)
                                        <span class="text-xs text-gray-500">[{{ $event->victim_guild }}]</span>
                                    @endif
                                </div>

                                <!-- Additional Info -->
                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-2">
                                    <span>IP: {{ $event->killer_ip }} vs {{ $event->victim_ip }}</span>
                                    @if($event->participants_count > 1)
                                        <span>{{ $event->participants_count }} participants</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Fame Badge -->
                        <div class="flex flex-col items-end ml-4">
                            <div class="text-2xl font-bold bg-gradient-to-r from-yellow-500 to-orange-500 bg-clip-text text-transparent">
                                {{ number_format($event->total_fame) }}
                            </div>
                            <div class="text-xs text-gray-500">Fame</div>
                        </div>
                    </div>
                </div>

                <!-- Expandable Details -->
                @if($event->event_data)
                    <div id="event-{{ $event->id }}" class="hidden border-t border-gray-800 bg-gray-950/50">
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Killer Equipment -->
                                <div>
                                    <h3 class="text-sm font-semibold text-green-400 mb-3">{{ $event->killer_name ?? 'Killer' }}</h3>
                                    <x-equipment-grid
                                        :equipment="$event->event_data['Killer']['Equipment'] ?? []"
                                        :inventory="$event->event_data['Killer']['Inventory'] ?? []"
                                        title="Equipment"
                                    />
                                </div>

                                <!-- Victim Equipment -->
                                <div>
                                    <h3 class="text-sm font-semibold text-red-400 mb-3">{{ $event->victim_name ?? 'Victim' }}</h3>
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
            <div class="bg-gray-900 rounded-lg border border-gray-800 p-12 text-center">
                <div class="text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-400">No events found</h3>
                    <p class="mt-1 text-sm text-gray-600">Try adjusting your filters or wait for new kills to be tracked.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($events->count() >= 50)
        <div class="text-center text-sm text-gray-500 py-4">
            Showing the 50 most recent events
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
        toggleText.textContent = 'Hide Details ▲';
    } else {
        element.classList.add('hidden');
        toggleText.textContent = 'Show Details ▼';
    }
}
</script>
@endsection
