<?php

namespace Database\Seeders;

use App\Enums\ActivityCategory;
use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeWithTranslationsSeeder extends Seeder
{
    /**
     * Seed activity types with proper translation structure.
     */
    public function run(): void
    {
        $activities = [
            // ========================================
            // 💪 WORKOUT ACTIVITIES
            // ========================================
            
            [
                'name' => [
                    'en' => 'Walking',
                    'ar' => 'المشي',
                ],
                'locale' => 'activity.walking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 25,
                'icon' => '🚶',
                'display_order' => 1,
                'description' => [
                    'en' => 'Walking for 30+ minutes',
                    'ar' => 'المشي لمدة 30+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Running',
                    'ar' => 'الركض',
                ],
                'locale' => 'activity.running',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '🏃',
                'display_order' => 2,
                'description' => [
                    'en' => 'Running for 20+ minutes',
                    'ar' => 'الركض لمدة 20+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Strength Training',
                    'ar' => 'تدريب القوة',
                ],
                'locale' => 'activity.strength_training',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🏋️',
                'display_order' => 3,
                'description' => [
                    'en' => 'Weight lifting or bodyweight exercises',
                    'ar' => 'رفع الأثقال أو تمارين وزن الجسم',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Cycling',
                    'ar' => 'ركوب الدراجة',
                ],
                'locale' => 'activity.cycling',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🚴',
                'display_order' => 4,
                'description' => [
                    'en' => 'Cycling for 30+ minutes',
                    'ar' => 'ركوب الدراجة لمدة 30+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Swimming',
                    'ar' => 'السباحة',
                ],
                'locale' => 'activity.swimming',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '🏊',
                'display_order' => 5,
                'description' => [
                    'en' => 'Swimming for 20+ minutes',
                    'ar' => 'السباحة لمدة 20+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Yoga',
                    'ar' => 'اليوجا',
                ],
                'locale' => 'activity.yoga',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🧘',
                'display_order' => 6,
                'description' => [
                    'en' => 'Yoga or stretching session',
                    'ar' => 'جلسة يوجا أو تمارين الإطالة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Dancing',
                    'ar' => 'الرقص',
                ],
                'locale' => 'activity.dancing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 35,
                'icon' => '💃',
                'display_order' => 7,
                'description' => [
                    'en' => 'Dancing for 30+ minutes',
                    'ar' => 'الرقص لمدة 30+ دقيقة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Basketball',
                    'ar' => 'كرة السلة',
                ],
                'locale' => 'activity.basketball',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '🏀',
                'display_order' => 8,
                'description' => [
                    'en' => 'Basketball game or practice',
                    'ar' => 'مباراة أو تدريب كرة السلة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Football',
                    'ar' => 'كرة القدم',
                ],
                'locale' => 'activity.football',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '⚽',
                'display_order' => 9,
                'description' => [
                    'en' => 'Football/soccer game or practice',
                    'ar' => 'مباراة أو تدريب كرة القدم',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Tennis',
                    'ar' => 'التنس',
                ],
                'locale' => 'activity.tennis',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 45,
                'icon' => '🎾',
                'display_order' => 10,
                'description' => [
                    'en' => 'Tennis match or practice',
                    'ar' => 'مباراة أو تدريب التنس',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Hiking',
                    'ar' => 'المشي لمسافات طويلة',
                ],
                'locale' => 'activity.hiking',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 40,
                'icon' => '🥾',
                'display_order' => 11,
                'description' => [
                    'en' => 'Hiking or trail walking',
                    'ar' => 'المشي لمسافات طويلة أو المشي في الطبيعة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Pilates',
                    'ar' => 'البيلاتس',
                ],
                'locale' => 'activity.pilates',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 30,
                'icon' => '🤸',
                'display_order' => 12,
                'description' => [
                    'en' => 'Pilates class or session',
                    'ar' => 'حصة أو جلسة بيلاتس',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Boxing',
                    'ar' => 'الملاكمة',
                ],
                'locale' => 'activity.boxing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 55,
                'icon' => '🥊',
                'display_order' => 13,
                'description' => [
                    'en' => 'Boxing training or sparring',
                    'ar' => 'تدريب الملاكمة أو المباراة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Martial Arts',
                    'ar' => 'الرياضات القتالية',
                ],
                'locale' => 'activity.martial_arts',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 50,
                'icon' => '🥋',
                'display_order' => 14,
                'description' => [
                    'en' => 'Karate, Taekwondo, or other martial arts',
                    'ar' => 'الكاراتيه، التايكوندو، أو الرياضات القتالية الأخرى',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Rock Climbing',
                    'ar' => 'تسلق الصخور',
                ],
                'locale' => 'activity.rock_climbing',
                'category' => ActivityCategory::WORKOUT->value,
                'base_points' => 60,
                'icon' => '🧗',
                'display_order' => 15,
                'description' => [
                    'en' => 'Indoor or outdoor rock climbing',
                    'ar' => 'تسلق الصخور في الداخل أو الخارج',
                ],
                'is_active' => true,
            ],

            // ========================================
            // 🥗 NUTRITION ACTIVITIES
            // ========================================
            
            [
                'name' => [
                    'en' => 'Healthy Breakfast',
                    'ar' => 'فطور صحي',
                ],
                'locale' => 'activity.healthy_breakfast',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🥣',
                'display_order' => 1,
                'description' => [
                    'en' => 'Nutritious breakfast with protein and fiber',
                    'ar' => 'فطور مغذي يحتوي على البروتين والألياف',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Vegetable Intake',
                    'ar' => 'تناول الخضروات',
                ],
                'locale' => 'activity.vegetables',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🥬',
                'display_order' => 2,
                'description' => [
                    'en' => '5+ servings of vegetables',
                    'ar' => '5+ حصص من الخضروات',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Fruit Intake',
                    'ar' => 'تناول الفواكه',
                ],
                'locale' => 'activity.fruits',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🍎',
                'display_order' => 3,
                'description' => [
                    'en' => '3+ servings of fresh fruits',
                    'ar' => '3+ حصص من الفواكه الطازجة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Water Intake',
                    'ar' => 'شرب الماء',
                ],
                'locale' => 'activity.water',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 10,
                'icon' => '💧',
                'display_order' => 4,
                'description' => [
                    'en' => '8+ glasses of water',
                    'ar' => '8+ أكواب من الماء',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Lean Protein',
                    'ar' => 'البروتين الخالي من الدهون',
                ],
                'locale' => 'activity.lean_protein',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🍗',
                'display_order' => 5,
                'description' => [
                    'en' => 'Lean protein source (chicken, fish, beans)',
                    'ar' => 'مصدر بروتين خالي من الدهون (دجاج، سمك، فاصوليا)',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Whole Grains',
                    'ar' => 'الحبوب الكاملة',
                ],
                'locale' => 'activity.whole_grains',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 15,
                'icon' => '🌾',
                'display_order' => 6,
                'description' => [
                    'en' => 'Whole grain foods (brown rice, quinoa, oats)',
                    'ar' => 'أطعمة الحبوب الكاملة (الأرز البني، الكينوا، الشوفان)',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Healthy Snacks',
                    'ar' => 'وجبات خفيفة صحية',
                ],
                'locale' => 'activity.healthy_snacks',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 10,
                'icon' => '🥜',
                'display_order' => 7,
                'description' => [
                    'en' => 'Nuts, seeds, or healthy snacks',
                    'ar' => 'المكسرات، البذور، أو وجبات خفيفة صحية',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Meal Prep',
                    'ar' => 'تحضير الوجبات',
                ],
                'locale' => 'activity.meal_prep',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🍱',
                'display_order' => 8,
                'description' => [
                    'en' => 'Preparing healthy meals in advance',
                    'ar' => 'تحضير وجبات صحية مسبقاً',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'No Processed Foods',
                    'ar' => 'عدم تناول الأطعمة المصنعة',
                ],
                'locale' => 'activity.no_processed',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 20,
                'icon' => '🚫',
                'display_order' => 9,
                'description' => [
                    'en' => 'Avoiding processed and junk foods',
                    'ar' => 'تجنب الأطعمة المصنعة والوجبات السريعة',
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Balanced Meal',
                    'ar' => 'وجبة متوازنة',
                ],
                'locale' => 'activity.balanced_meal',
                'category' => ActivityCategory::NUTRITION->value,
                'base_points' => 25,
                'icon' => '🍽️',
                'display_order' => 10,
                'description' => [
                    'en' => 'Complete balanced meal with all food groups',
                    'ar' => 'وجبة متوازنة كاملة تحتوي على جميع المجموعات الغذائية',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($activities as $activity) {
            ActivityType::create($activity);
        }
    }
}