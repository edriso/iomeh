<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ActivityType;
use App\Models\Interest;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $activityTypes = ActivityType::active()->get();

        foreach ($users as $user) {
            // Each user gets 3-7 random interests
            $selectedActivityTypes = $activityTypes->random(rand(3, 7));
            
            $order = 0;
            foreach ($selectedActivityTypes as $activityType) {
                Interest::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'activity_type_id' => $activityType->id,
                    ],
                    [
                        'custom_name' => $activityType->name,
                        'notes' => fake()->optional(0.3)->sentence(),
                        'display_order' => $order++,
                    ]
                );
            }
        }

        $this->command->info('Created interests for all users');
    }
}

