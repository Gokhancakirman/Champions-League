<?php

namespace App\Services\TeamSelection;

interface ITeamSelector
{
    public function select(int $count): array;
}