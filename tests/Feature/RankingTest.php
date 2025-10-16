<?php

use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('ranking belongs to user', function () {
    $user = User::factory()->create();
    
    $ranking = Season::create([
        'user_id' => $user->id,
        'name' => 4,
        'points' => 500,
        'season_year_points' => 500,
        'year' => 2025,
    ]);
    
    expect($ranking->user)->toBeInstanceOf(User::class);
    expect($ranking->user->id)->toBe($user->id);
});

test('can get rankings for today', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $rankings = Season::getTodayTop();
    
    expect($rankings)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

test('can get rankings for season', function () {
    $user = User::factory()->create();
    
    Season::create([
        'user_id' => $user->id,
        'name' => now()->quarter,
        'points' => 500,
        'season_year_points' => 500,
        'year' => now()->year,
    ]);
    
    $rankings = Season::forSeason(now()->year, now()->quarter)->get();
    
    expect($rankings)->toHaveCount(1);
    expect($rankings->first()->user_id)->toBe($user->id);
});

test('can get rankings for year', function () {
    $user = User::factory()->create();
    
    // Create a season ranking with season_year_points
    Season::create([
        'user_id' => $user->id,
        'name' => 1,
        'points' => 500,
        'season_year_points' => 2000,
        'year' => now()->year,
    ]);
    
    $rankings = Season::year(now()->year)->get();
    
    expect($rankings)->toHaveCount(1);
    expect($rankings->first()->user_id)->toBe($user->id);
});

test('ranking casts year properly', function () {
    $user = User::factory()->create();
    
    $ranking = Season::create([
        'user_id' => $user->id,
        'name' => 1,
        'points' => 500,
        'season_year_points' => 500,
        'year' => 2025,
    ]);
    
    expect($ranking->year)->toBeInt();
    expect($ranking->year)->toBe(2025);
});

test('ranking stores points correctly', function () {
    $user = User::factory()->create();
    
    $ranking = Season::create([
        'user_id' => $user->id,
        'name' => 2,
        'points' => 1500,
        'season_year_points' => 3000,
        'year' => 2025,
    ]);
    
    expect($ranking->points)->toBe(1500);
    expect($ranking->season_year_points)->toBe(3000);
    expect($ranking->name)->toBe(2);
});

test('activity in current quarter creates season ranking with both points', function () {
    $user = User::factory()->create();
    $activityType = \App\Models\ActivityType::factory()->create(['base_points' => 50]);
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Test Activity',
    ]);
    
    $activity = \App\Models\Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    $currentYear = now()->year;
    $currentSeason = ceil(now()->month / 3);
    
    // Check season ranking was created with both points and season_year_points
    $seasonSeason = \App\Models\Season::where('user_id', $user->id)
        ->where('year', $currentYear)
        ->where('name', $currentSeason)
        ->first();
    
    expect($seasonSeason)->not->toBeNull();
    expect($seasonSeason->points)->toBe(50);
    expect($seasonSeason->season_year_points)->toBe(50);
    
    // Check user's current_season_id is set
    $user->refresh();
    expect($user->current_season_id)->toBe($seasonSeason->id);
});

test('activity in next quarter creates new season ranking and updates season_year_points', function () {
    $user = User::factory()->create();
    $activityType = \App\Models\ActivityType::factory()->create(['base_points' => 50]);
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Test Activity',
    ]);
    
    // Create activity in current quarter
    \App\Models\Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    // Simulate activity in next quarter (add 3 months)
    $nextQuarterDate = now()->addMonths(3);
    $nextSeason = ceil($nextQuarterDate->month / 3);
    
    \App\Models\Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => $nextQuarterDate,
        'points_earned' => 75,
    ]);
    
    // Check both season rankings exist
    $currentSeason = ceil(now()->month / 3);
    $currentSeason = \App\Models\Season::where('user_id', $user->id)
        ->where('year', now()->year)
        ->where('name', $currentSeason)
        ->first();
    
    $nextSeason = \App\Models\Season::where('user_id', $user->id)
        ->where('year', $nextQuarterDate->year)
        ->where('name', $nextSeason)
        ->first();
    
    expect($currentSeason->points)->toBe(50);
    expect($nextSeason->points)->toBe(75);
    
    // If same year, season_year_points should be sum of all quarters
    if (now()->year === $nextQuarterDate->year) {
        expect($currentSeason->season_year_points)->toBe(125);
        expect($nextSeason->season_year_points)->toBe(125);
    }
});

test('activity in next year creates separate year rankings', function () {
    $user = User::factory()->create();
    $activityType = \App\Models\ActivityType::factory()->create(['base_points' => 100]);
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Test Activity',
    ]);
    
    // Create activity in current year
    \App\Models\Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now(),
        'points_earned' => 100,
    ]);
    
    // Create activity in next year (January)
    $nextYear = now()->year + 1;
    $nextYearDate = \Carbon\Carbon::create($nextYear, 1, 15);
    
    \App\Models\Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => $nextYearDate,
        'points_earned' => 200,
    ]);
    
    // Check current year season ranking
    $currentYearSeason = \App\Models\Season::where('user_id', $user->id)
        ->where('year', now()->year)
        ->first();
    
    expect($currentYearSeason)->not->toBeNull();
    expect($currentYearSeason->points)->toBe(100);
    expect($currentYearSeason->season_year_points)->toBe(100);
    
    // Check next year Q1 ranking
    $nextYearQ1Season = \App\Models\Season::where('user_id', $user->id)
        ->where('year', $nextYear)
        ->where('name', 1)
        ->first();
    
    expect($nextYearQ1Season)->not->toBeNull();
    expect($nextYearQ1Season->points)->toBe(200);
    expect($nextYearQ1Season->season_year_points)->toBe(200);
});

test('multiple activities in same quarter accumulate points', function () {
    $user = User::factory()->create();
    $activityType = \App\Models\ActivityType::factory()->create(['base_points' => 50]);
    $interest = $user->interests()->create([
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Test Activity',
    ]);
    
    // Create 3 activities in current quarter
    \App\Models\Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now(),
        'points_earned' => 50,
    ]);
    
    \App\Models\Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now()->addDays(1),
        'points_earned' => 75,
    ]);
    
    \App\Models\Activity::create([
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'date' => now()->addDays(2),
        'points_earned' => 100,
    ]);
    
    $currentYear = now()->year;
    $currentSeason = ceil(now()->month / 3);
    
    $seasonSeason = \App\Models\Season::where('user_id', $user->id)
        ->where('year', $currentYear)
        ->where('name', $currentSeason)
        ->first();
    
    expect($seasonSeason->points)->toBe(225);
    expect($seasonSeason->season_year_points)->toBe(225);
    expect($user->fresh()->lifetime_points)->toBe(225);
});

