<?php

use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can have habits', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create([
        'name' => ['en' => 'Running', 'ar' => 'الركض']
    ]);
    
    $habit = Habit::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Morning Jog',
        'display_order' => 1,
    ]);
    
    expect($user->habits()->count())->toBe(1);
    expect($habit->user->id)->toBe($user->id);
    expect($habit->activityType->id)->toBe($activityType->id);
});

test('habit belongs to user and activity type', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    
    $habit = Habit::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Habit',
    ]);
    
    expect($habit->user)->toBeInstanceOf(User::class);
    expect($habit->activityType)->toBeInstanceOf(ActivityType::class);
});

test('habit can have custom name', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create([
        'name' => ['en' => 'Weight Training', 'ar' => 'تدريب الأثقال']
    ]);
    
    $habit = Habit::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Gym Workout',
    ]);
    
    expect($habit->custom_name)->toBe('My Gym Workout');
});

test('habit can be ordered', function () {
    $user = User::factory()->create();
    $activityType1 = ActivityType::factory()->create();
    $activityType2 = ActivityType::factory()->create();
    
    Habit::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType1->id,
        'custom_name' => 'Habit 1',
        'display_order' => 2,
    ]);
    
    Habit::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType2->id,
        'custom_name' => 'Habit 2',
        'display_order' => 1,
    ]);
    
    $habits = $user->habits()->orderBy('display_order')->get();
    expect($habits->first()->display_order)->toBe(1);
    expect($habits->last()->display_order)->toBe(2);
});

