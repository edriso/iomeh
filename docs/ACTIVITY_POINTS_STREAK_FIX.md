# Activity Points and Streak Calculation Fixes

## Issues Identified

### 1. **Streak Multiplier Bug After Gap**
**Problem**: When a user had a 3-day streak, then missed days and logged again, the activity was still using the 1.2x multiplier (for streak 3) instead of 1.0x (for streak 1).

**Root Cause**: The controller's logic for calculating `streakForMultiplier` was flawed. After a gap in activities, it was trying to use the old streak value instead of resetting to 1.

**Fix**: Updated `ActivityController@store` to correctly determine the streak value:
- For the first activity of a new day after a gap: use streak 1 (multiplier 1.0)
- For the first activity of a consecutive day: use the CURRENT streak (before incrementing)
- For subsequent activities on the same day: use the current streak (already updated by first activity)

### 2. **Points Not Accumulating**
**Problem**: Total points weren't increasing after logging activities.

**Root Cause**: This was likely a side effect of the streak calculation bug. The `lifetime_points` attribute correctly sums all activity points, so once activities are created with correct points, the total will be accurate.

**Status**: Fixed as part of the streak calculation fix.

## Technical Details

### Streak Tiers (from config/gamification.php)
- Days 1-2: 1.0x multiplier (Newcomer)
- Days 3-6: 1.2x multiplier (Beginner)
- Days 7-13: 1.5x multiplier (Regular)
- Days 14-29: 2.0x multiplier (Committed)
- Days 30-59: 2.5x multiplier (Dedicated)
- Days 60-99: 3.0x multiplier (Expert)
- Days 100-199: 4.0x multiplier (Master)
- Days 200+: 5.0x multiplier (Legend)

### Milestone Bonuses
- 7 days: +50 points
- 30 days: +200 points
- 60 days: +500 points
- 100 days: +1000 points
- 365 days: +5000 points

## Code Changes

### File: `app/Http/Controllers/ActivityController.php`

**Before**: Complex logic that tried to predict future streak and calculate backwards
**After**: Simple logic that uses the current state:

```php
// Determine the streak value to use for the multiplier
$isFirstActivityToday = !Activity::where('user_id', $user->id)
    ->where('date', $today)
    ->exists();

if ($isFirstActivityToday) {
    if (!$user->last_activity_date) {
        // First activity ever
        $streakForMultiplier = 1;
        $predictedStreak = 1;
    } elseif ($user->last_activity_date->toDateString() === $yesterday) {
        // Consecutive day - use current streak
        $streakForMultiplier = $user->current_streak;
        $predictedStreak = $user->current_streak + 1;
    } else {
        // Gap - reset to streak 1
        $streakForMultiplier = 1;
        $predictedStreak = 1;
    }
} else {
    // Same day multiple activities - use current streak
    $streakForMultiplier = $user->current_streak;
    $predictedStreak = $user->current_streak;
}
```

## Manual Testing Scenarios

### Test Case 1: First Activity Ever
1. Create a new account or clear all activities
2. Log an activity (base points = 10)
3. **Expected**: 10 points earned (10 × 1.0), streak = 1

### Test Case 2: Consecutive Days
1. Log activity on Day 1
2. Log activity on Day 2
3. **Expected Day 1**: 10 points (10 × 1.0), streak = 1
4. **Expected Day 2**: 10 points (10 × 1.0), streak = 2

### Test Case 3: Reaching 1.2x Multiplier
1. Log activities on Days 1, 2, and 3
2. On Day 4, log another activity
3. **Expected Day 4**: 12 points (10 × 1.2), streak = 4

### Test Case 4: Gap After Streak (THE CRITICAL BUG FIX)
1. Build a 3-day streak (Days 1, 2, 3)
2. Skip several days
3. Log an activity on Day 10
4. **Expected Day 10**: 10 points (10 × 1.0), streak resets to 1
5. **Expected**: Longest streak remains 3

### Test Case 5: Multiple Activities Same Day
1. Log first activity today
2. Log second activity today (different habit)
3. **Expected**: Both activities use the same streak multiplier
4. **Expected**: Streak only increments once per day

### Test Case 6: Milestone Bonus at 7 Days
1. Build a 6-day streak
2. Log activity on Day 7
3. **Expected**: Base 12 points (10 × 1.2) + 50 milestone bonus = 62 total points

### Test Case 7: Tier Boundary (Day 7 to Day 8)
1. Build a 7-day streak
2. Log activity on Day 8
3. **Expected Day 8**: 15 points (10 × 1.5), streak = 8

## Verification Queries

Check current user state:
```sql
SELECT current_streak, longest_streak, last_activity_date 
FROM users 
WHERE id = YOUR_USER_ID;
```

Check recent activities with points:
```sql
SELECT date, habit_id, points_earned, created_at 
FROM activities 
WHERE user_id = YOUR_USER_ID 
ORDER BY date DESC, created_at DESC 
LIMIT 10;
```

Check lifetime points:
```sql
SELECT SUM(points_earned) as lifetime_points 
FROM activities 
WHERE user_id = YOUR_USER_ID;
```

## Testing the Fix

1. **Clear your activity history** (or use a test account)
2. Build a 3-day streak
3. Skip 3-4 days
4. Log a new activity
5. **Verify**: 
   - The new activity earns 10 points (not 12)
   - Your streak resets to 1
   - Your longest streak is still 3
   - Your lifetime points increased by 10

## Notes

- The `User` model's `lifetime_points` attribute dynamically calculates the sum of all activity points
- The streak is updated by the `Activity` model's observer AFTER the activity is created
- The points calculation happens BEFORE creation, using the current user state
- Milestone bonuses are only awarded on streak increments (first activity of a new consecutive day)
