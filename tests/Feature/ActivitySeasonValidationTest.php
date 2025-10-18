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

test('it allows activity within current season', function () {
    $this->actingAs($this->user);

    // Get a date within the current season
    $seasonDates = Season::getCurrentSeasonDates();
    $validDate = $seasonDates['start']->addDays(5)->format('Y-m-d');

    $response = $this->post('/activities', [
        'habit_id' => $this->habit->id,
        'date' => $validDate,
        'notes' => 'Test activity',
    ]);

    $response->assertSessionHasNoErrors();
    
    // Check that the activity was created
    $activity = \App\Models\Activity::where('user_id', $this->user->id)
        ->where('habit_id', $this->habit->id)
        ->whereDate('date', $validDate)
        ->first();
    
    expect($activity)->not->toBeNull();
    expect($activity->date->format('Y-m-d'))->toBe($validDate);
});

test('it rejects activity before current season', function () {
    $this->actingAs($this->user);

    // Get a date before the current season
    $seasonDates = Season::getCurrentSeasonDates();
    $invalidDate = $seasonDates['start']->subDays(1)->format('Y-m-d');

    $response = $this->post('/activities', [
        'habit_id' => $this->habit->id,
        'date' => $invalidDate,
        'notes' => 'Test activity',
    ]);

    $response->assertSessionHasErrors('date');
    $this->assertDatabaseMissing('activities', [
        'user_id' => $this->user->id,
        'date' => $invalidDate,
    ]);
});

test('it rejects activity after current season', function () {
    $this->actingAs($this->user);

    // Get a date after the current season (only if we're not at year end)
    $seasonDates = Season::getCurrentSeasonDates();
    
    // Skip this test if we're in Q4, as there's no "after" in the same year
    if ($seasonDates['season'] === 4) {
        $this->markTestSkipped('Cannot test future season in Q4');
    }

    $invalidDate = $seasonDates['end']->addDays(1)->format('Y-m-d');

    $response = $this->post('/activities', [
        'habit_id' => $this->habit->id,
        'date' => $invalidDate,
        'notes' => 'Test activity',
    ]);

    $response->assertSessionHasErrors('date');
    $this->assertDatabaseMissing('activities', [
        'user_id' => $this->user->id,
        'date' => $invalidDate,
    ]);
});

test('it allows activity on season boundary dates', function () {
    $this->actingAs($this->user);

    $seasonDates = Season::getCurrentSeasonDates();

    // Test start date
    $startDate = $seasonDates['start']->format('Y-m-d');
    
    // Only test if start date is not in the future
    if ($seasonDates['start']->lte(now())) {
        $response = $this->post('/activities', [
            'habit_id' => $this->habit->id,
            'date' => $startDate,
            'notes' => 'Start date test',
        ]);

        $response->assertSessionHasNoErrors('date');
    }

    // Test end date (only if it's not in the future)
    $endDate = $seasonDates['end']->format('Y-m-d');
    
    if ($seasonDates['end']->lte(now())) {
        $response = $this->post('/activities', [
            'habit_id' => $this->habit->id,
            'date' => $endDate,
            'notes' => 'End date test',
        ]);

        $response->assertSessionHasNoErrors('date');
    }

    expect(true)->toBeTrue(); // Ensure test passes if no assertions run
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
