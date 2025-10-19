<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ActivityType;
use App\Models\Habit;
use Illuminate\Database\Seeder;

class HabitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $activityTypes = ActivityType::active()->get();

        foreach ($users as $user) {
            // Each user gets 3-7 random habits
            $selectedActivityTypes = $activityTypes->random(rand(3, 7));
            
            $order = 0;
            foreach ($selectedActivityTypes as $activityType) {
                Habit::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'activity_type_id' => $activityType->id,
                    ],
                    [
                        'custom_name' => $activityType->getTranslatedName(),
                        'notes' => fake()->optional(0.3)->sentence(),
                        'display_order' => $order++,
                    ]
                );
            }
        }

        $this->command->info('Created habits for all users');
    }
}

