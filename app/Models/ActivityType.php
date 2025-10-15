<?php

namespace App\Models;

use App\Enums\ActivityCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'base_points',
        'met_value',
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
            'met_value' => 'decimal:1',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activityType) {
            if (empty($activityType->slug)) {
                $activityType->slug = Str::slug($activityType->name);
            }
        });
    }

    /**
     * Get all interests that use this activity type.
     */
    public function interests()
    {
        return $this->hasMany(Interest::class);
    }

    /**
     * Get activities through interests for this activity type.
     */
    public function activities()
    {
        return $this->hasManyThrough(Activity::class, Interest::class);
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

