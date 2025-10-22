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

test('activities are transferred when habits are updated with same activity type', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    
    // Create initial habit
    $originalHabit = Habit::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Original Habit',
        'display_order' => 0,
        'is_active' => true,
    ]);
    
    // Create an activity for today
    $activity = \App\Models\Activity::create([
        'user_id' => $user->id,
        'habit_id' => $originalHabit->id,
        'date' => now()->toDateString(),
        'points_earned' => 50,
    ]);
    
    // Verify activity exists and habit has activity today
    expect($originalHabit->hasActivityToday())->toBeTrue();
    expect($activity->habit_id)->toBe($originalHabit->id);
    
    // Simulate habit update via HabitsController (edit habit name, keeping same activity type)
    $this->actingAs($user);
    $response = $this->put('/settings/habits', [
        'habits' => [
            [
                'id' => $originalHabit->id,
                'activity_type_id' => $activityType->id,
                'custom_name' => 'Updated Habit Name',
                'custom_icon' => null,
                'notes' => null,
            ],
        ],
    ]);
    
    $response->assertRedirect();
    
    // Verify the habit was updated (not recreated)
    $updatedHabit = $user->activeHabits()->first();
    expect($updatedHabit->id)->toBe($originalHabit->id);
    expect($updatedHabit->custom_name)->toBe('Updated Habit Name');
    expect($updatedHabit->hasActivityToday())->toBeTrue();
    
    // Verify activity is still linked to the same habit
    $activity->refresh();
    expect($activity->habit_id)->toBe($originalHabit->id);
});

test('activities are transferred when habit is recreated with same activity type', function () {
    $user = User::factory()->create();
    $activityType = ActivityType::factory()->create();
    
    // Create initial habit
    $originalHabit = Habit::create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Original Habit',
        'display_order' => 0,
        'is_active' => true,
    ]);
    
    // Create an activity for today
    $activity = \App\Models\Activity::create([
        'user_id' => $user->id,
        'habit_id' => $originalHabit->id,
        'date' => now()->toDateString(),
        'points_earned' => 50,
    ]);
    
    // Verify activity exists and habit has activity today
    expect($originalHabit->hasActivityToday())->toBeTrue();
    
    // Simulate habit recreation (frontend sends no ID, forcing recreation)
    $this->actingAs($user);
    $response = $this->put('/settings/habits', [
        'habits' => [
            [
                // No ID - this will force creation of new habit
                'activity_type_id' => $activityType->id,
                'custom_name' => 'Recreated Habit',
                'custom_icon' => null,
                'notes' => null,
            ],
        ],
    ]);
    
    $response->assertRedirect();
    
    // Verify original habit was soft deleted
    $originalHabit->refresh();
    expect($originalHabit->is_active)->toBeFalse();
    
    // Verify new habit was created
    $newHabit = $user->activeHabits()->first();
    expect($newHabit->id)->not->toBe($originalHabit->id);
    expect($newHabit->custom_name)->toBe('Recreated Habit');
    expect($newHabit->activity_type_id)->toBe($activityType->id);
    
    // Most importantly: verify activity was transferred to new habit
    $activity->refresh();
    expect($activity->habit_id)->toBe($newHabit->id);
    expect($newHabit->hasActivityToday())->toBeTrue();
});

