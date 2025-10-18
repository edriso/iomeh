<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Habit> $habits
 * @method \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Habit> habits()
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'name',
        'avatar',
        'bio',
        'website_url',
        'week_starts_on',
        'current_season_id',
        'current_streak',
        'longest_streak',
        'last_activity_date',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'week_starts_on' => 'integer',
            'current_season_id' => 'integer',
            'current_streak' => 'integer',
            'longest_streak' => 'integer',
            'last_activity_date' => 'date',
        ];
    }

    /**
     * Get the user's social logins.
     */
    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    /**
     * Get all activities for the user.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get all habits for the user.
     */
    public function habits()
    {
        return $this->hasMany(Habit::class)->orderBy('display_order');
    }

    /**
     * Get all seasons for the user.
     */
    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    /**
     * Get the user's current season.
     */
    public function currentSeason()
    {
        return $this->belongsTo(Season::class, 'current_season_id');
    }

    /**
     * Get all ranking history for the user.
     */
    public function rankingHistory()
    {
        return $this->hasMany(RankingHistory::class);
    }

    /**
     * Get the user's display name (name or username).
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: $this->username;
    }

    /**
     * Get the user's joined days ago.
     */
    public function getJoinedDaysAgoAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Check if user has a password (traditional registration)
     */
    public function hasPassword(): bool
    {
        return !is_null($this->password);
    }

    /**
     * Check if user is a social-only user (no password)
     */
    public function isSocialUser(): bool
    {
        return is_null($this->password) && $this->socialLogins()->exists();
    }

    /**
     * Get today's activities
     */
    public function getTodayActivities()
    {
        return $this->activities()
            ->whereDate('date', now()->toDateString())
            ->with(['habit.activityType'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if user has activity today
     */
    public function hasActivityToday(): bool
    {
        return $this->activities()
            ->whereDate('date', now()->toDateString())
            ->exists();
    }

    /**
     * Update user's streak based on activity
     */
    public function updateStreak(): void
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        
        // If last activity was yesterday, increment streak
        if ($this->last_activity_date && $this->last_activity_date->toDateString() === $yesterday) {
            $this->current_streak++;
            
            // Update longest streak if current is higher
            if ($this->current_streak > $this->longest_streak) {
                $this->longest_streak = $this->current_streak;
            }
        } 
        // If last activity was not yesterday and not today, reset streak
        elseif (!$this->last_activity_date || $this->last_activity_date->toDateString() !== $today) {
            $this->current_streak = 1;
            
            // Update longest streak if this is the first ever streak
            if ($this->current_streak > $this->longest_streak) {
                $this->longest_streak = $this->current_streak;
            }
        }
        
        $this->last_activity_date = $today;
        $this->save();
    }

    /**
     * Get the user's lifetime points calculated from all activities
     */
    public function getLifetimePointsAttribute(): int
    {
        return $this->activities()->sum('points_earned');
    }

    /**
     * Add points to user and update seasons
     */
    public function addPoints(int $points, $date = null): void
    {
        $date = $date ? \Carbon\Carbon::parse($date) : now();
        
        // Update seasons based on the activity date
        $this->updateSeasons($date, $points);
    }

    /**
     * Update user's seasons for a specific date
     * This handles year/quarter transitions automatically
     * 
     * New structure: Each season record stores both points and season_year_points
     */
    public function updateSeasons($date = null, int $points = 0): void
    {
        $date = $date ? \Carbon\Carbon::parse($date) : now();
        $year = $date->year;
        $seasonName = ceil($date->month / 3); // Q1-Q4
        
        $today = now();
        $currentYear = $today->year;
        $currentSeasonName = ceil($today->month / 3);
        
        // Update the season (add points to points)
        $season = Season::firstOrCreate(
            [
                'user_id' => $this->id,
                'year' => $year,
                'name' => $seasonName,
            ],
            [
                'points' => 0,
                'season_year_points' => 0,
            ]
        );
        
        // Add points to points
        $season->increment('points', $points);
        
        // Calculate new season_year_points by summing all seasons in this year
        $yearTotal = Season::where('user_id', $this->id)
            ->where('year', $year)
            ->sum('points');
        
        // Update season_year_points for ALL season records in this year
        Season::where('user_id', $this->id)
            ->where('year', $year)
            ->update(['season_year_points' => $yearTotal]);
        
        // Update current_season_id if this is for the current season
        if ($year === $currentYear && $seasonName === $currentSeasonName) {
            // Refresh to get updated season_year_points
            $season->refresh();
            $this->current_season_id = $season->id;
            $this->save();
        }
    }

    /**
     * Get user's current season
     */
    public function getCurrentSeason()
    {
        $currentYear = now()->year;
        $currentSeasonName = ceil(now()->month / 3);
        
        return $this->seasons()
            ->where('year', $currentYear)
            ->where('name', $currentSeasonName)
            ->first();
    }

    /**
     * Get user's current year season (from any season in current year with highest points)
     */
    public function getCurrentYearSeason()
    {
        $currentYear = now()->year;
        
        return $this->seasons()
            ->where('year', $currentYear)
            ->orderBy('season_year_points', 'desc')
            ->first();
    }

    /**
     * Get activity calendar for the user
     */
    public function getActivityCalendar()
    {
        return $this->activities()
            ->with(['habit.activityType'])
            ->latest('date')
            ->get()
            ->groupBy(function ($activity) {
                return $activity->date->format('Y-m-d');
            })
            ->map(function ($dayActivities, $date) {
                return [
                    'date' => \Carbon\Carbon::parse($date),
                    'iso_date' => \Carbon\Carbon::parse($date)->toISOString(),
                    'activities_count' => $dayActivities->count(),
                    'total_points' => $dayActivities->sum('points_earned'),
                    'activities' => $dayActivities->map(function ($activity) {
                        return [
                            'id' => $activity->id,
                            'habit_name' => $activity->habit->custom_name,
                            'activity_type' => $activity->habit->activityType->name,
                            'points_earned' => $activity->points_earned,
                            'notes' => $activity->notes,
                        ];
                    })->values(),
                ];
            })
            ->values();
    }

    /**
     * Get the current streak tier information
     */
    public function getStreakTier(): array
    {
        $streak = $this->current_streak ?? 0;
        $tiers = config('gamification.streak_tiers', []);
        
        foreach ($tiers as $tier) {
            if ($streak >= $tier['min'] && $streak <= $tier['max']) {
                return $tier;
            }
        }
        
        // Fallback to first tier if no match
        return $tiers[0] ?? ['min' => 1, 'max' => 2, 'name' => 'Newcomer', 'multiplier' => 1.0, 'icon' => '🌱'];
    }

    /**
     * Calculate points with streak bonus applied
     */
    public function calculatePointsWithStreakBonus(int $basePoints): int
    {
        $tier = $this->getStreakTier();
        $multiplier = $tier['multiplier'] ?? 1.0;
        
        return (int) round($basePoints * $multiplier);
    }

    /**
     * Check if user has reached a milestone and return bonus points
     */
    public function getMilestoneBonus(): int
    {
        $milestones = config('gamification.milestone_bonuses', []);
        $currentStreak = $this->current_streak ?? 0;
        
        // Check if current streak matches a milestone
        return $milestones[$currentStreak] ?? 0;
    }

    /**
     * Get streak tier name (e.g., "Regular", "Master")
     */
    public function getStreakTierNameAttribute(): string
    {
        return $this->getStreakTier()['name'] ?? 'Newcomer';
    }

    /**
     * Get streak multiplier (e.g., 2.5)
     */
    public function getStreakMultiplierAttribute(): float
    {
        return $this->getStreakTier()['multiplier'] ?? 1.0;
    }
}
