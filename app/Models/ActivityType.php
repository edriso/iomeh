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
        'locale',
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
            'name' => 'array',
            'description' => 'array',
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

    /**
     * Get the translated name for the current locale.
     */
    public function getTranslatedNameAttribute()
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['en'] ?? '';
    }

    /**
     * Get the translated description for the current locale.
     */
    public function getTranslatedDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $this->description[$locale] ?? $this->description['en'] ?? null;
    }

    /**
     * Get the name in a specific locale.
     */
    public function getNameInLocale(string $locale): string
    {
        return $this->name[$locale] ?? $this->name['en'] ?? '';
    }

    /**
     * Get the description in a specific locale.
     */
    public function getDescriptionInLocale(string $locale): ?string
    {
        return $this->description[$locale] ?? $this->description['en'] ?? null;
    }

    /**
     * Get the translated name for the current locale.
     */
    public function getTranslatedName()
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['en'] ?? '';
    }

    /**
     * Get the translated description for the current locale.
     */
    public function getTranslatedDescription()
    {
        $locale = app()->getLocale();
        return $this->description[$locale] ?? $this->description['en'] ?? null;
    }
}

