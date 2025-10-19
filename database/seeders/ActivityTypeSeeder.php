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
     * Base Points Guide (Based on common activities and health impact):
     * - Light activities (10-20 pts): Basic daily habits
     * - Moderate activities (25-40 pts): Regular exercise and healthy eating
     * - Intense activities (45-60 pts): Vigorous workouts
     * 
     * Daily Target: ~60-80 points suggested
     */
    public function run(): void
    {
        $activities = [
            // ========================================
            // 💪 WORKOUT ACTIVITIES (15 total)
            // Ordered by most common/accessible
            // ========================================
            
            [
                'name' => 'Walking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 25,
                'icon' => '🚶',
                'display_order' => 1,
                'description' => 'Walking for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Running',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '🏃',
                'display_order' => 2,
                'description' => 'Running for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Strength Training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🏋️',
                'display_order' => 3,
                'description' => 'Weight training or bodyweight exercises for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Gym Workout',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🏃‍♀️',
                'display_order' => 4,
                'description' => 'General gym workout for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Cycling',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🚴',
                'display_order' => 5,
                'description' => 'Cycling for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Swimming',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🏊',
                'display_order' => 6,
                'description' => 'Swimming for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Stretching',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 15,
                'icon' => '🤸‍♀️',
                'display_order' => 7,
                'description' => 'Stretching session for 15+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Home Workout',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🏠',
                'display_order' => 8,
                'description' => 'Home workout for 30+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Sports',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '⚽',
                'display_order' => 9,
                'description' => 'Playing sports (soccer, basketball, tennis) for 45+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Hiking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🥾',
                'display_order' => 10,
                'description' => 'Hiking for 60+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'HIIT Training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '⚡',
                'display_order' => 11,
                'description' => 'High-intensity interval training for 20+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Stairs Climbing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 20,
                'icon' => '🪜',
                'display_order' => 12,
                'description' => 'Taking stairs instead of elevator throughout the day',
                'is_active' => true,
            ],
            [
                'name' => 'Active Break',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 10,
                'icon' => '⏰',
                'display_order' => 13,
                'description' => 'Taking active breaks during work (5-10 minute walks)',
                'is_active' => true,
            ],
            [
                'name' => 'Jump Rope',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🪢',
                'display_order' => 14,
                'description' => 'Jumping rope for 15+ minutes',
                'is_active' => true,
            ],
            [
                'name' => 'Prayer/Meditation',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🤲',
                'display_order' => 15,
                'description' => 'Prayer, spiritual reflection, or meditation for 30+ minutes',
                'is_active' => true,
            ],
            
            // ========================================
            // 🥗 NUTRITION ACTIVITIES (15 total)
            // Ordered by most important/common
            // ========================================
            
            [
                'name' => 'Drink Water',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '💧',
                'display_order' => 16,
                'description' => 'Drinking 8+ glasses of water daily',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Vegetables',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 35,
                'icon' => '🥬',
                'display_order' => 17,
                'description' => 'Eating 3+ servings of vegetables',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Fruits',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 30,
                'icon' => '🍎',
                'display_order' => 18,
                'description' => 'Eating 2+ servings of fruits',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Breakfast',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🍳',
                'display_order' => 19,
                'description' => 'Eating a healthy breakfast',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Lunch',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🥗',
                'display_order' => 20,
                'description' => 'Eating a healthy lunch',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Dinner',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🍽️',
                'display_order' => 21,
                'description' => 'Eating a healthy dinner',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Protein',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 30,
                'icon' => '🍗',
                'display_order' => 22,
                'description' => 'Including lean protein in meals',
                'is_active' => true,
            ],
            [
                'name' => 'Cook at Home',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 30,
                'icon' => '👨‍🍳',
                'display_order' => 23,
                'description' => 'Preparing meals at home',
                'is_active' => true,
            ],
            [
                'name' => 'Avoid Junk Food',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🚫',
                'display_order' => 24,
                'description' => 'Avoiding processed and junk foods',
                'is_active' => true,
            ],
            [
                'name' => 'Eat Whole Grains',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🌾',
                'display_order' => 25,
                'description' => 'Choosing whole grain foods',
                'is_active' => true,
            ],
            [
                'name' => 'Healthy Snacks',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🥜',
                'display_order' => 26,
                'description' => 'Eating healthy snacks (nuts, fruits, yogurt)',
                'is_active' => true,
            ],
            [
                'name' => 'Take Vitamins',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '💊',
                'display_order' => 27,
                'description' => 'Taking daily vitamins or supplements',
                'is_active' => true,
            ],
            [
                'name' => 'Limit Sugar',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🍭',
                'display_order' => 28,
                'description' => 'Limiting added sugar and sugary drinks',
                'is_active' => true,
            ],
            [
                'name' => 'Portion Control',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🍽️',
                'display_order' => 29,
                'description' => 'Eating appropriate portion sizes',
                'is_active' => true,
            ],
            [
                'name' => 'Meal Planning',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '📋',
                'display_order' => 30,
                'description' => 'Planning healthy meals in advance',
                'is_active' => true,
            ],
        ];

        foreach ($activities as $activity) {
            ActivityType::firstOrCreate(
                ['name' => $activity['name']],
                $activity
            );
        }

        $this->command->info('✓ Successfully seeded 30 activity types');
    }
}