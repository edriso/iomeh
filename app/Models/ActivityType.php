<?php

namespace App\Models;

use App\Enums\ActivityCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'base_points',
        'icon',
        'display_order',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => ActivityCategory::class,
            'base_points' => 'integer',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all habits that use this activity type.
     */
    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    /**
     * Get activities through habits for this activity type.
     */
    public function activities()
    {
        return $this->hasManyThrough(Activity::class, Habit::class);
    }

    /**
     * Scope to get only active activity types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Get activity types grouped by category.
     */
    public static function getByCategory()
    {
        return self::active()
            ->ordered()
            ->get()
            ->groupBy('category');
    }
}

