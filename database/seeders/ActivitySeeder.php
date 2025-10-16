<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable auto-updates during seeding for better performance
        Activity::$skipAutoUpdate = true;
        
        $users = User::with('habits')->get();

        foreach ($users as $user) {
            if ($user->habits->isEmpty()) {
                continue;
            }

            // Create activities for the last 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays($i)->toDateString();
                
                // 70% chance of having activity on any given day
                if (rand(1, 100) <= 70) {
                    // User does 1-3 activities per active day
                    $activitiesPerDay = rand(1, min(3, $user->habits->count()));
                    $selectedHabits = $user->habits->random($activitiesPerDay);

                    foreach ($selectedHabits as $habit) {
                        // Get base points from activity type
                        $basePoints = $habit->activityType->base_points;
                        
                        // Add some variance (-5 to +10 points)
                        $points = $basePoints + rand(-5, 10);
                        $points = max(5, $points); // Minimum 5 points
                        
                        // Generate more realistic notes and memory URLs for better testing
                        $activityNotes = [
                            'Morning workout felt great! 💪',
                            'Hit a new personal record today',
                            'Feeling energized after this session',
                            'Great progress on my fitness journey',
                            'Pushed through the tough parts',
                            'Awesome workout with friends',
                            'Feeling stronger every day',
                            'Perfect weather for outdoor activity',
                            'Completed with full focus',
                            'Best session this week!',
                        ];
                        
                        $memoryUrls = [
                            'https://www.strava.com/activities/123456',
                            'https://www.instagram.com/p/example',
                            'https://example.com/workout-proof',
                            'https://www.youtube.com/watch?v=example',
                        ];
                        
                        Activity::create([
                            'user_id' => $user->id,
                            'habit_id' => $habit->id,
                            'date' => $date,
                            'points_earned' => $points,
                            'notes' => fake()->optional(0.6)->randomElement($activityNotes),
                            'memory_url' => fake()->optional(0.3)->randomElement($memoryUrls),
                        ]);
                    }
                }
            }
        }

        // Re-enable auto-updates
        Activity::$skipAutoUpdate = false;
        
        // Now manually calculate points, streaks and seasons for all users
        $this->command->info('Calculating points, streaks and seasons for seeded users...');
        
        foreach ($users as $user) {
            $user->refresh();
            
            // Get all activities for this user
            $activities = Activity::where('user_id', $user->id)
                ->orderBy('date', 'asc')
                ->get();
            
            // Update seasons for each activity date
            foreach ($activities as $activity) {
                $user->updateSeasons($activity->date, $activity->points_earned);
            }
            
            // Calculate streak from most recent activities
            $this->calculateStreak($user);
        }

        $this->command->info('Created activities for the last 30 days with updated points, streaks and seasons');
    }
    
    /**
     * Calculate and update user's streak based on their activities
     */
    private function calculateStreak(User $user): void
    {
        $activities = Activity::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->values();
        
        if ($activities->isEmpty()) {
            return;
        }
        
        $currentStreak = 0;
        $longestStreak = 0;
        $tempStreak = 1;
        
        // Check if there's activity today or yesterday
        $lastActivityDate = $activities->first();
        $daysSinceLastActivity = now()->diffInDays(Carbon::parse($lastActivityDate));
        
        if ($daysSinceLastActivity > 1) {
            // Streak is broken
            $user->update([
                'current_streak' => 0,
                'longest_streak' => $longestStreak,
                'last_activity_date' => $lastActivityDate,
            ]);
            return;
        }
        
        // Calculate current streak
        $currentStreak = 1;
        for ($i = 0; $i < $activities->count() - 1; $i++) {
            $date1 = Carbon::parse($activities[$i]);
            $date2 = Carbon::parse($activities[$i + 1]);
            
            if ($date1->diffInDays($date2) === 1) {
                $currentStreak++;
            } else {
                break;
            }
        }
        
        // Calculate longest streak
        $tempStreak = 1;
        $longestStreak = 1;
        for ($i = 0; $i < $activities->count() - 1; $i++) {
            $date1 = Carbon::parse($activities[$i]);
            $date2 = Carbon::parse($activities[$i + 1]);
            
            if ($date1->diffInDays($date2) === 1) {
                $tempStreak++;
                $longestStreak = max($longestStreak, $tempStreak);
            } else {
                $tempStreak = 1;
            }
        }
        
        $user->update([
            'current_streak' => $currentStreak,
            'longest_streak' => max($longestStreak, $currentStreak),
            'last_activity_date' => $lastActivityDate,
        ]);
    }
}

