<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Streak Bonus Tiers
    |--------------------------------------------------------------------------
    |
    | Define the streak tiers and their corresponding point multipliers.
    | Each tier rewards users for maintaining consecutive days of activity.
    |
    */
    'streak_tiers' => [
        ['min' => 1, 'max' => 2, 'name' => 'Newcomer', 'multiplier' => 1.0, 'icon' => '🌱'],
        ['min' => 3, 'max' => 6, 'name' => 'Beginner', 'multiplier' => 1.2, 'icon' => '🔥'],
        ['min' => 7, 'max' => 13, 'name' => 'Regular', 'multiplier' => 1.5, 'icon' => '⚡'],
        ['min' => 14, 'max' => 29, 'name' => 'Committed', 'multiplier' => 2.0, 'icon' => '💪'],
        ['min' => 30, 'max' => 59, 'name' => 'Dedicated', 'multiplier' => 2.5, 'icon' => '🚀'],
        ['min' => 60, 'max' => 99, 'name' => 'Expert', 'multiplier' => 3.0, 'icon' => '⭐'],
        ['min' => 100, 'max' => 199, 'name' => 'Master', 'multiplier' => 4.0, 'icon' => '👑'],
        ['min' => 200, 'max' => PHP_INT_MAX, 'name' => 'Legend', 'multiplier' => 5.0, 'icon' => '🏆'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Milestone Bonuses
    |--------------------------------------------------------------------------
    |
    | Additional one-time bonus points awarded when reaching milestone streaks
    |
    */
    'milestone_bonuses' => [
        7 => 50,      // 1 week bonus
        30 => 200,    // 1 month bonus
        60 => 500,    // 2 months bonus
        100 => 1000,  // 100 days bonus
        365 => 5000,  // 1 year bonus
    ],
];

