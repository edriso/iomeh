<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $table = 'seasons';

    protected $fillable = [
        'user_id',
        'name',
        'year',
        'points',
        'season_year_points',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'integer',
            'year' => 'integer',
            'points' => 'integer',
            'season_year_points' => 'integer',
        ];
    }

    /**
     * Get the user for the season.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get seasons filtered by year and name, ordered by points.
     */
    public function scopeForSeason($query, $year, $name)
    {
        return $query->where('year', $year)
                    ->where('name', $name)
                    ->orderBy('points', 'desc');
    }

    /**
     * Scope to get year rankings ordered by season year points.
     */
    public function scopeYear($query, $year)
    {
        return $query->where('year', $year)
                    ->orderBy('season_year_points', 'desc');
    }

    /**
     * Calculate user's rank for this specific season.
     */
    public function getSeasonRankAttribute()
    {
        // Count how many users have more points in the same season
        // Due to unique constraint (user_id, name, year), each record represents a unique user
        return self::where('year', $this->year)
            ->where('name', $this->name)
            ->where('points', '>', $this->points)
            ->count() + 1;
    }

    /**
     * Calculate user's rank for the year (across all users in the same year).
     */
    public function getYearRankAttribute()
    {
        // Count distinct users with higher season_year_points
        // We need to count unique users, not season records
        $usersWithMorePoints = self::where('year', $this->year)
            ->where('season_year_points', '>', $this->season_year_points)
            ->distinct()
            ->pluck('user_id')
            ->count();
        
        return $usersWithMorePoints + 1;
    }

    /**
     * Get season display name (Q1, Q2, Q3, Q4).
     */
    public function getDisplayNameAttribute()
    {
        return 'Q' . $this->name;
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
     * Get top seasons for current season.
     */
    public static function getCurrentSeasonTop($limit = 10)
    {
        $currentYear = now()->year;
        $currentSeasonName = ceil(now()->month / 3);
        
        $seasons = self::forSeason($currentYear, $currentSeasonName)
            ->with('user')
            ->limit($limit)
            ->get();
        
        // Add rank as attribute
        $rank = 1;
        foreach ($seasons as $season) {
            $season->rank = $rank++;
        }
        
        return $seasons;
    }

    /**
     * Get top seasons for current year.
     * Groups by user and gets their best season_year_points from any season.
     */
    public static function getCurrentYearTop($limit = 10)
    {
        $currentYear = now()->year;
        
        // Get max season_year_points for each user
        $seasons = self::where('year', $currentYear)
            ->selectRaw('user_id, MAX(season_year_points) as season_year_points')
            ->groupBy('user_id')
            ->orderBy('season_year_points', 'desc')
            ->limit($limit)
            ->with('user')
            ->get();
        
        // Add rank as attribute
        $rank = 1;
        foreach ($seasons as $season) {
            $season->rank = $rank++;
        }
        
        return $seasons;
    }

    /**
     * Get the current season number (1-4) based on the current month.
     * Q1 = Jan-Mar (1), Q2 = Apr-Jun (2), Q3 = Jul-Sep (3), Q4 = Oct-Dec (4)
     */
    public static function getCurrentSeasonNumber(): int
    {
        return (int) ceil(now()->month / 3);
    }

    /**
     * Get the start date of a specific season.
     * 
     * @param int $seasonNumber Season number (1-4)
     * @param int|null $year Year (defaults to current year)
     * @return \Carbon\Carbon
     */
    public static function getSeasonStartDate(int $seasonNumber, ?int $year = null): \Carbon\Carbon
    {
        $year = $year ?? now()->year;
        
        $startMonths = [
            1 => 1,  // Q1: January
            2 => 4,  // Q2: April
            3 => 7,  // Q3: July
            4 => 10, // Q4: October
        ];
        
        return \Carbon\Carbon::create($year, $startMonths[$seasonNumber], 1)->startOfDay();
    }

    /**
     * Get the end date of a specific season.
     * 
     * @param int $seasonNumber Season number (1-4)
     * @param int|null $year Year (defaults to current year)
     * @return \Carbon\Carbon
     */
    public static function getSeasonEndDate(int $seasonNumber, ?int $year = null): \Carbon\Carbon
    {
        $year = $year ?? now()->year;
        
        $endMonths = [
            1 => 3,  // Q1: March
            2 => 6,  // Q2: June
            3 => 9,  // Q3: September
            4 => 12, // Q4: December
        ];
        
        return \Carbon\Carbon::create($year, $endMonths[$seasonNumber], 1)->endOfMonth()->endOfDay();
    }

    /**
     * Get the start and end dates of the current season.
     * 
     * @return array{start: \Carbon\Carbon, end: \Carbon\Carbon, season: int, year: int}
     */
    public static function getCurrentSeasonDates(): array
    {
        $seasonNumber = self::getCurrentSeasonNumber();
        $year = now()->year;
        
        return [
            'start' => self::getSeasonStartDate($seasonNumber, $year),
            'end' => self::getSeasonEndDate($seasonNumber, $year),
            'season' => $seasonNumber,
            'year' => $year,
        ];
    }

    /**
     * Check if a given date falls within the current season.
     * 
     * @param string|\Carbon\Carbon $date
     * @return bool
     */
    public static function isDateInCurrentSeason($date): bool
    {
        $date = \Carbon\Carbon::parse($date);
        $seasonDates = self::getCurrentSeasonDates();
        
        return $date->between($seasonDates['start'], $seasonDates['end']);
    }
}

