<?php

use App\Models\ActivityType;
use App\Models\Interest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can have interests', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create(['name' => 'Running']);
    
    $interest = Interest::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Morning Jog',
        'display_order' => 1,
    ]);
    
    expect($user->interests()->count())->toBe(1);
    expect($interest->user->id)->toBe($user->id);
    expect($interest->activityType->id)->toBe($activityType->id);
});

test('interest belongs to user and activity type', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    
    $interest = Interest::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Interest',
    ]);
    
    expect($interest->user)->toBeInstanceOf(User::class);
    expect($interest->activityType)->toBeInstanceOf(ActivityType::class);
});

test('interest can have custom name', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create(['name' => 'Weight Training']);
    
    $interest = Interest::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Gym Workout',
    ]);
    
    expect($interest->custom_name)->toBe('My Gym Workout');
});

test('interest can be ordered', function () {
    $user = User::factory()->create();
    $activityType1 = ActivityType::factory()->create();
    $activityType2 = ActivityType::factory()->create();
    
    Interest::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType1->id,
        'custom_name' => 'Interest 1',
        'display_order' => 2,
    ]);
    
    Interest::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType2->id,
        'custom_name' => 'Interest 2',
        'display_order' => 1,
    ]);
    
    $interests = $user->interests()->orderBy('display_order')->get();
    expect($interests->first()->display_order)->toBe(1);
    expect($interests->last()->display_order)->toBe(2);
});

