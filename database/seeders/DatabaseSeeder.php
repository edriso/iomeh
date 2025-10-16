<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            ActivityTypeSeeder::class,  // Create activity types first
            UserSeeder::class,           // Create users
            HabitSeeder::class,       // Create habits (depends on users and activity types)
            ActivitySeeder::class,       // Create activities (depends on users and habits)
            RankingSeeder::class,        // Recalculate seasons (depends on users, runs last)
        ];

        foreach ($seeders as $seeder) {
            $this->command->info("Running {$seeder}...");
            $this->call($seeder);
            
            // Small delay to prevent database locks
            usleep(100000); // 0.1 second delay
        }

        $this->command->info('Database seeded successfully');
    }
}
