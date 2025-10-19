<?php

use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\Season;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create();

    // Create an activity type
    $activityType = ActivityType::factory()->create([
        'name' => 'Running',
        'base_points' => 10,
    ]);

    // Create a habit for the user
    $this->habit = Habit::factory()->create([
        'user_id' => $this->user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Morning Run',
    ]);
});

test('it allows activity for today only', function () {
    $this->actingAs($this->user);

    // Get CSRF token
    $this->get('/');
    $csrfToken = $this->app['session']->token();

    $response = $this->post('/activities', [
        'habit_id' => $this->habit->id,
        'notes' => 'Test activity',
        '_token' => $csrfToken,
    ]);

    $response->assertSessionHasNoErrors();
    
    // Check that the activity was created with today's date
    $activity = \App\Models\Activity::where('user_id', $this->user->id)
        ->where('habit_id', $this->habit->id)
        ->whereDate('date', now()->toDateString())
        ->first();
    
    expect($activity)->not->toBeNull();
    expect($activity->date->format('Y-m-d'))->toBe(now()->toDateString());
});

test('it creates activity with today date when no date provided', function () {
    $this->actingAs($this->user);

    // Get CSRF token
    $this->get('/');
    $csrfToken = $this->app['session']->token();

    $response = $this->post('/activities', [
        'habit_id' => $this->habit->id,
        'notes' => 'Test activity',
        '_token' => $csrfToken,
    ]);

    $response->assertSessionHasNoErrors();
    
    // Check that the activity was created with today's date
    $activity = \App\Models\Activity::where('user_id', $this->user->id)
        ->where('habit_id', $this->habit->id)
        ->whereDate('date', now()->toDateString())
        ->first();
    
    expect($activity)->not->toBeNull();
    expect($activity->date->format('Y-m-d'))->toBe(now()->toDateString());
});

test('it ignores any date field sent from frontend', function () {
    $this->actingAs($this->user);

    // Get CSRF token
    $this->get('/');
    $csrfToken = $this->app['session']->token();

    // Even if frontend sends a date, backend should ignore it and use today
    $response = $this->post('/activities', [
        'habit_id' => $this->habit->id,
        'date' => '2020-01-01', // Old date that should be ignored
        'notes' => 'Test activity',
        '_token' => $csrfToken,
    ]);

    $response->assertSessionHasNoErrors();
    
    // Check that the activity was created with today's date, not the sent date
    $activity = \App\Models\Activity::where('user_id', $this->user->id)
        ->where('habit_id', $this->habit->id)
        ->whereDate('date', now()->toDateString())
        ->first();
    
    expect($activity)->not->toBeNull();
    expect($activity->date->format('Y-m-d'))->toBe(now()->toDateString());
    expect($activity->date->format('Y-m-d'))->not->toBe('2020-01-01');
});

test('season helper methods work correctly', function () {
    // Test Q1 (Jan-Mar)
    Carbon::setTestNow(Carbon::create(2025, 2, 15)); // Feb 15
    expect(Season::getCurrentSeasonNumber())->toBe(1);
    $dates = Season::getCurrentSeasonDates();
    expect($dates['start']->format('Y-m-d'))->toBe('2025-01-01');
    expect($dates['end']->format('Y-m-d'))->toBe('2025-03-31');
    expect(Season::isDateInCurrentSeason('2025-02-15'))->toBeTrue();
    expect(Season::isDateInCurrentSeason('2024-12-31'))->toBeFalse();
    expect(Season::isDateInCurrentSeason('2025-04-01'))->toBeFalse();

    // Test Q2 (Apr-Jun)
    Carbon::setTestNow(Carbon::create(2025, 5, 20)); // May 20
    expect(Season::getCurrentSeasonNumber())->toBe(2);
    $dates = Season::getCurrentSeasonDates();
    expect($dates['start']->format('Y-m-d'))->toBe('2025-04-01');
    expect($dates['end']->format('Y-m-d'))->toBe('2025-06-30');
    expect(Season::isDateInCurrentSeason('2025-05-20'))->toBeTrue();
    expect(Season::isDateInCurrentSeason('2025-03-31'))->toBeFalse();
    expect(Season::isDateInCurrentSeason('2025-07-01'))->toBeFalse();

    // Test Q3 (Jul-Sep)
    Carbon::setTestNow(Carbon::create(2025, 8, 10)); // Aug 10
    expect(Season::getCurrentSeasonNumber())->toBe(3);
    $dates = Season::getCurrentSeasonDates();
    expect($dates['start']->format('Y-m-d'))->toBe('2025-07-01');
    expect($dates['end']->format('Y-m-d'))->toBe('2025-09-30');
    expect(Season::isDateInCurrentSeason('2025-08-10'))->toBeTrue();
    expect(Season::isDateInCurrentSeason('2025-06-30'))->toBeFalse();
    expect(Season::isDateInCurrentSeason('2025-10-01'))->toBeFalse();

    // Test Q4 (Oct-Dec)
    Carbon::setTestNow(Carbon::create(2025, 11, 25)); // Nov 25
    expect(Season::getCurrentSeasonNumber())->toBe(4);
    $dates = Season::getCurrentSeasonDates();
    expect($dates['start']->format('Y-m-d'))->toBe('2025-10-01');
    expect($dates['end']->format('Y-m-d'))->toBe('2025-12-31');
    expect(Season::isDateInCurrentSeason('2025-11-25'))->toBeTrue();
    expect(Season::isDateInCurrentSeason('2025-09-30'))->toBeFalse();
    expect(Season::isDateInCurrentSeason('2026-01-01'))->toBeFalse();

    // Reset the test time
    Carbon::setTestNow();
});
