<?php

/**
 * Demo script to test season validation functionality
 * 
 * Run with: php test_season_validation_demo.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Season;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n============================================\n";
echo "   SEASON VALIDATION DEMONSTRATION\n";
echo "============================================\n\n";

// Test current season
echo "📅 CURRENT SEASON INFORMATION\n";
echo "--------------------------------------------\n";
$seasonNum = Season::getCurrentSeasonNumber();
$seasonDates = Season::getCurrentSeasonDates();

echo "Current Season: Q{$seasonNum}\n";
echo "Season Year: {$seasonDates['year']}\n";
echo "Start Date: {$seasonDates['start']->format('Y-m-d (M j, Y)')}\n";
echo "End Date: {$seasonDates['end']->format('Y-m-d (M j, Y)')}\n";
echo "Today: " . now()->format('Y-m-d (M j, Y)') . "\n\n";

// Test date validation
echo "✅ VALIDATION TESTS\n";
echo "--------------------------------------------\n";

$testDates = [
    'Today' => now()->format('Y-m-d'),
    'Season Start' => $seasonDates['start']->format('Y-m-d'),
    'Season End' => $seasonDates['end']->format('Y-m-d'),
    'Before Season' => $seasonDates['start']->subDays(1)->format('Y-m-d'),
    'After Season' => $seasonDates['end']->addDays(1)->format('Y-m-d'),
];

foreach ($testDates as $label => $date) {
    $isValid = Season::isDateInCurrentSeason($date);
    $status = $isValid ? '✅ VALID' : '❌ INVALID';
    echo "{$label} ({$date}): {$status}\n";
}

// Test all quarters
echo "\n🗓️  ALL QUARTERS (2025)\n";
echo "--------------------------------------------\n";
for ($q = 1; $q <= 4; $q++) {
    $start = Season::getSeasonStartDate($q, 2025);
    $end = Season::getSeasonEndDate($q, 2025);
    echo "Q{$q}: {$start->format('M j')} - {$end->format('M j, Y')}\n";
}

// Test validation rule
echo "\n🔒 VALIDATION RULE TEST\n";
echo "--------------------------------------------\n";
$rule = new \App\Rules\CurrentSeasonDate();

$validDate = $seasonDates['start']->addDays(5)->format('Y-m-d');
$invalidDate = $seasonDates['start']->subDays(5)->format('Y-m-d');

$errors = [];

echo "Testing valid date ({$validDate}):\n";
$rule->validate('date', $validDate, function($message) use (&$errors) {
    $errors[] = $message;
});
echo empty($errors) ? "  ✅ Passed validation\n" : "  ❌ Failed: {$errors[0]}\n";

$errors = [];
echo "\nTesting invalid date ({$invalidDate}):\n";
$rule->validate('date', $invalidDate, function($message) use (&$errors) {
    $errors[] = $message;
});
echo empty($errors) ? "  ✅ Passed validation\n" : "  ❌ Failed (as expected)\n  Error: {$errors[0]}\n";

// Test different seasons
echo "\n🌍 SEASON TESTING (Simulated Dates)\n";
echo "--------------------------------------------\n";

$testSeasons = [
    ['month' => 2, 'day' => 15, 'name' => 'Q1'],
    ['month' => 5, 'day' => 20, 'name' => 'Q2'],
    ['month' => 8, 'day' => 10, 'name' => 'Q3'],
    ['month' => 11, 'day' => 25, 'name' => 'Q4'],
];

foreach ($testSeasons as $test) {
    Carbon::setTestNow(Carbon::create(2025, $test['month'], $test['day']));
    $dates = Season::getCurrentSeasonDates();
    $testDate = now()->format('M j, Y');
    echo "Date: {$testDate} → Detected as {$test['name']} ✓\n";
    echo "  Range: {$dates['start']->format('M j')} - {$dates['end']->format('M j, Y')}\n";
}

Carbon::setTestNow(); // Reset

echo "\n============================================\n";
echo "   DEMONSTRATION COMPLETE\n";
echo "============================================\n\n";

echo "📚 Documentation:\n";
echo "  - See docs/SEASON_VALIDATION.md for full documentation\n";
echo "  - See docs/SEASON_VALIDATION_QUICK_REFERENCE.md for quick reference\n\n";

echo "🧪 Run Tests:\n";
echo "  ./vendor/bin/pest tests/Feature/ActivitySeasonValidationTest.php\n\n";

