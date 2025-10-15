<?php

namespace App\Enums;

enum ActivityCategory: string
{
    case WORKOUT = 'workout';
    case NUTRITION = 'nutrition';
    case WELLNESS = 'wellness';
    case MINDFULNESS = 'mindfulness';

    public function label(): string
    {
        return match($this) {
            self::WORKOUT => 'Workout',
            self::NUTRITION => 'Nutrition',
            self::WELLNESS => 'Wellness',
            self::MINDFULNESS => 'Mindfulness',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::WORKOUT => 'Physical activities and exercises',
            self::NUTRITION => 'Healthy eating and hydration',
            self::WELLNESS => 'Sleep, recovery, and lifestyle',
            self::MINDFULNESS => 'Mental health and stress management',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::WORKOUT => '💪',
            self::NUTRITION => '🥗',
            self::WELLNESS => '🌟',
            self::MINDFULNESS => '🧘',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

