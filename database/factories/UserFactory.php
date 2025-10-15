<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'name' => fake()->optional(0.7)->name(),
            'avatar' => null,
            'bio' => fake()->optional(0.6)->paragraph(),
            'website_url' => fake()->optional(0.3)->url(),
            'current_season_points' => fake()->numberBetween(0, 500),
            'current_year_points' => fake()->numberBetween(0, 2000),
            'lifetime_points' => fake()->numberBetween(0, 10000),
            'current_streak' => fake()->numberBetween(0, 50),
            'longest_streak' => fake()->numberBetween(0, 100),
            'last_activity_date' => fake()->optional(0.8)->date(),
            'is_active' => fake()->boolean(98),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user with an avatar.
     */
    public function withAvatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'avatar' => fake()->optional(0.3)->imageUrl(200, 200, 'people'),
        ]);
    }

    /**
     * Create a user with high activity (good streak and points).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_season_points' => fake()->numberBetween(300, 1000),
            'current_year_points' => fake()->numberBetween(1000, 5000),
            'lifetime_points' => fake()->numberBetween(5000, 50000),
            'current_streak' => fake()->numberBetween(7, 100),
            'longest_streak' => fake()->numberBetween(30, 200),
            'last_activity_date' => now()->toDateString(),
        ]);
    }
}
