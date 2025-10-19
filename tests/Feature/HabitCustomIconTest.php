<?php

use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\User;

test('habit can have custom icon', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create(['icon' => '🏃‍♂️']);

    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Morning Run',
        'custom_icon' => '🏃‍♀️',
        'display_order' => 0,
    ]);

    expect($habit->custom_icon)->toBe('🏃‍♀️');
    expect($habit->getEffectiveIcon())->toBe('🏃‍♀️');
});

test('habit uses activity type icon when no custom icon', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create(['icon' => '🏃‍♂️']);

    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Morning Run',
        'custom_icon' => null,
        'display_order' => 0,
    ]);

    expect($habit->custom_icon)->toBeNull();
    expect($habit->getEffectiveIcon())->toBe('🏃‍♂️');
});

test('habit uses default icon when no activity type and no custom icon', function () {
    $user = User::factory()->create();

    $habit = $user->habits()->create([
        'activity_type_id' => null,
        'custom_name' => 'Custom Habit',
        'custom_icon' => null,
        'display_order' => 0,
    ]);

    expect($habit->custom_icon)->toBeNull();
    expect($habit->getEffectiveIcon())->toBe('🏃‍♂️');
});

test('habit can update custom icon', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create(['icon' => '🏃‍♂️']);

    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Morning Run',
        'custom_icon' => '🏃‍♀️',
        'display_order' => 0,
    ]);

    $habit->update(['custom_icon' => '🚴‍♀️']);
    
    expect($habit->fresh()->custom_icon)->toBe('🚴‍♀️');
    expect($habit->fresh()->getEffectiveIcon())->toBe('🚴‍♀️');
});
