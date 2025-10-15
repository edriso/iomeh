<?php

use App\Models\Ranking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('ranking belongs to user', function () {
    $user = User::factory()->create();
    
    $ranking = Ranking::create([
        'user_id' => $user->id,
        'season' => 4,
        'points' => 500,
        'rank' => 1,
        'year' => 2025,
    ]);
    
    expect($ranking->user)->toBeInstanceOf(User::class);
    expect($ranking->user->id)->toBe($user->id);
});

test('can get rankings for today', function () {
    $user1 = User::factory()->create(['current_season_points' => 100]);
    $user2 = User::factory()->create(['current_season_points' => 50]);
    
    $rankings = Ranking::getTodayTop();
    
    expect($rankings)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

test('can get rankings for season', function () {
    $user = User::factory()->create();
    
    Ranking::create([
        'user_id' => $user->id,
        'season' => now()->quarter,
        'points' => 500,
        'rank' => 1,
        'year' => now()->year,
    ]);
    
    $rankings = Ranking::season(now()->year, now()->quarter)->get();
    
    expect($rankings)->toHaveCount(1);
    expect($rankings->first()->user_id)->toBe($user->id);
});

test('can get rankings for year', function () {
    $user = User::factory()->create();
    
    Ranking::create([
        'user_id' => $user->id,
        'season' => null,
        'points' => 2000,
        'rank' => 1,
        'year' => now()->year,
    ]);
    
    $rankings = Ranking::year(now()->year)->get();
    
    expect($rankings)->toHaveCount(1);
    expect($rankings->first()->user_id)->toBe($user->id);
});

test('ranking casts year properly', function () {
    $user = User::factory()->create();
    
    $ranking = Ranking::create([
        'user_id' => $user->id,
        'season' => 1,
        'points' => 500,
        'rank' => 1,
        'year' => 2025,
    ]);
    
    expect($ranking->year)->toBeInt();
    expect($ranking->year)->toBe(2025);
});

test('ranking stores points and rank correctly', function () {
    $user = User::factory()->create();
    
    $ranking = Ranking::create([
        'user_id' => $user->id,
        'season' => 2,
        'points' => 1500,
        'rank' => 5,
        'year' => 2025,
    ]);
    
    expect($ranking->points)->toBe(1500);
    expect($ranking->rank)->toBe(5);
    expect($ranking->season)->toBe(2);
});

