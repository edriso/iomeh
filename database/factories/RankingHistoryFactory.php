<?php

namespace Database\Factories;

use App\Models\RankingHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RankingHistory>
 */
class RankingHistoryFactory extends Factory
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
            'season' => fake()->optional(0.7)->numberBetween(1, 4),
            'year' => fake()->year(),
            'points' => fake()->numberBetween(0, 1000),
            'rank' => fake()->numberBetween(1, 1000),
        ];
    }

    /**
     * Indicate that the ranking is for a specific season.
     */
    public function forSeason(int $year, int $season): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
            'season' => $season,
        ]);
    }

    /**
     * Indicate that the ranking is for a full year.
     */
    public function forYear(int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
            'season' => null,
        ]);
    }

    /**
     * Indicate that the ranking is for current season.
     */
    public function currentSeason(): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => now()->year,
            'season' => ceil(now()->month / 3),
        ]);
    }

    /**
     * Indicate that the ranking is for current year.
     */
    public function currentYear(): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => now()->year,
            'season' => null,
        ]);
    }
}

