<?php

use App\Enums\ActivityCategory;
use App\Models\ActivityType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('activity type can be created with all fields', function () {
    $activityType = ActivityType::create([
        'name' => ['en' => 'Morning Run', 'ar' => 'الركض الصباحي'],
        'category' => ActivityCategory::WORKOUT->value,
        'base_points' => 30,
        'description' => ['en' => 'A morning jogging session', 'ar' => 'جلسة ركض صباحية'],
        'icon' => '🏃',
        'is_active' => true,
    ]);
    
    expect($activityType->getTranslatedName())->toBe('Morning Run');
    expect($activityType->category)->toBe(ActivityCategory::WORKOUT);
    expect($activityType->base_points)->toBe(30);
    expect($activityType->getTranslatedDescription())->toBe('A morning jogging session');
    expect($activityType->is_active)->toBeTrue();
});

test('activity type casts category to enum', function () {
    $activityType = ActivityType::create([
        'name' => ['en' => 'Morning Run', 'ar' => 'الركض الصباحي'],
        'category' => ActivityCategory::WORKOUT->value,
        'base_points' => 25,
    ]);
    
    expect($activityType->category)->toBeInstanceOf(ActivityCategory::class);
    expect($activityType->category)->toBe(ActivityCategory::WORKOUT);
});

test('activity type has activities relationship', function () {
    $activityType = ActivityType::factory()->create();
    
    expect($activityType->activities())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasManyThrough::class);
});

test('activity type has habits relationship', function () {
    $activityType = ActivityType::factory()->create();
    
    expect($activityType->habits())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('all activity categories are valid', function () {
    expect(ActivityCategory::cases())->toHaveCount(2);
    expect(ActivityCategory::values())->toContain('workout');
    expect(ActivityCategory::values())->toContain('nutrition');
});

test('activity category has display names', function () {
    expect(ActivityCategory::WORKOUT->label())->toBe('Workout');
    expect(ActivityCategory::NUTRITION->label())->toBe('Nutrition');
});

