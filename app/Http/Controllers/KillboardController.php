<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\ProcessedDeath;
use App\Models\ProcessedKill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KillboardController extends Controller
{
    public function index(Request $request)
    {
        $playerFilter = $request->get('player');
        $sortBy = $request->get('sort', 'recent');

        // Combine kills and deaths into a single collection
        $killsQuery = ProcessedKill::query()
            ->with('player')
            ->select('processed_kills.*', DB::raw("'kill' as event_type"));

        $deathsQuery = ProcessedDeath::query()
            ->with('player')
            ->select('processed_deaths.*', DB::raw("'death' as event_type"));

        if ($playerFilter) {
            $killsQuery->where(function($q) use ($playerFilter) {
                $q->where('killer_name', 'like', "%{$playerFilter}%")
                  ->orWhere('victim_name', 'like', "%{$playerFilter}%");
            });
            $deathsQuery->where(function($q) use ($playerFilter) {
                $q->where('killer_name', 'like', "%{$playerFilter}%")
                  ->orWhere('victim_name', 'like', "%{$playerFilter}%");
            });
        }

        // Combine and sort
        $events = $killsQuery->get()->merge($deathsQuery->get());

        // Sort by selected criteria
        $events = match($sortBy) {
            'fame' => $events->sortByDesc('total_fame'),
            'recent' => $events->sortByDesc('killed_at'),
            default => $events->sortByDesc('killed_at'),
        };

        $events = $events->take(50);

        // Get all tracked players for filter dropdown
        $players = Player::where('active', true)->orderBy('name')->get();

        // Get daily statistics for the last 14 days
        $dailyStats = $this->getDailyStats(14);

        return view('killboard.index', compact('events', 'players', 'playerFilter', 'sortBy', 'dailyStats'));
    }

    public function player(string $albionId)
    {
        $player = Player::where('albion_id', $albionId)->firstOrFail();

        $kills = ProcessedKill::where('albion_player_id', $albionId)
            ->orderBy('killed_at', 'desc')
            ->get();

        $deaths = ProcessedDeath::where('albion_player_id', $albionId)
            ->orderBy('killed_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_kills' => $kills->count(),
            'total_deaths' => $deaths->count(),
            'total_kill_fame' => $kills->sum('total_fame'),
            'total_death_fame' => $deaths->sum('total_fame'),
            'highest_fame_kill' => $kills->sortByDesc('total_fame')->first(),
            'most_used_weapon_kills' => $kills->where('killer_weapon', '!=', null)
                ->groupBy('killer_weapon')
                ->map->count()
                ->sortDesc()
                ->first(),
            'most_used_weapon_deaths' => $deaths->where('victim_weapon', '!=', null)
                ->groupBy('victim_weapon')
                ->map->count()
                ->sortDesc()
                ->first(),
            'weapon_kills_breakdown' => $kills->where('killer_weapon', '!=', null)
                ->groupBy('killer_weapon')
                ->map->count()
                ->sortDesc()
                ->take(5),
            'weapon_deaths_breakdown' => $deaths->where('victim_weapon', '!=', null)
                ->groupBy('victim_weapon')
                ->map->count()
                ->sortDesc()
                ->take(5),
            'avg_kill_fame' => $kills->count() > 0 ? round($kills->avg('total_fame')) : 0,
            'avg_death_fame' => $deaths->count() > 0 ? round($deaths->avg('total_fame')) : 0,
        ];

        return view('killboard.player', compact('player', 'kills', 'deaths', 'stats'));
    }

    private function getDailyStats(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();

        // Get daily kill counts
        $dailyKills = ProcessedKill::where('killed_at', '>=', $startDate)
            ->select(DB::raw('DATE(killed_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Get daily death counts
        $dailyDeaths = ProcessedDeath::where('killed_at', '>=', $startDate)
            ->select(DB::raw('DATE(killed_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Build complete date range with zero values for missing dates
        $dates = [];
        $kills = [];
        $deaths = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $kills[] = $dailyKills[$date] ?? 0;
            $deaths[] = $dailyDeaths[$date] ?? 0;
        }

        return [
            'dates' => $dates,
            'kills' => $kills,
            'deaths' => $deaths,
        ];
    }
}
