<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'current_season_points',
        'current_year_points',
        'lifetime_points',
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
            'current_season_points' => 'integer',
            'current_year_points' => 'integer',
            'lifetime_points' => 'integer',
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
     * Get all interests for the user.
     */
    public function interests()
    {
        return $this->hasMany(Interest::class)->orderBy('display_order');
    }

    /**
     * Get all rankings for the user.
     */
    public function rankings()
    {
        return $this->hasMany(Ranking::class);
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
            ->with(['interest.activityType'])
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
        }
        
        $this->last_activity_date = $today;
        $this->save();
    }

    /**
     * Add points to user and update rankings
     */
    public function addPoints(int $points): void
    {
        $this->current_season_points += $points;
        $this->current_year_points += $points;
        $this->lifetime_points += $points;
        $this->save();
        
        // Update rankings
        $this->updateRankings();
    }

    /**
     * Update user's rankings for current season and year
     */
    public function updateRankings(): void
    {
        $currentYear = now()->year;
        $currentSeason = ceil(now()->month / 3); // Q1-Q4
        
        // Update season ranking
        Ranking::updateOrCreate(
            [
                'user_id' => $this->id,
                'year' => $currentYear,
                'season' => $currentSeason,
            ],
            [
                'points' => $this->current_season_points,
            ]
        );
        
        // Update yearly ranking
        Ranking::updateOrCreate(
            [
                'user_id' => $this->id,
                'year' => $currentYear,
                'season' => null,
            ],
            [
                'points' => $this->current_year_points,
            ]
        );
    }

    /**
     * Get user's current season ranking
     */
    public function getCurrentSeasonRank()
    {
        $currentYear = now()->year;
        $currentSeason = ceil(now()->month / 3);
        
        return $this->rankings()
            ->where('year', $currentYear)
            ->where('season', $currentSeason)
            ->first();
    }

    /**
     * Get user's current year ranking
     */
    public function getCurrentYearRank()
    {
        $currentYear = now()->year;
        
        return $this->rankings()
            ->where('year', $currentYear)
            ->whereNull('season')
            ->first();
    }

    /**
     * Get activity calendar for the user
     */
    public function getActivityCalendar()
    {
        return $this->activities()
            ->with(['interest.activityType'])
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
                            'interest_name' => $activity->interest->custom_name,
                            'activity_type' => $activity->interest->activityType->name,
                            'points_earned' => $activity->points_earned,
                            'notes' => $activity->notes,
                        ];
                    })->values(),
                ];
            })
            ->values();
    }
}
