<?php

namespace App\Http\Controllers;

use App\Repositories\SeasonRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(protected SeasonRepository $seasons) {}

    public function index()
    {
        $seasons = $this->seasons->getAllSeasonsWithWinners();
        $activeSeason = $this->seasons->getActiveSeasonWithRelations();

        return Inertia::render('Home', [
            'seasons' => $seasons,
            'activeSeason' => $activeSeason
        ]);
    }
} 