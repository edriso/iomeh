<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use App\Models\Interest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
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
            'interest_id' => Interest::factory(),
            'date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'points_earned' => fake()->numberBetween(5, 50),
            'notes' => fake()->optional(0.5)->sentence(),
            'proof_url' => fake()->optional(0.2)->url(),
        ];
    }

    /**
     * Indicate that the activity is from today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->toDateString(),
        ]);
    }

    /**
     * Indicate that the activity is from yesterday.
     */
    public function yesterday(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->subDay()->toDateString(),
        ]);
    }

    /**
     * Indicate that the activity has notes.
     */
    public function withNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate that the activity has proof.
     */
    public function withProof(): static
    {
        return $this->state(fn (array $attributes) => [
            'proof_url' => fake()->url(),
        ]);
    }
}

