<?php

use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\User;

test('user cannot exceed maximum of 15 habits', function () {
    $user = User::factory()->create();
    
    // Create 15 habits for the user
    $activityTypes = ActivityType::factory()->count(15)->create();
    
    foreach ($activityTypes as $index => $activityType) {
        $user->habits()->create([
            'activity_type_id' => $activityType->id,
            'custom_name' => "Habit {$index}",
            'display_order' => $index,
        ]);
    }
    
    // Try to add a 16th habit
    $newActivityType = ActivityType::factory()->create();
    
    $response = $this->actingAs($user)
        ->put('/settings/habits', [
            'habits' => array_merge(
                $user->habits()->get()->map(function ($habit) {
                    return [
                        'activity_type_id' => $habit->activity_type_id,
                        'custom_name' => $habit->custom_name,
                        'notes' => $habit->notes,
                    ];
                })->toArray(),
                [
                    [
                        'activity_type_id' => $newActivityType->id,
                        'custom_name' => 'New Habit',
                        'notes' => null,
                    ]
                ]
            )
        ]);
    
    $response->assertSessionHasErrors(['habits']);
    $response->assertSessionHasErrors(['habits' => 'habits.maximum_reached']);
});

test('user can have exactly 15 habits', function () {
    $user = User::factory()->create();
    
    // Create 15 habits for the user
    $activityTypes = ActivityType::factory()->count(15)->create();
    
    $habitsData = [];
    foreach ($activityTypes as $index => $activityType) {
        $habitsData[] = [
            'activity_type_id' => $activityType->id,
            'custom_name' => "Habit {$index}",
            'notes' => null,
        ];
    }
    
    $response = $this->actingAs($user)
        ->put('/settings/habits', [
            'habits' => $habitsData
        ]);
    
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $response->assertSessionHas('success', 'success.habits_updated');
    
    expect($user->habits()->count())->toBe(15);
});

test('user can have less than 15 habits', function () {
    $user = User::factory()->create();
    
    // Create 5 habits for the user
    $activityTypes = ActivityType::factory()->count(5)->create();
    
    $habitsData = [];
    foreach ($activityTypes as $index => $activityType) {
        $habitsData[] = [
            'activity_type_id' => $activityType->id,
            'custom_name' => "Habit {$index}",
            'notes' => null,
        ];
    }
    
    $response = $this->actingAs($user)
        ->put('/settings/habits', [
            'habits' => $habitsData
        ]);
    
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $response->assertSessionHas('success', 'success.habits_updated');
    
    expect($user->habits()->count())->toBe(5);
});

test('user must have at least 1 habit', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->put('/settings/habits', [
            'habits' => []
        ]);
    
    $response->assertSessionHasErrors(['habits']);
});
