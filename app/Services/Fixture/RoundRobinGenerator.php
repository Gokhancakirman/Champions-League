<?php 

namespace App\Services\Fixture;

use App\Services\Fixture\IFixtureGenerator;
use Illuminate\Support\Collection;

class RoundRobinGenerator implements IFixtureGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(Collection $teams): array
    {
        $teamIds = $teams->pluck('id')->toArray();
        $count = count($teamIds);

        // Ensure even count (validation upstream should prevent odd)
        if ($count % 2 !== 0) {
            throw new \InvalidArgumentException('Odd number of teams is not supported.');
        }

        $rounds = $count - 1;
        $half   = $count / 2;

        $firstLeg = [];

        // Generate first leg
        for ($round = 0; $round < $rounds; $round++) {
            for ($i = 0; $i < $half; $i++) {
                $home = $teamIds[$i];
                $away = $teamIds[$count - 1 - $i];

                // Alternate home advantage every round for fairness on the first pairing
                if ($round % 2 === 1 && $i === 0) {
                    [$home, $away] = [$away, $home];
                }

                $firstLeg[] = [
                    'week'    => $round + 1,
                    'home_team_id' => $home,
                    'away_team_id' => $away,
                ];
            }

            // Rotate teams (except the first fixed)
            $slice    = array_slice($teamIds, 1);
            $last     = array_pop($slice);
            array_unshift($slice, $last);
            $teamIds  = array_merge([ $teamIds[0] ], $slice);
        }

        // Generate second leg by mirroring first leg
        $secondLeg = array_map(function ($fixture) use ($rounds) {
            return [
                'week'    => $fixture['week'] + $rounds,
                'home_team_id' => $fixture['away_team_id'],
                'away_team_id' => $fixture['home_team_id'],
            ];
        }, $firstLeg);

        return array_merge($firstLeg, $secondLeg);
    }
}