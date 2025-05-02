<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Season;
use App\Models\Team;
use App\Models\SeasonTeam;
use App\Models\Fixture;
use App\Models\Standing;
use App\Services\SeasonService;
use App\Services\Prediction\IPredictor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ChampionsLeagueTest extends TestCase
{
    use RefreshDatabase;

    protected $seasonService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seasonService = app(SeasonService::class);
    }

    /** @test */
    public function it_can_create_a_new_season_with_teams()
    {
        // Create test teams
        $teams = Team::factory()->count(4)->create();

        // Create a new season
        $season = $this->seasonService->create('Test Season 2024');

        // Select teams for the season
        $selectedTeams = $this->seasonService->selectTeams(4);
        
        // Start the season with selected teams
        $response = $this->seasonService->start($season->slug, $selectedTeams->original);

        $this->assertNotNull($response);
        $this->assertEquals(4, $season->teams()->count());
    }

    /** @test */
    public function it_can_generate_fixtures_for_a_season()
    {
        // Create a season with teams
        $season = Season::factory()->create();
        $teams = Team::factory()->count(4)->create();
        
        $seasonTeams = $teams->map(function ($team) {
            return [
                'team_id' => $team->id,
                'power' => rand(70, 90),
                'supporter_strength' => rand(50, 100)
            ];
        });

        // Start the season to generate fixtures
        $start = $this->seasonService->start($season->slug, $seasonTeams->toArray());
        
        // Get fixtures directly from the database to avoid grouped collection
        $fixtures = $season->fixtures()->get();
        $fixtureCount = $fixtures->count();
        
        \Log::info('Test fixture count: ' . $fixtureCount);
        \Log::info('Test fixtures: ' . json_encode($fixtures->toArray()));

        // Verify fixtures are generated
        $this->assertEquals(12, $fixtureCount); // 2 teams * 1 match * 2 (home/away)
        
        // Verify each team plays home and away matches
        foreach ($season->teams as $team) {
            $homeMatches = $fixtures->where('home_team_id', $team->id)->count();
            $awayMatches = $fixtures->where('away_team_id', $team->id)->count();
            $this->assertEquals(6, $homeMatches + $awayMatches);
        }
    }

    /** @test */
    public function it_can_simulate_matches_week_by_week()
    {
        // Create a season with teams and fixtures
        $season = Season::factory()->create();
        $teams = Team::factory()->count(4)->create();
        
        $seasonTeams = $teams->map(function ($team) {
            return [
                'team_id' => $team->id,
                'power' => rand(70, 90),
                'supporter_strength' => rand(50, 100)
            ];
        });

        $this->seasonService->start($season->slug, $seasonTeams->toArray());

        // Simulate first week
        $response = $this->seasonService->simulateWeek($season->slug);
        // Verify matches are played
        $this->assertEquals(2, $season->fixtures()->where('week', 1)->where('played', true)->count());    
        // Verify current week is updated
        $this->assertEquals(2, $season->fresh()->current_week);
    }

    /** @test */
    public function it_can_simulate_all_matches_at_once()
    {
        // Create a season with teams and fixtures
        $season = Season::factory()->create();
        $teams = Team::factory()->count(4)->create();
        
        $seasonTeams = $teams->map(function ($team) {
            return [
                'team_id' => $team->id,
                'power' => rand(70, 90),
                'supporter_strength' => rand(50, 100)
            ];
        });


        $this->seasonService->start($season->slug, $seasonTeams->toArray());

        // Simulate all matches
        $response = $this->seasonService->simulateAll($season->slug);
        
        // Verify all matches are played
        $this->assertEquals(12, $season->fixtures()->where('played', true)->count());
        // Verify season is completed
        $this->assertEquals(0, $season->fresh()->is_active);
        
        // Verify standings are complete
        foreach ($season->standings as $standing) {
            $this->assertEquals(6, $standing->played); // 6 matches per team
        }
    }

    /** @test */
    public function it_updates_standings_correctly_after_matches()
    {
        // Create a season with teams
        $season = Season::factory()->create();
        $teams = Team::factory()->count(4)->create();
        
        $seasonTeams = $teams->map(function ($team) {
            return [
                'team_id' => $team->id,
                'power' => rand(70, 90),
                'supporter_strength' => rand(50, 100)
            ];
        });

        $this->seasonService->start($season->slug, $seasonTeams->toArray());

        // Simulate first week
        $this->seasonService->simulateWeek($season->slug);

        // Verify standings are updated correctly
        foreach ($season->standings as $standing) {
            $this->assertGreaterThanOrEqual(0, $standing->points);
            $this->assertGreaterThanOrEqual(0, $standing->goals_for);
            $this->assertGreaterThanOrEqual(0, $standing->goals_against);
            $this->assertEquals($standing->goals_for - $standing->goals_against, $standing->goal_difference);
        }
    }

    /** @test */
    public function it_can_predict_championship_probabilities()
    {
        // Create a season with teams
        $season = Season::factory()->create();
        $teams = Team::factory()->count(4)->create();
        
        $seasonTeams = $teams->map(function ($team) {
            return [
                'team_id' => $team->id,
                'power' => rand(70, 90),
                'supporter_strength' => rand(50, 100)
            ];
        });

        $this->seasonService->start($season->slug, $seasonTeams->toArray());

        // Get predictions
        $predictor = app(IPredictor::class);
        $predictions = $predictor->predict($season, 100);

        // Verify predictions
        $this->assertCount(4, $predictions);
        $totalProbability = array_sum($predictions);
        $this->assertEquals(100, round($totalProbability));
        
        // Verify each team has a probability between 0 and 100
        foreach ($predictions as $probability) {
            $this->assertGreaterThanOrEqual(0, $probability);
            $this->assertLessThanOrEqual(100, $probability);
        }
    }
} 