<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Fixture extends Model
{
    use HasFactory;
    protected $fillable = [
        'season_id',
        'week',
        'home_team_id',
        'away_team_id',
        'home_team_score',
        'away_team_score',
        'played',
        'simulation_details'
    ];

    protected $casts = [
        'played' => 'boolean',
        'simulation_details' => 'array'
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(SeasonTeam::class, 'home_team_id', 'id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(SeasonTeam::class, 'away_team_id', 'id');
    }
}
