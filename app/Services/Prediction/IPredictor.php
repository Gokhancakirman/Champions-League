<?php

namespace App\Services\Prediction;

use App\Models\Season;
use App\Services\Simulation\IMatchSimulator;

/**
 * Interface for prediction strategies.
 */
interface IPredictor
{
    /**
     * Predict championship probabilities for each team in the season.
     *
     * @param Season $season       The season to predict for
     * @param int    $simulations  Number of Monte Carlo iterations
     * @return array               [ team_id => probability (0-100), ... ]
     */
    public function predict(Season $season, int $simulations = 1000): array;
} 