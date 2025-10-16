#!/usr/bin/env php
<?php

/**
 * Points & Streak System Demo
 * 
 * This script demonstrates how the points and streak system works
 * with real examples showing calculations and milestone bonuses.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;

echo "🎮 Points & Streak System Demo\n";
echo "================================\n\n";

// Create test user
$timestamp = time();
$user = User::factory()->create([
    'username' => 'streak_demo_' . $timestamp,
    'email' => 'demo_' . $timestamp . '@test.com',
    'current_streak' => 0,
    'longest_streak' => 0,
    'last_activity_date' => null,
]);

echo "👤 Created test user: {$user->username}\n";
echo "   Current Streak: {$user->current_streak}\n";
echo "   Longest Streak: {$user->longest_streak}\n\n";

// Create activity type and habit
$activityType = ActivityType::factory()->create([
    'name' => 'Demo Exercise ' . $timestamp,
    'base_points' => 10,
]);

$habit = $user->habits()->create([
    'activity_type_id' => $activityType->id,
    'custom_name' => 'Morning Workout',
]);

echo "🎯 Created habit: {$habit->custom_name}\n";
echo "   Base Points: {$activityType->base_points}\n\n";

// Simulate 10 days of consecutive activities
echo "📊 Simulating 10 consecutive days:\n";
echo "-----------------------------------\n";

DB::transaction(function () use ($user, $habit, $activityType) {
    for ($day = 0; $day < 10; $day++) {
        $date = now()->subDays(9 - $day);
        
        // Manually update streak before creating activity (to show current state)
        if ($day > 0) {
            $user->refresh();
        }
        
        $beforeStreak = $user->current_streak;
        $multiplier = $user->calculatePointsWithStreakBonus(10) / 10;
        
        // Create activity (this will automatically update streak)
        Activity::withoutEvents(function () use ($user, $habit, $date, $activityType, $beforeStreak, $multiplier) {
            // Manually update streak
            if ($beforeStreak == 0) {
                $user->current_streak = 1;
                $user->longest_streak = 1;
            } else {
                $user->current_streak = $beforeStreak + 1;
                $user->longest_streak = max($user->longest_streak, $user->current_streak);
            }
            $user->last_activity_date = $date;
            $user->save();
            
            // Calculate points with new streak
            $user->refresh();
            $newMultiplier = $user->calculatePointsWithStreakBonus(10) / 10;
            $points = 10 * $multiplier;
            
            // Check for milestone
            $milestones = config('gamification.milestone_bonuses', []);
            $milestoneBonus = $milestones[$user->current_streak] ?? 0;
            $totalPoints = $points + $milestoneBonus;
            
            // Create activity
            $activity = Activity::create([
                'user_id' => $user->id,
                'habit_id' => $habit->id,
                'date' => $date,
                'points_earned' => $totalPoints,
            ]);
            
            echo sprintf(
                "Day %2d: Streak %2d → %2d | Multiplier: %.1fx | Points: %2d%s = %d pts\n",
                $user->current_streak,
                $beforeStreak,
                $user->current_streak,
                $multiplier,
                (int)$points,
                $milestoneBonus > 0 ? " + {$milestoneBonus} (🎉 Milestone!)" : "",
                (int)$totalPoints
            );
        });
    }
});

$user->refresh();

echo "\n📈 Final Stats:\n";
echo "-----------------------------------\n";
echo "Current Streak: {$user->current_streak} days 🔥\n";
echo "Longest Streak: {$user->longest_streak} days ⭐\n";
echo "Total Activities: " . $user->activities()->count() . "\n";
echo "Total Points: " . $user->activities()->sum('points_earned') . " pts\n";

$streakTier = $user->getStreakTier();
echo "Current Tier: {$streakTier['name']} {$streakTier['icon']} ({$streakTier['multiplier']}x)\n\n";

// Show what happens with a gap
echo "⏸️  What happens with a 3-day gap?\n";
echo "-----------------------------------\n";
echo "Scenario: User had 10-day streak, then missed 3 days\n";
echo "   Last activity: 4 days ago\n";
echo "   Current streak: 10\n\n";
echo "🔄 When logging next activity:\n";
echo "   ⚠️  Streak resets to 1 (gap detected)\n";
echo "   Points earned: 10 (1.0x multiplier)\n";
echo "   User must rebuild streak from day 1\n\n";

// Cleanup
echo "🧹 Cleaning up test data...\n";
$user->activities()->delete();
$user->habits()->delete();
$user->rankingHistory()->delete();
$user->delete();

echo "✅ Demo completed!\n\n";

echo "Key Takeaways:\n";
echo "• Streak increments each consecutive day\n";
echo "• Multiplier increases at: 3, 7, 14, 30, 60, 100, 200 days\n";
echo "• Milestone bonuses at: 7 (50pts), 30 (200pts), 60 (500pts), 100 (1000pts), 365 (5000pts)\n";
echo "• Gaps reset streak to 1 (not 0)\n";
echo "• Points use CURRENT multiplier, bonuses use PREDICTED streak\n";

