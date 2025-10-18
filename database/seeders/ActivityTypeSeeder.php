<?php

namespace Database\Seeders;

use App\Enums\ActivityCategory;
use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Seed activity types for IOMeH workout and nutrition tracking platform.
     * 
     * Base Points Guide (Based on MET values and user needs research):
     * - Low Intensity (10-20 pts): Light activities, easy habits
     * - Moderate Intensity (25-40 pts): Regular daily activities  
     * - High Intensity (45-60 pts): Vigorous activities, major commitments
     * - Very High Intensity (65-80 pts): Extreme effort, exceptional activities
     * 
     * Daily Target: ~80-100 points suggested
     */
    public function run(): void
    {
        $activities = [
            // ========================================
            // 💪 WORKOUT ACTIVITIES (25 total)
            // ========================================
            
            // VERY HIGH INTENSITY (65-80 points) - Based on MET 10-12+
            [
                'name' => 'Sprint Running',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 80,
                'icon' => '🏃‍♂️',
                'display_order' => 1,
                'description' => 'Sprint running at maximum effort for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'HIIT Training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 75,
                'icon' => '⚡',
                'display_order' => 2,
                'description' => 'High-intensity interval training for 25+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'CrossFit Workout',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 70,
                'icon' => '🏋️‍♂️',
                'display_order' => 3,
                'description' => 'CrossFit or functional fitness workout for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Karate Training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 65,
                'icon' => '🥋',
                'display_order' => 4,
                'description' => 'Karate or Martial arts training for 30+ minutes',
                'is_active' => true,
            ],

            // HIGH INTENSITY (45-60 points) - Based on MET 7-9
            [
                'name' => 'Running',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 60,
                'icon' => '🏃',
                'display_order' => 5,
                'description' => 'Running at moderate pace for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Cycling',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '🚴',
                'display_order' => 6,
                'description' => 'Cycling for 45+ minutes at moderate intensity',
                'is_active' => true,
            ],
            [
                'name' => 'Swimming',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '🏊',
                'display_order' => 7,
                'description' => 'Swimming laps for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Weight Training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '🏋️',
                'display_order' => 8,
                'description' => 'Strength training with weights for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Basketball',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '🏀',
                'display_order' => 9,
                'description' => 'Playing basketball for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Soccer',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '⚽',
                'display_order' => 10,
                'description' => 'Playing soccer for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Tennis',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🎾',
                'display_order' => 11,
                'description' => 'Playing tennis for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Rock Climbing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🧗',
                'display_order' => 12,
                'description' => 'Rock climbing or bouldering for 60+ minutes',
                'is_active' => true,
            ],
            
            // MODERATE INTENSITY (25-40 points) - Based on MET 4-6
            [
                'name' => 'Breathing Exercises',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 25,
                'icon' => '🫁',
                'display_order' => 13,
                'description' => 'Deep breathing or meditation breathing for 15+ minutes. Reduces stress, improves focus, and enhances lung capacity.',
                'is_active' => true,
            ],
            [
                'name' => 'Pilates',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🤸',
                'display_order' => 14,
                'description' => 'Pilates class or session for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Bodyweight Workout',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '💪',
                'display_order' => 15,
                'description' => 'Bodyweight exercises for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Hiking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🥾',
                'display_order' => 16,
                'description' => 'Hiking for 60+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Walking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🚶',
                'display_order' => 17,
                'description' => 'Brisk walking for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Jump Rope',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🪢',
                'display_order' => 18,
                'description' => 'Jump rope for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Dancing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '💃',
                'display_order' => 19,
                'description' => 'Dancing for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Elliptical',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🏃‍♀️',
                'display_order' => 20,
                'description' => 'Elliptical machine for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Treadmill',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🏃‍♂️',
                'display_order' => 21,
                'description' => 'Treadmill workout for 30+ minutes',
                'is_active' => true,
            ],
            
            // LOW INTENSITY (10-20 points) - Based on MET 2-3
            [
                'name' => 'Stretching',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 20,
                'icon' => '🤸‍♀️',
                'display_order' => 22,
                'description' => 'Stretching session for 15+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Light Walking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 15,
                'icon' => '🚶‍♀️',
                'display_order' => 23,
                'description' => 'Light walking for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Tai Chi',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 15,
                'icon' => '☯️',
                'display_order' => 24,
                'description' => 'Tai Chi practice for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Standing Desk',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 10,
                'icon' => '🖥️',
                'display_order' => 25,
                'description' => 'Using standing desk for 4+ hours',
                'is_active' => true,
            ],
            
            // ========================================
            // 🥗 NUTRITION ACTIVITIES (20 total)
            // ========================================
            
            // HIGH NUTRITION VALUE (40-50 points) - Most important for health
            [
                'name' => 'Eat 5+ Vegetables',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 50,
                'icon' => '🥬',
                'display_order' => 26,
                'description' => 'Consuming 5 or more different vegetables',
                'is_active' => true,
            ],
            [
                'name' => 'Eat 3+ Fruits',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 45,
                'icon' => '🍎',
                'display_order' => 27,
                'description' => 'Consuming 3 or more different fruits',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Lean Protein',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 45,
                'icon' => '🍗',
                'display_order' => 28,
                'description' => 'Consuming lean protein sources (chicken, fish, beans)',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Whole Grains',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 40,
                'icon' => '🌾',
                'display_order' => 29,
                'description' => 'Consuming whole grain foods',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Healthy Fats',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 40,
                'icon' => '🥑',
                'display_order' => 30,
                'description' => 'Consuming healthy fats (avocado, nuts, olive oil)',
                'is_active' => true,
            ],
            
            // MODERATE NUTRITION VALUE (25-35 points) - Daily essentials
            [
                'name' => 'Drink 8+ Glasses Water',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 35,
                'icon' => '💧',
                'display_order' => 31,
                'description' => 'Drinking 8 or more glasses of water',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Breakfast',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 30,
                'icon' => '🍳',
                'display_order' => 32,
                'description' => 'Eating a healthy breakfast',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Lunch',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 30,
                'icon' => '🥗',
                'display_order' => 33,
                'description' => 'Eating a healthy lunch',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Dinner',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 30,
                'icon' => '🍽️',
                'display_order' => 34,
                'description' => 'Eating a healthy dinner',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Nuts',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🥜',
                'display_order' => 35,
                'description' => 'Consuming a handful of nuts',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Seeds',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🌰',
                'display_order' => 36,
                'description' => 'Consuming seeds (chia, flax, pumpkin)',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Legumes',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🫘',
                'display_order' => 37,
                'description' => 'Consuming legumes (beans, lentils, chickpeas)',
                'is_active' => true,
            ],
            [
                'name' => 'Meal Prep',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 30,
                'icon' => '👨‍🍳',
                'display_order' => 38,
                'description' => 'Preparing healthy meals in advance',
                'is_active' => true,
            ],
            [
                'name' => 'Cook at Home',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🏠',
                'display_order' => 39,
                'description' => 'Preparing meals at home instead of takeout',
                'is_active' => true,
            ],
            
            // BASIC NUTRITION (10-20 points) - Foundation habits
            [
                'name' => 'Take Vitamins',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '💊',
                'display_order' => 40,
                'description' => 'Taking daily vitamins or supplements',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Probiotics',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🦠',
                'display_order' => 41,
                'description' => 'Consuming probiotic foods (yogurt, kefir)',
                'is_active' => true,
            ],
            [
                'name' => 'Avoid Processed Foods',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🚫',
                'display_order' => 42,
                'description' => 'Avoiding processed and junk foods',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Mindfully',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🧘',
                'display_order' => 43,
                'description' => 'Eating slowly and mindfully',
                'is_active' => true,
            ],
            [
                'name' => 'Track Calories',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 10,
                'icon' => '📊',
                'display_order' => 44,
                'description' => 'Tracking daily calorie intake',
                'is_active' => true,
            ],
        ];

        foreach ($activities as $activity) {
            ActivityType::firstOrCreate(
                ['name' => $activity['name']],
                $activity
            );
        }

        $this->command->info('✓ Successfully seeded 45 activity types');
        $this->command->info('');
        $this->command->info('  📊 Category Breakdown:');
        $this->command->info('     💪 Workout: 25 activities (10-80 points)');
        $this->command->info('     🥗 Nutrition: 20 activities (10-50 points)');
        $this->command->info('');
        $this->command->info('  📈 Suggested Daily Target: 80-100 points');
        $this->command->info('     • Workout: 40-60 pts (1-2 activities)');
        $this->command->info('     • Nutrition: 40-50 pts (meals + hydration)');
        $this->command->info('');
        $this->command->info('  🔬 Based on MET values and user needs research');
        $this->command->info('     • Points reflect energy expenditure and health impact');
        $this->command->info('     • Optimized for most essential fitness activities');
    }
}