<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateSeasonRequest;
use Illuminate\Http\JsonResponse;
use App\Services\SeasonService;
use Inertia\Inertia;

class SeasonController extends Controller
{
    public function __construct(protected SeasonService $seasonService) {}

    public function create()
    {
        return $this->seasonService->getCreatePage();
    }

    public function show(string $slug)
    {
        return $this->seasonService->getShowPage($slug);
    }

    public function store(CreateSeasonRequest $request): JsonResponse
    {
        $season = $this->seasonService->create($request->input('name'));
        return response()->json($season, 201);
    }

    public function selectTeams(Request $request)
    {
        return $this->seasonService->selectTeams($request->input('team_count'));
    }

    public function start(string $slug, Request $request)
    {
        return $this->seasonService->start($slug, $request->input('teams'));
    }

    public function simulateWeek(SeasonService $seasonService, string $slug)
    {
        return $seasonService->simulateWeek($slug);
    }

    public function simulateAll(SeasonService $seasonService, string $slug)
    {
        return $seasonService->simulateAll($slug);
    }

    public function reset(string $slug)
    {
        return $this->seasonService->reset($slug);
    }
}
