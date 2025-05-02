<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TeamSelection\ITeamSelector;
use App\Services\TeamSelection\RandomTeamSelector;
use App\Services\Fixture\IFixtureGenerator;
use App\Services\Fixture\RoundRobinGenerator;
use App\Services\Simulation\IMatchSimulator;
use App\Services\Simulation\PowerBasedSimulator;
use App\Services\Prediction\IPredictor;
use App\Services\Prediction\ChampionshipPredictor;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SeasonRepository::class, SeasonRepository::class);
        $this->app->bind(ITeamSelector::class, RandomTeamSelector::class);
        $this->app->bind(IFixtureGenerator::class, RoundRobinGenerator::class);
        $this->app->bind(IMatchSimulator::class, PowerBasedSimulator::class);
        $this->app->bind(IPredictor::class, ChampionshipPredictor::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
