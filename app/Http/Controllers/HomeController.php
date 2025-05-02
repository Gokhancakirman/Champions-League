<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        # Find winner for each season
        $seasons = Season::with(['standings.seasonTeam.team'])
            ->orderBy('created_at', 'desc')->get();
        $seasons->each(function ($season) {
            $standing_data = $season->standings->sortByDesc('points')->first();
            if ($standing_data && $standing_data->seasonTeam) {
                $season->winner = $standing_data->seasonTeam->team;
            }
        });
        $activeSeason = $seasons->firstWhere('is_active', true);

        return Inertia::render('Home', [
            'seasons' => $seasons,
            'activeSeason' => $activeSeason
        ]);
    }
} 