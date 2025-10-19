<?php

namespace App\Console\Commands;

use App\Models\ActivityType;
use Illuminate\Console\Command;

class UpdateActivityTypeTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-types:update-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing activity types with translations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating activity types with translations...');

        // Translation mappings for existing activity types
        $translations = [
            'Walking' => [
                'name_translations' => ['en' => 'Walking', 'ar' => 'المشي'],
                'description_translations' => ['en' => 'Walking for 30+ minutes', 'ar' => 'المشي لمدة 30+ دقيقة'],
                'translation_key' => 'activity.walking'
            ],
            'Running' => [
                'name_translations' => ['en' => 'Running', 'ar' => 'الجري'],
                'description_translations' => ['en' => 'Running for 20+ minutes', 'ar' => 'الجري لمدة 20+ دقيقة'],
                'translation_key' => 'activity.running'
            ],
            'Strength Training' => [
                'name_translations' => ['en' => 'Strength Training', 'ar' => 'تدريب القوة'],
                'description_translations' => ['en' => 'Strength training for 45+ minutes', 'ar' => 'تدريب القوة لمدة 45+ دقيقة'],
                'translation_key' => 'activity.strength_training'
            ],
            'Gym Workout' => [
                'name_translations' => ['en' => 'Gym Workout', 'ar' => 'تمرين الجيم'],
                'description_translations' => ['en' => 'Gym workout for 45+ minutes', 'ar' => 'تمرين الجيم لمدة 45+ دقيقة'],
                'translation_key' => 'activity.gym_workout'
            ],
            'Cycling' => [
                'name_translations' => ['en' => 'Cycling', 'ar' => 'ركوب الدراجة'],
                'description_translations' => ['en' => 'Cycling for 30+ minutes', 'ar' => 'ركوب الدراجة لمدة 30+ دقيقة'],
                'translation_key' => 'activity.cycling'
            ],
            'Swimming' => [
                'name_translations' => ['en' => 'Swimming', 'ar' => 'السباحة'],
                'description_translations' => ['en' => 'Swimming for 30+ minutes', 'ar' => 'السباحة لمدة 30+ دقيقة'],
                'translation_key' => 'activity.swimming'
            ],
            'Stretching' => [
                'name_translations' => ['en' => 'Stretching', 'ar' => 'التمدد'],
                'description_translations' => ['en' => 'Stretching session for 15+ minutes', 'ar' => 'جلسة تمدد لمدة 15+ دقيقة'],
                'translation_key' => 'activity.stretching'
            ],
            'Home Workout' => [
                'name_translations' => ['en' => 'Home Workout', 'ar' => 'تمرين منزلي'],
                'description_translations' => ['en' => 'Home workout for 30+ minutes', 'ar' => 'تمرين منزلي لمدة 30+ دقيقة'],
                'translation_key' => 'activity.home_workout'
            ],
            'Sports' => [
                'name_translations' => ['en' => 'Sports', 'ar' => 'الرياضة'],
                'description_translations' => ['en' => 'Playing sports for 30+ minutes', 'ar' => 'ممارسة الرياضة لمدة 30+ دقيقة'],
                'translation_key' => 'activity.sports'
            ],
            'Hiking' => [
                'name_translations' => ['en' => 'Hiking', 'ar' => 'المشي لمسافات طويلة'],
                'description_translations' => ['en' => 'Hiking for 60+ minutes', 'ar' => 'المشي لمسافات طويلة لمدة 60+ دقيقة'],
                'translation_key' => 'activity.hiking'
            ],
            'HIIT Training' => [
                'name_translations' => ['en' => 'HIIT Training', 'ar' => 'تدريب HIIT'],
                'description_translations' => ['en' => 'High Intensity Interval Training', 'ar' => 'تدريب عالي الكثافة'],
                'translation_key' => 'activity.hiit_training'
            ],
            'Stairs Climbing' => [
                'name_translations' => ['en' => 'Stairs Climbing', 'ar' => 'صعود السلالم'],
                'description_translations' => ['en' => 'Climbing stairs for 10+ minutes', 'ar' => 'صعود السلالم لمدة 10+ دقيقة'],
                'translation_key' => 'activity.stairs_climbing'
            ],
            'Active Break' => [
                'name_translations' => ['en' => 'Active Break', 'ar' => 'استراحة نشطة'],
                'description_translations' => ['en' => 'Taking active breaks during work', 'ar' => 'أخذ استراحات نشطة أثناء العمل'],
                'translation_key' => 'activity.active_break'
            ],
            'Jump Rope' => [
                'name_translations' => ['en' => 'Jump Rope', 'ar' => 'القفز بالحبل'],
                'description_translations' => ['en' => 'Jump rope for 15+ minutes', 'ar' => 'القفز بالحبل لمدة 15+ دقيقة'],
                'translation_key' => 'activity.jump_rope'
            ],
            'Prayer/Meditation' => [
                'name_translations' => ['en' => 'Prayer/Meditation', 'ar' => 'الصلاة/التأمل'],
                'description_translations' => ['en' => 'Prayer or meditation session', 'ar' => 'جلسة صلاة أو تأمل'],
                'translation_key' => 'activity.prayer_meditation'
            ],
            'Drink Water' => [
                'name_translations' => ['en' => 'Drink Water', 'ar' => 'شرب الماء'],
                'description_translations' => ['en' => 'Drinking 8+ glasses of water', 'ar' => 'شرب 8+ أكواب من الماء'],
                'translation_key' => 'activity.drink_water'
            ],
            'Eat Vegetables' => [
                'name_translations' => ['en' => 'Eat Vegetables', 'ar' => 'تناول الخضروات'],
                'description_translations' => ['en' => 'Eating 3+ servings of vegetables', 'ar' => 'تناول 3+ حصص من الخضروات'],
                'translation_key' => 'activity.eat_vegetables'
            ],
            'Eat Fruits' => [
                'name_translations' => ['en' => 'Eat Fruits', 'ar' => 'تناول الفواكه'],
                'description_translations' => ['en' => 'Eating 2+ servings of fruits', 'ar' => 'تناول 2+ حصص من الفواكه'],
                'translation_key' => 'activity.eat_fruits'
            ],
            'Eat Breakfast' => [
                'name_translations' => ['en' => 'Eat Breakfast', 'ar' => 'تناول الفطور'],
                'description_translations' => ['en' => 'Eating a healthy breakfast', 'ar' => 'تناول فطور صحي'],
                'translation_key' => 'activity.eat_breakfast'
            ],
            'Eat Lunch' => [
                'name_translations' => ['en' => 'Eat Lunch', 'ar' => 'تناول الغداء'],
                'description_translations' => ['en' => 'Eating a healthy lunch', 'ar' => 'تناول غداء صحي'],
                'translation_key' => 'activity.eat_lunch'
            ],
            'Eat Dinner' => [
                'name_translations' => ['en' => 'Eat Dinner', 'ar' => 'تناول العشاء'],
                'description_translations' => ['en' => 'Eating a healthy dinner', 'ar' => 'تناول عشاء صحي'],
                'translation_key' => 'activity.eat_dinner'
            ],
            'Eat Protein' => [
                'name_translations' => ['en' => 'Eat Protein', 'ar' => 'تناول البروتين'],
                'description_translations' => ['en' => 'Meeting daily protein requirements', 'ar' => 'تحقيق متطلبات البروتين اليومية'],
                'translation_key' => 'activity.eat_protein'
            ],
            'Cook at Home' => [
                'name_translations' => ['en' => 'Cook at Home', 'ar' => 'الطبخ في المنزل'],
                'description_translations' => ['en' => 'Preparing healthy meals at home', 'ar' => 'تحضير وجبات صحية في المنزل'],
                'translation_key' => 'activity.cook_home'
            ],
            'Avoid Junk Food' => [
                'name_translations' => ['en' => 'Avoid Junk Food', 'ar' => 'تجنب الوجبات السريعة'],
                'description_translations' => ['en' => 'Avoiding processed and junk food', 'ar' => 'تجنب الأطعمة المصنعة والوجبات السريعة'],
                'translation_key' => 'activity.avoid_junk_food'
            ],
            'Eat Whole Grains' => [
                'name_translations' => ['en' => 'Eat Whole Grains', 'ar' => 'تناول الحبوب الكاملة'],
                'description_translations' => ['en' => 'Eating whole grain foods', 'ar' => 'تناول الأطعمة المصنوعة من الحبوب الكاملة'],
                'translation_key' => 'activity.eat_whole_grains'
            ],
            'Healthy Snacks' => [
                'name_translations' => ['en' => 'Healthy Snacks', 'ar' => 'وجبات خفيفة صحية'],
                'description_translations' => ['en' => 'Choosing healthy snacks', 'ar' => 'اختيار وجبات خفيفة صحية'],
                'translation_key' => 'activity.healthy_snacks'
            ],
            'Take Vitamins' => [
                'name_translations' => ['en' => 'Take Vitamins', 'ar' => 'تناول الفيتامينات'],
                'description_translations' => ['en' => 'Taking daily vitamins', 'ar' => 'تناول الفيتامينات اليومية'],
                'translation_key' => 'activity.take_vitamins'
            ],
            'Limit Sugar' => [
                'name_translations' => ['en' => 'Limit Sugar', 'ar' => 'تقييد السكر'],
                'description_translations' => ['en' => 'Limiting sugar intake', 'ar' => 'تقييد تناول السكر'],
                'translation_key' => 'activity.limit_sugar'
            ],
            'Portion Control' => [
                'name_translations' => ['en' => 'Portion Control', 'ar' => 'التحكم في الحصص'],
                'description_translations' => ['en' => 'Controlling portion sizes', 'ar' => 'التحكم في أحجام الحصص'],
                'translation_key' => 'activity.portion_control'
            ],
            'Meal Planning' => [
                'name_translations' => ['en' => 'Meal Planning', 'ar' => 'تخطيط الوجبات'],
                'description_translations' => ['en' => 'Planning healthy meals', 'ar' => 'تخطيط وجبات صحية'],
                'translation_key' => 'activity.meal_planning'
            ],
        ];

        $updated = 0;
        $notFound = 0;

        foreach ($translations as $name => $translationData) {
            $activityType = ActivityType::where('name', $name)->first();
            if ($activityType) {
                $activityType->update($translationData);
                $this->line("✅ Updated: {$name}");
                $updated++;
            } else {
                $this->warn("❌ Not found: {$name}");
                $notFound++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("✅ Updated: {$updated} activity types");
        $this->info("❌ Not found: {$notFound} activity types");
        $this->info("\n🎉 Translation update completed!");
    }
}