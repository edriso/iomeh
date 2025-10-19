<?php

namespace Database\Seeders;

use App\Enums\ActivityCategory;
use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeWithTranslationsSeeder extends Seeder
{
    /**
     * Seed activity types with translations for IOMeH workout and nutrition tracking platform.
     */
    public function run(): void
    {
        $activities = [
            // ========================================
            // 💪 WORKOUT ACTIVITIES
            // ========================================
            
            [
                'name' => 'Walking',
                'name_translations' => [
                    'en' => 'Walking',
                    'ar' => 'المشي',
                ],
                'translation_key' => 'activity.walking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 15,
                'icon' => '🚶‍♂️',
                'display_order' => 1,
                'description' => 'Walking for 30+ minutes',
                'description_translations' => [
                    'en' => 'Walking for 30+ minutes',
                    'ar' => 'المشي لمدة 30+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Running',
                'name_translations' => [
                    'en' => 'Running',
                    'ar' => 'الجري',
                ],
                'translation_key' => 'activity.running',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🏃‍♂️',
                'display_order' => 2,
                'description' => 'Running for 20+ minutes',
                'description_translations' => [
                    'en' => 'Running for 20+ minutes',
                    'ar' => 'الجري لمدة 20+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Gym Workout',
                'name_translations' => [
                    'en' => 'Gym Workout',
                    'ar' => 'تمرين الجيم',
                ],
                'translation_key' => 'activity.gym_workout',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '🏋️‍♂️',
                'display_order' => 3,
                'description' => 'Gym workout for 45+ minutes',
                'description_translations' => [
                    'en' => 'Gym workout for 45+ minutes',
                    'ar' => 'تمرين الجيم لمدة 45+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Cycling',
                'name_translations' => [
                    'en' => 'Cycling',
                    'ar' => 'ركوب الدراجة',
                ],
                'translation_key' => 'activity.cycling',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🚴‍♂️',
                'display_order' => 4,
                'description' => 'Cycling for 30+ minutes',
                'description_translations' => [
                    'en' => 'Cycling for 30+ minutes',
                    'ar' => 'ركوب الدراجة لمدة 30+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Swimming',
                'name_translations' => [
                    'en' => 'Swimming',
                    'ar' => 'السباحة',
                ],
                'translation_key' => 'activity.swimming',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🏊‍♂️',
                'display_order' => 5,
                'description' => 'Swimming for 30+ minutes',
                'description_translations' => [
                    'en' => 'Swimming for 30+ minutes',
                    'ar' => 'السباحة لمدة 30+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Stretching',
                'name_translations' => [
                    'en' => 'Stretching',
                    'ar' => 'التمدد',
                ],
                'translation_key' => 'activity.stretching',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 15,
                'icon' => '🤸‍♀️',
                'display_order' => 6,
                'description' => 'Stretching session for 15+ minutes',
                'description_translations' => [
                    'en' => 'Stretching session for 15+ minutes',
                    'ar' => 'جلسة تمدد لمدة 15+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Home Workout',
                'name_translations' => [
                    'en' => 'Home Workout',
                    'ar' => 'تمرين منزلي',
                ],
                'translation_key' => 'activity.home_workout',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '🏠',
                'display_order' => 7,
                'description' => 'Home workout for 30+ minutes',
                'description_translations' => [
                    'en' => 'Home workout for 30+ minutes',
                    'ar' => 'تمرين منزلي لمدة 30+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'HIIT',
                'name_translations' => [
                    'en' => 'HIIT',
                    'ar' => 'تمارين HIIT',
                ],
                'translation_key' => 'activity.hiit',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '⚡',
                'display_order' => 8,
                'description' => 'High Intensity Interval Training',
                'description_translations' => [
                    'en' => 'High Intensity Interval Training',
                    'ar' => 'تمارين عالية الكثافة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Yoga',
                'name_translations' => [
                    'en' => 'Yoga',
                    'ar' => 'اليوجا',
                ],
                'translation_key' => 'activity.yoga',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 25,
                'icon' => '🧘‍♀️',
                'display_order' => 9,
                'description' => 'Yoga session for 30+ minutes',
                'description_translations' => [
                    'en' => 'Yoga session for 30+ minutes',
                    'ar' => 'جلسة يوجا لمدة 30+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Dancing',
                'name_translations' => [
                    'en' => 'Dancing',
                    'ar' => 'الرقص',
                ],
                'translation_key' => 'activity.dancing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '💃',
                'display_order' => 10,
                'description' => 'Dancing for 20+ minutes',
                'description_translations' => [
                    'en' => 'Dancing for 20+ minutes',
                    'ar' => 'الرقص لمدة 20+ دقيقة',
                ],
                'is_active' => true,
            ],

            // ========================================
            // 🥗 NUTRITION ACTIVITIES
            // ========================================
            
            [
                'name' => 'Healthy Breakfast',
                'name_translations' => [
                    'en' => 'Healthy Breakfast',
                    'ar' => 'فطور صحي',
                ],
                'translation_key' => 'activity.healthy_breakfast',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🥣',
                'display_order' => 1,
                'description' => 'Nutritious breakfast with protein and fiber',
                'description_translations' => [
                    'en' => 'Nutritious breakfast with protein and fiber',
                    'ar' => 'فطور مغذي يحتوي على البروتين والألياف',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Vegetables',
                'name_translations' => [
                    'en' => 'Vegetables',
                    'ar' => 'الخضروات',
                ],
                'translation_key' => 'activity.vegetables',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🥬',
                'display_order' => 2,
                'description' => 'Eating 3+ servings of vegetables',
                'description_translations' => [
                    'en' => 'Eating 3+ servings of vegetables',
                    'ar' => 'تناول 3+ حصص من الخضروات',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Fruits',
                'name_translations' => [
                    'en' => 'Fruits',
                    'ar' => 'الفواكه',
                ],
                'translation_key' => 'activity.fruits',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🍎',
                'display_order' => 3,
                'description' => 'Eating 2+ servings of fruits',
                'description_translations' => [
                    'en' => 'Eating 2+ servings of fruits',
                    'ar' => 'تناول 2+ حصص من الفواكه',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Water Intake',
                'name_translations' => [
                    'en' => 'Water Intake',
                    'ar' => 'شرب الماء',
                ],
                'translation_key' => 'activity.water_intake',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 10,
                'icon' => '💧',
                'display_order' => 4,
                'description' => 'Drinking 8+ glasses of water',
                'description_translations' => [
                    'en' => 'Drinking 8+ glasses of water',
                    'ar' => 'شرب 8+ أكواب من الماء',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Protein Goal',
                'name_translations' => [
                    'en' => 'Protein Goal',
                    'ar' => 'هدف البروتين',
                ],
                'translation_key' => 'activity.protein_goal',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🥩',
                'display_order' => 5,
                'description' => 'Meeting daily protein requirements',
                'description_translations' => [
                    'en' => 'Meeting daily protein requirements',
                    'ar' => 'تحقيق متطلبات البروتين اليومية',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Cooking at Home',
                'name_translations' => [
                    'en' => 'Cooking at Home',
                    'ar' => 'الطبخ في المنزل',
                ],
                'translation_key' => 'activity.cooking_home',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '👨‍🍳',
                'display_order' => 6,
                'description' => 'Preparing healthy meals at home',
                'description_translations' => [
                    'en' => 'Preparing healthy meals at home',
                    'ar' => 'تحضير وجبات صحية في المنزل',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($activities as $activity) {
            ActivityType::create($activity);
        }
    }
}
