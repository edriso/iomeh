<?php

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a test user
    $this->user = User::factory()->create([
        'current_streak' => 0,
        'longest_streak' => 0,
        'last_activity_date' => null,
        'email_verified_at' => now(),
    ]);

    // Create an activity type with base points of 10
    $this->activityType = ActivityType::factory()->create([
        'name' => json_encode(['en' => 'Test Activity']),
        'base_points' => 10,
    ]);

    // Create a habit for the user
    $this->habit = Habit::factory()->create([
        'user_id' => $this->user->id,
        'activity_type_id' => $this->activityType->id,
    ]);

    $this->actingAs($this->user);

    // Helper function to create activities for past days and update streak
    $this->createPastActivity = function ($daysAgo, $points = 10) {
        $date = now()->subDays($daysAgo)->toDateString();
        Activity::create([
            'user_id' => $this->user->id,
            'habit_id' => $this->habit->id,
            'date' => $date,
            'points_earned' => $points,
        ]);
        // Manually update user streak state
        $this->user->last_activity_date = $date;
        if ($this->user->current_streak == 0 || $daysAgo == 0) {
            $this->user->current_streak = 1;
        } else {
            $this->user->current_streak++;
        }
        if ($this->user->current_streak > $this->user->longest_streak) {
            $this->user->longest_streak = $this->user->current_streak;
        }
        $this->user->save();
    };
});

test('it calculates correct points for first activity', function () {
    // First activity ever should use multiplier 1.0 (streak 1)
    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $response->assertStatus(302); // Redirect back with success

    $activity = Activity::latest('id')->first();
    expect($activity->points_earned)->toBe(10); // 10 * 1.0 = 10

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(1);
    expect($this->user->longest_streak)->toBe(1);
    expect($this->user->lifetime_points)->toBe(10);
});

test('it calculates correct points for consecutive days', function () {
    // Day 1: First activity - manually update streak since we're creating directly
    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDay()->toDateString(),
        'points_earned' => 10, // 10 * 1.0
    ]);
    // Manually set the user state as if the activity was logged yesterday
    $this->user->current_streak = 1;
    $this->user->longest_streak = 1;
    $this->user->last_activity_date = now()->subDay()->toDateString();
    $this->user->save();

    // Day 2: Consecutive day (streak 1 -> 2, multiplier still 1.0)
    $this->user->refresh();
    expect($this->user->current_streak)->toBe(1); // After day 1

    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $activity = Activity::latest('id')->first();
    expect($activity->points_earned)->toBe(10); // 10 * 1.0 (using streak 1)

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(2);
    expect($this->user->lifetime_points)->toBe(20);
});

test('it uses correct multiplier for day 3', function () {
    // Build up to day 3 to test multiplier progression
    // Skip auto-update for test setup
    Activity::$skipAutoUpdate = true;

    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDays(2)->toDateString(),
        'points_earned' => 10,
    ]);

    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDay()->toDateString(),
        'points_earned' => 10,
    ]);

    // Manually set user state as if they completed 2 consecutive days
    $this->user->current_streak = 2;
    $this->user->longest_streak = 2;
    $this->user->last_activity_date = now()->subDay()->toDateString();
    $this->user->save();

    Activity::$skipAutoUpdate = false;

    // Day 3: Should use multiplier 1.0 (streak 2) before incrementing to 3
    $this->user->refresh();
    expect($this->user->current_streak)->toBe(2);

    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $activity = Activity::latest('id')->first();
    expect($activity->points_earned)->toBe(10); // 10 * 1.0 (using streak 2)

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(3);
});

test('it uses correct multiplier for day 4', function () {
    // Build up to day 4 to test 1.2 multiplier
    Activity::$skipAutoUpdate = true;

    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDays(3)->toDateString(),
        'points_earned' => 10,
    ]);

    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDays(2)->toDateString(),
        'points_earned' => 10,
    ]);

    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDay()->toDateString(),
        'points_earned' => 10,
    ]);

    // Manually set user state as if they completed 3 consecutive days
    $this->user->current_streak = 3;
    $this->user->longest_streak = 3;
    $this->user->last_activity_date = now()->subDay()->toDateString();
    $this->user->save();

    Activity::$skipAutoUpdate = false;

    // Day 4: Should use multiplier 1.2 (streak 3)
    $this->user->refresh();
    expect($this->user->current_streak)->toBe(3);

    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $activity = Activity::latest('id')->first();
    expect($activity->points_earned)->toBe(12); // 10 * 1.2 (using streak 3)

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(4);
});

