<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ranking;
use Illuminate\Database\Seeder;

class RankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = now()->year;
        $currentSeason = ceil(now()->month / 3);

        // Get all users ordered by current season points
        $users = User::orderBy('current_season_points', 'desc')->get();
        
        // Create season rankings
        $rank = 1;
        foreach ($users as $user) {
            Ranking::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'year' => $currentYear,
                    'season' => $currentSeason,
                ],
                [
                    'points' => $user->current_season_points,
                    'rank' => $rank++,
                ]
            );
        }

        // Create year rankings
        $users = User::orderBy('current_year_points', 'desc')->get();
        $rank = 1;
        foreach ($users as $user) {
            Ranking::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'year' => $currentYear,
                    'season' => null,
                ],
                [
                    'points' => $user->current_year_points,
                    'rank' => $rank++,
                ]
            );
        }

        $this->command->info('Created rankings for current season and year');
    }
}

