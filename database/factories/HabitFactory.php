<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\User;
use App\Models\ActivityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Habit>
 */
class HabitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'activity_type_id' => ActivityType::factory(),
            'custom_name' => fake()->words(2, true),
            'notes' => fake()->optional(0.5)->sentence(),
            'display_order' => fake()->numberBetween(0, 10),
        ];
    }
}

