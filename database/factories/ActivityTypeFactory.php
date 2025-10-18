<?php

namespace Database\Factories;

use App\Models\ActivityType;
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
        return [
            'name' => fake()->unique()->words(2, true),
            'category' => fake()->randomElement(\App\Enums\ActivityCategory::cases())->value,
            'base_points' => fake()->numberBetween(10, 50),
            'met_value' => fake()->optional(0.6)->randomFloat(1, 2.0, 12.0),
            'icon' => fake()->randomElement(['💪', '🏃', '🧘', '🍎', '🥗', '💤', '🏋️', '🚴', '🏊']),
            'display_order' => fake()->numberBetween(0, 100),
            'description' => fake()->optional(0.7)->sentence(),
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
            'met_value' => fake()->randomFloat(1, 3.0, 12.0),
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
            'met_value' => null,
            'icon' => fake()->randomElement(['🍎', '🥗', '🥑', '🍇', '🥤']),
        ]);
    }

    /**
     * Indicate that the activity type is wellness.
     */
    public function wellness(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => \App\Enums\ActivityCategory::WELLNESS->value,
            'met_value' => null,
            'icon' => fake()->randomElement(['💤', '🧘', '📚', '🎵', '🌿']),
        ]);
    }

    /**
     * Indicate that the activity type is mindfulness.
     */
    public function mindfulness(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => \App\Enums\ActivityCategory::MINDFULNESS->value,
            'met_value' => null,
            'icon' => fake()->randomElement(['🧘‍♀️', '📝', '📚', '🙏', '🎨']),
        ]);
    }
}

