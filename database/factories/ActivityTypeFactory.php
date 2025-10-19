<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityType>
 */
class ActivityTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $description = fake()->optional(0.7)->sentence();
        
        return [
            'name' => [
                'en' => $name,
                'ar' => fake()->words(2, true), // Generate Arabic-like text
            ],
            'locale' => 'activity.' . strtolower(str_replace(' ', '_', $name)),
            'category' => fake()->randomElement(\App\Enums\ActivityCategory::cases())->value,
            'base_points' => fake()->numberBetween(10, 50),
            'icon' => fake()->randomElement(['💪', '🏃', '🧘', '🍎', '🥗', '💤', '🏋️', '🚴', '🏊']),
            'display_order' => fake()->numberBetween(0, 100),
            'description' => $description ? [
                'en' => $description,
                'ar' => fake()->sentence(), // Generate Arabic-like text
            ] : null,
            'is_active' => fake()->boolean(95),
        ];
    }

    /**
     * Indicate that the activity type is a workout.
     */
    public function workout(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => \App\Enums\ActivityCategory::WORKOUT->value,
            'icon' => fake()->randomElement(['💪', '🏃', '🧘', '🏋️', '🚴', '🏊']),
        ]);
    }

    /**
     * Indicate that the activity type is nutrition.
     */
    public function nutrition(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => \App\Enums\ActivityCategory::NUTRITION->value,
            'icon' => fake()->randomElement(['🍎', '🥗', '🥑', '🍇', '🥤']),
        ]);
    }
}

