<?php

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'current_streak' => 0, 'longest_streak' => 0,
        'last_activity_date' => null, 'email_verified_at' => now(),
    ]);
    $this->activityType = ActivityType::factory()->create([
        'name' => json_encode(['en' => 'Test Activity']), 'base_points' => 10,
    ]);
    $this->habit = Habit::factory()->create([
        'user_id' => $this->user->id, 'activity_type_id' => $this->activityType->id,
    ]);
    $this->actingAs($this->user);
});

test('home page reflects newly logged activity (cache invalidation)', function () {
    // First load home -> populates cache
    $resp = $this->get('/');
    $resp->assertInertia(fn ($p) =>
        $p->component('Home')
          ->where('today_activities', [])
          ->where('habits.0.has_activity_today', false)
    );

    // Log an activity
    $this->post(route('activities.store'), ['habit_id' => $this->habit->id])
        ->assertRedirect();

    // Reload home -> should reflect the new activity, NOT stale cache
    $this->get('/')->assertInertia(fn ($p) =>
        $p->component('Home')
          ->where('habits.0.has_activity_today', true)
          ->has('today_activities', 1)
          ->where('user.lifetime_points', 10)
    );
});

test('home reflects deleted activity', function () {
    $this->post(route('activities.store'), ['habit_id' => $this->habit->id]);
    $activity = Activity::latest('id')->first();
    $this->get('/'); // cache with the activity
    $this->delete(route('activities.destroy', $activity))->assertRedirect();
    $this->get('/')->assertInertia(fn ($p) =>
        $p->where('habits.0.has_activity_today', false)
          ->has('today_activities', 0)
    );
});