test('it resets streak and uses multiplier 1 after gap', function () {
    // Build up a streak of 3
    Activity::$skipAutoUpdate = true;

    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDays(10)->toDateString(),
        'points_earned' => 10,
    ]);

    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDays(9)->toDateString(),
        'points_earned' => 10,
    ]);

    Activity::create([
        'user_id' => $this->user->id,
        'habit_id' => $this->habit->id,
        'date' => now()->subDays(8)->toDateString(),
        'points_earned' => 10,
    ]);

    // Manually set user state as if they completed 3 consecutive days (10 days ago)
    $this->user->current_streak = 3;
    $this->user->longest_streak = 3;
    $this->user->last_activity_date = now()->subDays(8)->toDateString();
    $this->user->save();

    Activity::$skipAutoUpdate = false;

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(3);
    expect($this->user->longest_streak)->toBe(3);

    // Now skip several days and post again - should reset to streak 1, multiplier 1.0
    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $activity = Activity::latest('id')->first();
    expect($activity->points_earned)->toBe(10); // 10 * 1.0 (streak reset to 1)

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(1);
    expect($this->user->longest_streak)->toBe(3); // Longest should remain 3
    expect($this->user->lifetime_points)->toBe(40); // 3 old + 1 new
});

test('it handles multiple activities on same day', function () {
    // First activity on day 1
    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(1);
    expect($this->user->lifetime_points)->toBe(10);

    // Second activity on the same day - should use same streak (1)
    $habit2 = Habit::factory()->create([
        'user_id' => $this->user->id,
        'activity_type_id' => $this->activityType->id,
    ]);

    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $habit2->id,
    ]);

    $activity = Activity::latest('id')->first();
    expect($activity->points_earned)->toBe(10); // 10 * 1.0 (same streak)

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(1); // Streak doesn't increment for same day
    expect($this->user->lifetime_points)->toBe(20);
});

test('it awards milestone bonus at streak 7', function () {
    // Build up to day 6
    Activity::$skipAutoUpdate = true;

    for ($i = 6; $i >= 1; $i--) {
        Activity::create([
            'user_id' => $this->user->id,
            'habit_id' => $this->habit->id,
            'date' => now()->subDays($i)->toDateString(),
            'points_earned' => 10,
        ]);
    }

    // Manually set user state as if they completed 6 consecutive days
    $this->user->current_streak = 6;
    $this->user->longest_streak = 6;
    $this->user->last_activity_date = now()->subDay()->toDateString();
    $this->user->save();

    Activity::$skipAutoUpdate = false;

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(6);

    // Day 7: Should get milestone bonus of 50 points
    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $activity = Activity::latest('id')->first();
    // Base: 10 * 1.2 (streak 6) = 12
    // Milestone bonus: 50
    // Total: 62
    expect($activity->points_earned)->toBe(62);

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(7);
});

test('it prevents duplicate activity for same habit on same day', function () {
    // First activity
    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $response->assertStatus(302); // Redirect back with success
    expect(Activity::count())->toBe(1);

    // Try to log same habit again on same day
    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $response->assertSessionHasErrors(['habit_id']);
    expect(Activity::count())->toBe(1); // Still only 1 activity
});

test('it calculates points correctly at streak tier boundaries', function () {
    // Test at streak 7 (multiplier changes from 1.2 to 1.5)
    Activity::$skipAutoUpdate = true;

    for ($i = 7; $i >= 1; $i--) {
        Activity::create([
            'user_id' => $this->user->id,
            'habit_id' => $this->habit->id,
            'date' => now()->subDays($i)->toDateString(),
            'points_earned' => 10,
        ]);
    }

    // Manually set user state as if they completed 7 consecutive days
    $this->user->current_streak = 7;
    $this->user->longest_streak = 7;
    $this->user->last_activity_date = now()->subDay()->toDateString();
    $this->user->save();

    Activity::$skipAutoUpdate = false;

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(7);

    // Day 8: Should use multiplier 1.5 (streak 7)
    $response = $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);

    $activity = Activity::latest('id')->first();
    expect($activity->points_earned)->toBe(15); // 10 * 1.5

    $this->user->refresh();
    expect($this->user->current_streak)->toBe(8);
});

test('additional same-day activities use the same streak multiplier as the first', function () {
    // Build a 7-day streak (multiplier 1.5x) ending yesterday.
    Activity::$skipAutoUpdate = true;
    for ($i = 7; $i >= 1; $i--) {
        Activity::create([
            'user_id' => $this->user->id,
            'habit_id' => $this->habit->id,
            'date' => now()->subDays($i)->toDateString(),
            'points_earned' => 10,
        ]);
    }
    $this->user->current_streak = 7;
    $this->user->longest_streak = 7;
    $this->user->last_activity_date = now()->subDay()->toDateString();
    $this->user->save();
    Activity::$skipAutoUpdate = false;

    // First activity today -> scored with streak 7 (1.5x) = 15
    $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $this->habit->id,
    ]);
    expect(Activity::latest('id')->first()->points_earned)->toBe(15);

    // Second activity today (different habit) -> must use the SAME 1.5x multiplier = 15
    $habit2 = Habit::factory()->create([
        'user_id' => $this->user->id,
        'activity_type_id' => $this->activityType->id,
    ]);
    $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $habit2->id,
    ]);
    expect(Activity::latest('id')->first()->points_earned)->toBe(15);

    // A third same-day activity must also be consistent.
    $habit3 = Habit::factory()->create([
        'user_id' => $this->user->id,
        'activity_type_id' => $this->activityType->id,
    ]);
    $this->withoutMiddleware()->postJson(route('activities.store'), [
        'habit_id' => $habit3->id,
    ]);
    expect(Activity::latest('id')->first()->points_earned)->toBe(15);
});
