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
     * Base Points Guide:
     * - Low Intensity (10-20 pts): Light activities, easy habits
     * - Moderate Intensity (25-35 pts): Regular daily activities
     * - High Intensity (40-50 pts): Vigorous activities, major commitments
     * - Very High Intensity (55-60 pts): Extreme effort, exceptional activities
     * 
     * Daily Target: ~60-80 points suggested
     */
    public function run(): void
    {
        $activities = [
            // ========================================
            // 💪 WORKOUT ACTIVITIES (30 total)
            // ========================================
            
            // VERY HIGH INTENSITY (55-60 points)
            [
                'name' => 'Fast Running',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 60,
                'icon' => '🏃‍♂️',
                'display_order' => 1,
                'description' => 'Running at 12+ km/h (7.5+ mph) for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Jump Rope Fast',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '🪢',
                'display_order' => 2,
                'description' => 'Fast continuous jump rope for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'HIIT Training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '⚡',
                'display_order' => 3,
                'description' => 'High-intensity interval training for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'CrossFit Workout',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '🏋️‍♂️',
                'display_order' => 4,
                'description' => 'CrossFit or functional fitness workout',
                'is_active' => true,
            ],
            [
                'name' => 'Boxing Training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '🥊',
                'display_order' => 5,
                'description' => 'Boxing or martial arts training for 30+ minutes',
                'is_active' => true,
            ],
            
            // HIGH INTENSITY (40-50 points)
            [
                'name' => 'Weight Training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '🏋️',
                'display_order' => 6,
                'description' => 'Strength training with weights for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Running',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🏃',
                'display_order' => 7,
                'description' => 'Running at moderate pace for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Cycling',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🚴',
                'display_order' => 8,
                'description' => 'Cycling for 45+ minutes at moderate intensity',
                'is_active' => true,
            ],
            [
                'name' => 'Swimming',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🏊',
                'display_order' => 9,
                'description' => 'Swimming laps for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Basketball',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🏀',
                'display_order' => 10,
                'description' => 'Playing basketball for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Soccer',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '⚽',
                'display_order' => 11,
                'description' => 'Playing soccer for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Tennis',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🎾',
                'display_order' => 12,
                'description' => 'Playing tennis for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Rock Climbing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🧗',
                'display_order' => 13,
                'description' => 'Rock climbing or bouldering for 60+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Rowing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🚣',
                'display_order' => 14,
                'description' => 'Rowing machine or water rowing for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Dancing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '💃',
                'display_order' => 15,
                'description' => 'Dancing for 45+ minutes',
                'is_active' => true,
            ],
            
            // MODERATE INTENSITY (25-35 points)
            [
                'name' => 'Yoga',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🧘‍♀️',
                'display_order' => 16,
                'description' => 'Yoga practice for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Pilates',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🤸',
                'display_order' => 17,
                'description' => 'Pilates class or session for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Bodyweight Workout',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '💪',
                'display_order' => 18,
                'description' => 'Bodyweight exercises for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Hiking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🥾',
                'display_order' => 19,
                'description' => 'Hiking for 60+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Walking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🚶',
                'display_order' => 20,
                'description' => 'Brisk walking for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Jump Rope',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🪢',
                'display_order' => 21,
                'description' => 'Jump rope for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Stair Climbing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🪜',
                'display_order' => 22,
                'description' => 'Stair climbing for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Elliptical',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🏃‍♀️',
                'display_order' => 23,
                'description' => 'Elliptical machine for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Treadmill',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🏃‍♂️',
                'display_order' => 24,
                'description' => 'Treadmill workout for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Golf',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 25,
                'icon' => '⛳',
                'display_order' => 25,
                'description' => 'Playing golf (walking) for 2+ hours',
                'is_active' => true,
            ],
            
            // LOW INTENSITY (10-20 points)
            [
                'name' => 'Stretching',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 20,
                'icon' => '🤸‍♀️',
                'display_order' => 26,
                'description' => 'Stretching session for 15+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Light Walking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 15,
                'icon' => '🚶‍♀️',
                'display_order' => 27,
                'description' => 'Light walking for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Tai Chi',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 15,
                'icon' => '☯️',
                'display_order' => 28,
                'description' => 'Tai Chi practice for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Qigong',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 15,
                'icon' => '🧘',
                'display_order' => 29,
                'description' => 'Qigong practice for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Standing Desk',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 10,
                'icon' => '🖥️',
                'display_order' => 30,
                'description' => 'Using standing desk for 4+ hours',
                'is_active' => true,
            ],
            
            // ========================================
            // 🥗 NUTRITION ACTIVITIES (15 total)
            // ========================================
            
            // HIGH NUTRITION VALUE (25-30 points)
            [
                'name' => 'Eat 5+ Vegetables',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 30,
                'icon' => '🥬',
                'display_order' => 31,
                'description' => 'Consuming 5 or more different vegetables',
                'is_active' => true,
            ],
            [
                'name' => 'Eat 3+ Fruits',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🍎',
                'display_order' => 32,
                'description' => 'Consuming 3 or more different fruits',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Lean Protein',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🍗',
                'display_order' => 33,
                'description' => 'Consuming lean protein sources (chicken, fish, beans)',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Whole Grains',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🌾',
                'display_order' => 34,
                'description' => 'Consuming whole grain foods',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Healthy Fats',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🥑',
                'display_order' => 35,
                'description' => 'Consuming healthy fats (avocado, nuts, olive oil)',
                'is_active' => true,
            ],
            
            // MODERATE NUTRITION VALUE (15-20 points)
            [
                'name' => 'Drink 8+ Glasses Water',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '💧',
                'display_order' => 36,
                'description' => 'Drinking 8 or more glasses of water',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Breakfast',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🍳',
                'display_order' => 37,
                'description' => 'Eating a healthy breakfast',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Lunch',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🥗',
                'display_order' => 38,
                'description' => 'Eating a healthy lunch',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Dinner',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🍽️',
                'display_order' => 39,
                'description' => 'Eating a healthy dinner',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Nuts',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🥜',
                'display_order' => 40,
                'description' => 'Consuming a handful of nuts',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Seeds',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🌰',
                'display_order' => 41,
                'description' => 'Consuming seeds (chia, flax, pumpkin)',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Legumes',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🫘',
                'display_order' => 42,
                'description' => 'Consuming legumes (beans, lentils, chickpeas)',
                'is_active' => true,
            ],
            
            // BASIC NUTRITION (10 points)
            [
                'name' => 'Take Vitamins',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 10,
                'icon' => '💊',
                'display_order' => 43,
                'description' => 'Taking daily vitamins or supplements',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Probiotics',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 10,
                'icon' => '🦠',
                'display_order' => 44,
                'description' => 'Consuming probiotic foods (yogurt, kefir)',
                'is_active' => true,
            ],
            [
                'name' => 'Cook at Home',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 10,
                'icon' => '👨‍🍳',
                'display_order' => 45,
                'description' => 'Preparing meals at home instead of takeout',
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
        $this->command->info('     💪 Workout: 30 activities (10-60 points)');
        $this->command->info('     🥗 Nutrition: 15 activities (10-30 points)');
        $this->command->info('');
        $this->command->info('  📈 Suggested Daily Target: 60-80 points');
        $this->command->info('     • Workout: 30-50 pts (1-2 activities)');
        $this->command->info('     • Nutrition: 30-40 pts (meals + hydration)');
    }
}