<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Season;
use App\Models\RankingHistory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user
        $testUser = User::firstOrCreate(
            ['username' => 'test'],
            [
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'name' => 'Test User',
                'current_streak' => 10,
                'longest_streak' => 25,
                'last_activity_date' => now()->toDateString(),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Add historical season records for test user
        $this->createHistoricalSeasons($testUser);
        
        // Add historical ranking history for test user
        $this->createRankingHistory($testUser);

        // Create active demo users with good stats
        User::factory()
            ->active()
            ->count(15)
            ->create();

        // Create some regular users
        User::factory()
            ->count(10)
            ->create();

        $this->command->info('Created users with various activity levels and historical data');
    }

    /**
     * Create historical season records for a user
     */
    private function createHistoricalSeasons(User $user): void
    {
        $currentYear = now()->year;
        $currentSeason = ceil(now()->month / 3);
        
        // Create seasons for last year (2024)
        $lastYear = $currentYear - 1;
        $seasonData = [
            ['name' => 1, 'points' => 450, 'season_year_points' => 1850],
            ['name' => 2, 'points' => 520, 'season_year_points' => 1850],
            ['name' => 3, 'points' => 380, 'season_year_points' => 1850],
            ['name' => 4, 'points' => 500, 'season_year_points' => 1850],
        ];
        
        foreach ($seasonData as $data) {
            Season::create([
                'user_id' => $user->id,
                'year' => $lastYear,
                'name' => $data['name'],
                'points' => $data['points'],
                'season_year_points' => $data['season_year_points'],
            ]);
        }
        
        // Create seasons for year before last (2023)
        $twoYearsAgo = $currentYear - 2;
        $seasonData2023 = [
            ['name' => 1, 'points' => 320, 'season_year_points' => 1200],
            ['name' => 2, 'points' => 290, 'season_year_points' => 1200],
            ['name' => 3, 'points' => 310, 'season_year_points' => 1200],
            ['name' => 4, 'points' => 280, 'season_year_points' => 1200],
        ];
        
        foreach ($seasonData2023 as $data) {
            Season::create([
                'user_id' => $user->id,
                'year' => $twoYearsAgo,
                'name' => $data['name'],
                'points' => $data['points'],
                'season_year_points' => $data['season_year_points'],
            ]);
        }
        
        // Create partial seasons for current year (previous quarters if applicable)
        for ($q = 1; $q < $currentSeason; $q++) {
            Season::create([
                'user_id' => $user->id,
                'year' => $currentYear,
                'name' => $q,
                'points' => fake()->numberBetween(200, 600),
                'season_year_points' => 0, // Will be calculated by ActivitySeeder
            ]);
        }
    }

    /**
     * Create ranking history records for a user
     */
    private function createRankingHistory(User $user): void
    {
        $currentYear = now()->year;
        
        // Add 2024 ranking history (completed year)
        $lastYear = $currentYear - 1;
        
        // Quarterly rankings for 2024
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $lastYear,
            'season' => 1,
            'points' => 450,
            'rank' => 15,
        ]);
        
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $lastYear,
            'season' => 2,
            'points' => 520,
            'rank' => 12,
        ]);
        
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $lastYear,
            'season' => 3,
            'points' => 380,
            'rank' => 23,
        ]);
        
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $lastYear,
            'season' => 4,
            'points' => 500,
            'rank' => 18,
        ]);
        
        // Yearly ranking for 2024
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $lastYear,
            'season' => null, // null = yearly
            'points' => 1850,
            'rank' => 28,
        ]);
        
        // Add 2023 ranking history (completed year)
        $twoYearsAgo = $currentYear - 2;
        
        // Quarterly rankings for 2023
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $twoYearsAgo,
            'season' => 1,
            'points' => 320,
            'rank' => 42,
        ]);
        
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $twoYearsAgo,
            'season' => 2,
            'points' => 290,
            'rank' => 51,
        ]);
        
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $twoYearsAgo,
            'season' => 3,
            'points' => 310,
            'rank' => 48,
        ]);
        
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $twoYearsAgo,
            'season' => 4,
            'points' => 280,
            'rank' => 55,
        ]);
        
        // Yearly ranking for 2023
        RankingHistory::create([
            'user_id' => $user->id,
            'year' => $twoYearsAgo,
            'season' => null, // null = yearly
            'points' => 1200,
            'rank' => 67,
        ]);
    }
}
