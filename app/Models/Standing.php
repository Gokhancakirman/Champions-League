<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Season;
use App\Models\SeasonTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Standing extends Model
{
    use HasFactory;
    protected $fillable = [
        'season_id',
        'team_id',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'points'
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function seasonTeam(): BelongsTo
    {
        return $this->belongsTo(SeasonTeam::class, 'team_id', 'id');
    }
}
