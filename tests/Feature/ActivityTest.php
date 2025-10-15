<?php

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can have activities', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    $activity = Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    expect($user->activities()->count())->toBe(1);
    expect($activity->user->id)->toBe($user->id);
    expect($activity->interest->id)->toBe($interest->id);
});

test('activity belongs to user and interest', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    $activity = Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    expect($activity->user)->toBeInstanceOf(User::class);
    expect($activity->interest)->toBeInstanceOf(\App\Models\Interest::class);
});

test('can query activities for today', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    // Create today's activity
    Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    // Create yesterday's activity
    Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now()->yesterday(),
        'points_earned' => 30,
    ]);
    
    $todayActivities = Activity::today()->count();
    expect($todayActivities)->toBe(1);
});

test('can query activities for yesterday', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    // Create today's activity
    Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    // Create yesterday's activity
    Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now()->yesterday(),
        'points_earned' => 30,
    ]);
    
    $yesterdayActivities = Activity::yesterday()->count();
    expect($yesterdayActivities)->toBe(1);
});

test('activity casts date properly', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    $activity = Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => '2025-10-15',
        'points_earned' => 50,
    ]);
    
    expect($activity->date)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($activity->date->format('Y-m-d'))->toBe('2025-10-15');
});

