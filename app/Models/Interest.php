<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type_id',
        'custom_name',
        'notes',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
        ];
    }

    /**
     * Get the user that owns the interest.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activity type for the interest.
     */
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }

    /**
     * Get all activities for this interest.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get today's activity for this interest.
     */
    public function getTodayActivity()
    {
        return $this->activities()
            ->whereDate('date', now()->toDateString())
            ->first();
    }

    /**
     * Check if activity was logged today.
     */
    public function hasActivityToday(): bool
    {
        return $this->activities()
            ->whereDate('date', now()->toDateString())
            ->exists();
    }

    /**
     * Get activity count for a given period.
     */
    public function getActivityCount($days = 30)
    {
        return $this->activities()
            ->whereDate('date', '>=', now()->subDays($days)->toDateString())
            ->count();
    }

    /**
     * Get total points earned from this interest.
     */
    public function getTotalPoints()
    {
        return $this->activities()->sum('points_earned');
    }
}

