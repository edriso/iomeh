<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use HasFactory;

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
     * Get the user for the ranking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get season rankings.
     */
    public function scopeSeason($query, $year, $season)
    {
        return $query->where('year', $year)
                    ->where('season', $season)
                    ->orderBy('rank');
    }

    /**
     * Scope to get year rankings.
     */
    public function scopeYear($query, $year)
    {
        return $query->where('year', $year)
                    ->whereNull('season')
                    ->orderBy('rank');
    }

    /**
     * Recalculate all ranks for a given season/year.
     */
    public static function recalculateRanks($year, $season = null)
    {
        $query = self::where('year', $year);
        
        if ($season !== null) {
            $query->where('season', $season);
        } else {
            $query->whereNull('season');
        }
        
        $rankings = $query->orderBy('points', 'desc')->get();
        
        $rank = 1;
        foreach ($rankings as $ranking) {
            $ranking->rank = $rank++;
            $ranking->save();
        }
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
     * Get top rankings for today (current day).
     */
    public static function getTodayTop($limit = 10)
    {
        $today = now()->toDateString();
        
        return User::whereHas('activities', function ($query) use ($today) {
                $query->whereDate('date', $today);
            })
            ->withSum(['activities as today_points' => function ($query) use ($today) {
                $query->whereDate('date', $today);
            }], 'points_earned')
            ->orderBy('today_points', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top rankings for yesterday.
     */
    public static function getYesterdayTop($limit = 10)
    {
        $yesterday = now()->subDay()->toDateString();
        
        return User::whereHas('activities', function ($query) use ($yesterday) {
                $query->whereDate('date', $yesterday);
            })
            ->withSum(['activities as yesterday_points' => function ($query) use ($yesterday) {
                $query->whereDate('date', $yesterday);
            }], 'points_earned')
            ->orderBy('yesterday_points', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top rankings for current season.
     */
    public static function getCurrentSeasonTop($limit = 10)
    {
        $currentYear = now()->year;
        $currentSeason = ceil(now()->month / 3);
        
        return self::season($currentYear, $currentSeason)
            ->with('user')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top rankings for current year.
     */
    public static function getCurrentYearTop($limit = 10)
    {
        $currentYear = now()->year;
        
        return self::year($currentYear)
            ->with('user')
            ->limit($limit)
            ->get();
    }
}

