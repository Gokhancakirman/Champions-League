<?php

namespace App\Services\TeamSelection;


use App\Models\Team;

class RandomTeamSelector implements ITeamSelector
{
    public function select(int $count): array
    {
        return Team::inRandomOrder()
                   ->limit($count)
                   ->get(['id','name','power_min','logo','power_max','supporter_strength'])
                   ->map(fn($t)=>[
                       'team_id' => $t->id,
                       'team_name' => $t->name,
                       'team_logo' => $t->logo,
                       'power'   => rand($t->power_min, $t->power_max),
                       'supporter_strength' => $t->supporter_strength,
                   ])->toArray();
    }
}