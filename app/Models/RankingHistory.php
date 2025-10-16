<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankingHistory extends Model
{
    use HasFactory;

    protected $table = 'ranking_history';

    protected $fillable = [
        'user_id',
        'season',
        'year',
        'points',
        'rank',
    ];

    protected function casts(): array
    {
        return [
            'season' => 'integer',
            'year' => 'integer',
            'points' => 'integer',
            'rank' => 'integer',
        ];
    }

    /**
     * Get the user for the ranking history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get season name (Q1, Q2, Q3, Q4).
     */
    public function getSeasonNameAttribute()
    {
        if ($this->season === null) {
            return 'Year';
        }
        
        return 'Q' . $this->season;
    }

    /**
     * Get formatted display for badges (e.g., "2025 #112", "2024 Q1 #133")
     */
    public function getDisplayNameAttribute()
    {
        if ($this->season === null) {
            return "{$this->year} #{$this->rank}";
        }
        
        return "{$this->year} Q{$this->season} #{$this->rank}";
    }

    /**
     * Scope to get user's ranking history ordered by most recent
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->orderBy('year', 'desc')
            ->orderBy('season', 'desc');
    }
}

