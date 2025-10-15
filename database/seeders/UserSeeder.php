<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user
        User::firstOrCreate(
            ['username' => 'test'],
            [
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'name' => 'Test User',
                'current_season_points' => 500,
                'current_year_points' => 2000,
                'lifetime_points' => 5000,
                'current_streak' => 10,
                'longest_streak' => 25,
                'last_activity_date' => now()->toDateString(),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Create active demo users with good stats
        User::factory()
            ->active()
            ->count(15)
            ->create();

        // Create some regular users
        User::factory()
            ->count(10)
            ->create();

        $this->command->info('Created users with various activity levels');
    }
}
