<?php

namespace App\Enums;

enum ActivityCategory: string
{
    case WORKOUT = 'workout';
    case NUTRITION = 'nutrition';

    public function label(): string
    {
        return match($this) {
            self::WORKOUT => 'Workout',
            self::NUTRITION => 'Nutrition',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::WORKOUT => 'Physical activities and exercises',
            self::NUTRITION => 'Healthy eating and hydration',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::WORKOUT => '💪',
            self::NUTRITION => '🥗',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

