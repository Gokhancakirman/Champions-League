<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\SeasonTeam;
use App\Models\Standing;
use App\Models\Fixture;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'total_weeks',
        'current_week',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(SeasonTeam::class);
    }

    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class);
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function hasTeams(): bool
    {
        return $this->teams()->exists();
    }
    
    public function thisWeekMatches()
    {
        return $this->hasMany(Fixture::class)
            ->where('week', $this->current_week)
            ->where('played', false);
    }
}
