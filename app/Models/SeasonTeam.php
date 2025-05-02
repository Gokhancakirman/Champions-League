<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Team;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Fixture;

class SeasonTeam extends Model
{
    use HasFactory;
    protected $fillable = [
        'season_id',
        'team_id',
        'power',
        'supporter_strength'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }

    public function fixtures()
    {
        return Fixture::where(function($query) {
            $query->where('home_team_id', $this->id)
                  ->orWhere('away_team_id', $this->id);
        });
    }
}
