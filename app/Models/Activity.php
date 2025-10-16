<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'interest_id',
        'date',
        'points_earned',
        'notes',
        'proof_url',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'points_earned' => 'integer',
        ];
    }

    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the interest for the activity.
     */
    public function interest()
    {
        return $this->belongsTo(Interest::class);
    }

    /**
     * Flag to control whether to auto-update points and streaks
     * Set to false during bulk operations/seeding for performance
     */
    public static $skipAutoUpdate = false;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically update user points and streaks when activity is created
        static::created(function ($activity) {
            // Skip auto-updates if flag is set (during seeding/bulk operations)
            if (self::$skipAutoUpdate) {
                return;
            }

            // Update user's streak
            $activity->user->updateStreak();
            
            // Award points to user based on the activity date
            if ($activity->points_earned > 0) {
                $activity->user->addPoints($activity->points_earned, $activity->date);
            }
        });
    }

    /**
     * Scope to get activities for today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    /**
     * Scope to get activities for yesterday.
     */
    public function scopeYesterday($query)
    {
        return $query->whereDate('date', now()->subDay()->toDateString());
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->whereDate('date', '>=', now()->subDays($days)->toDateString());
    }

    /**
     * Get activity type through interest.
     */
    public function getActivityTypeAttribute()
    {
        return $this->interest?->activityType;
    }
}

