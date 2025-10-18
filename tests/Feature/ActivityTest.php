<?php

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can have activities', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    $activity = Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    expect($user->activities()->count())->toBe(1);
    expect($activity->user->id)->toBe($user->id);
    expect($activity->habit->id)->toBe($habit->id);
});

test('activity belongs to user and habit', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    $activity = Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    expect($activity->user)->toBeInstanceOf(User::class);
    expect($activity->habit)->toBeInstanceOf(\App\Models\Habit::class);
});

test('can query activities for today', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    // Create today's activity
    Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    // Create yesterday's activity
    Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now()->yesterday(),
        'points_earned' => 30,
    ]);
    
    $todayActivities = Activity::today()->count();
    expect($todayActivities)->toBe(1);
});

test('can query activities for yesterday', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    // Create today's activity
    Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    // Create yesterday's activity
    Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now()->yesterday(),
        'points_earned' => 30,
    ]);
    
    $yesterdayActivities = Activity::yesterday()->count();
    expect($yesterdayActivities)->toBe(1);
});

test('activity casts date properly', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    $activity = Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => '2025-10-15',
        'points_earned' => 50,
    ]);
    
    expect($activity->date)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($activity->date->format('Y-m-d'))->toBe('2025-10-15');
});

test('activity creation automatically creates ranking records', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create(['base_points' => 100]);
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now(),
        'points_earned' => 100,
    ]);
    
    $currentYear = now()->year;
    $currentSeason = ceil(now()->month / 3);
    
    // Check ranking record was created
    $seasonRanking = \App\Models\Season::where('user_id', $user->id)
        ->where('year', $currentYear)
        ->where('name', $currentSeason)
        ->first();
    
    expect($seasonRanking)->not->toBeNull();
    expect($seasonRanking->points)->toBe(100);
    expect($seasonRanking->season_year_points)->toBe(100);
});

test('activity creation updates user lifetime points', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create(['base_points' => 75]);
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now(),
        'points_earned' => 75,
    ]);
    
    $user->refresh();
    expect($user->lifetime_points)->toBe(75);
});

test('activity on different dates creates appropriate rankings', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create(['base_points' => 50]);
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    // Activity in Q1
    Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => '2025-01-15',
        'points_earned' => 50,
    ]);
    
    // Activity in Q2
    Activity::create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => '2025-04-15',
        'points_earned' => 75,
    ]);
    
    // Check Q1 ranking
    $q1Ranking = \App\Models\Season::where('user_id', $user->id)
        ->where('year', 2025)
        ->where('name', 1)
        ->first();
    
    // Check Q2 ranking
    $q2Ranking = \App\Models\Season::where('user_id', $user->id)
        ->where('year', 2025)
        ->where('name', 2)
        ->first();
    
    expect($q1Ranking->points)->toBe(50);
    expect($q2Ranking->points)->toBe(75);
    
    // Both should have same season_year_points (sum of all quarters)
    expect($q1Ranking->season_year_points)->toBe(125);
    expect($q2Ranking->season_year_points)->toBe(125);
});

test('activity notes can be up to 2000 characters', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    $notes = str_repeat('A', 2000); // Exactly 2000 characters
    
    $response = $this
        ->actingAs($user)
        ->post(route('activities.store'), [
            'habit_id' => $habit->id,
            'notes' => $notes,
        ]);
    
    $response->assertSessionHasNoErrors();
    
    $activity = Activity::first();
    expect($activity->notes)->toBe($notes);
    expect(strlen($activity->notes))->toBe(2000);
});

test('activity notes cannot exceed 2000 characters', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    $habit = $user->habits()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'My Activity',
    ]);
    
    $notes = str_repeat('A', 2001); // 2001 characters - should fail
    
    $response = $this
        ->actingAs($user)
        ->post(route('activities.store'), [
            'habit_id' => $habit->id,
            'notes' => $notes,
        ]);
    
    $response->assertSessionHasErrors(['notes']);
});

