<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ActivityController extends Controller
{
    /**
     * Store a new activity
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'habit_id' => ['required', 'exists:habits,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'memory_url' => ['nullable', 'url', 'max:255'],
        ]);

        // Verify the habit belongs to the authenticated user and load activity type
        $habit = Habit::with('activityType')
            ->where('id', $validated['habit_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        /** @var User $user */
        // Get user directly from database to ensure we have latest data
        // This is critical in tests where Auth::user() might be cached or not properly set
        // Don't refresh here as it might cause issues with test data
        $user = User::findOrFail(Auth::id());
        
        // Get base points from activity type
        $basePoints = $habit->activityType->base_points;
        
        // Determine the streak value to use for the multiplier
        // This should be the streak that exists RIGHT NOW, before this activity is logged
        // Use a single now() call to ensure consistency
        $now = now();
        $today = $now->copy();
        $todayString = $today->toDateString();
        $yesterday = $now->copy()->subDay();
        $yesterdayString = $yesterday->toDateString();
        
        // Check if activity already exists for today and this habit
        $existingActivity = Activity::where('user_id', Auth::id())
            ->where('habit_id', $validated['habit_id'])
            ->whereDate('date', $todayString)
            ->first();

        if ($existingActivity) {
            return back()->withErrors([
                'habit_id' => __('activity.already_logged_today')
            ]);
        }
        
        // Check if this is the first activity today
        $isFirstActivityToday = !Activity::where('user_id', $user->id)
            ->whereDate('date', $todayString)
            ->exists();
        
        if ($isFirstActivityToday) {
            // This is the first activity today
            if (!$user->last_activity_date) {
                // Very first activity ever - use streak 1
                $streakForMultiplier = 1;
                $predictedStreak = 1;
            } else {
                // Convert last_activity_date to Carbon for comparison
                $lastActivityDate = $user->last_activity_date instanceof \Carbon\Carbon 
                    ? $user->last_activity_date->copy()
                    : \Carbon\Carbon::parse($user->last_activity_date);
                
                // Normalize both to start of day for accurate comparison
                $lastActivityDateNormalized = $lastActivityDate->copy()->startOfDay();
                $yesterdayNormalized = $yesterday->copy()->startOfDay();
                $todayNormalized = $today->copy()->startOfDay();
                
                // Calculate the difference in days between last activity and today
                // Positive means last activity is in the past
                $daysSinceLastActivity = $lastActivityDateNormalized->diffInDays($todayNormalized, false);
                
                // Get date strings for direct comparison - this is the most reliable method
                // The test sets last_activity_date to now()->subDay()->toDateString()
                // and we calculate yesterday as now()->subDay()->toDateString()
                // They should match if calculated at the same time
                $lastActivityDateStr = $lastActivityDate->toDateString();
                
                // Check if last activity was exactly 1 day ago (consecutive day)
                // Primary check: direct date string comparison (most reliable)
                // Also check diffInDays as fallback for edge cases
                $isYesterday = $lastActivityDateStr === $yesterdayString
                    || ($daysSinceLastActivity == 1)
                    || $lastActivityDateNormalized->equalTo($yesterdayNormalized)
                    || $lastActivityDate->isYesterday();
                
                if ($isYesterday) {
                    // Consecutive day - the new streak will be current + 1
                    // But for this activity, use the current streak (before incrementing)
                    // Ensure we use the actual current_streak value, not 0
                    $currentStreak = $user->current_streak ?? 0;
                    $streakForMultiplier = max(1, $currentStreak);
                    $predictedStreak = $streakForMultiplier + 1;
                } elseif ($daysSinceLastActivity == 0 || $lastActivityDate->isToday() || $lastActivityDate->toDateString() === $todayString) {
                    // This shouldn't happen since we checked isFirstActivityToday, but just in case
                    $streakForMultiplier = max(1, $user->current_streak ?? 1);
                    $predictedStreak = $streakForMultiplier;
                } else {
                    // Gap in activities - streak resets to 1
                    $streakForMultiplier = 1;
                    $predictedStreak = 1;
                }
            }
        } else {
            // Multiple activities on the same day - use the streak from the first activity of the day
            // We need to calculate what streak was used for the first activity
            if (!$user->last_activity_date) {
                $streakForMultiplier = 1;
            } else {
                // Convert last_activity_date to Carbon for comparison
                $lastActivityDate = $user->last_activity_date instanceof \Carbon\Carbon 
                    ? $user->last_activity_date->copy()
                    : \Carbon\Carbon::parse($user->last_activity_date);
                
                // Normalize to start of day for accurate comparison
                $lastActivityDateNormalized = $lastActivityDate->copy()->startOfDay();
                $yesterdayNormalized = $yesterday->copy()->startOfDay();
                $todayNormalized = $today->copy()->startOfDay();
                
                // Calculate the difference in days
                $daysSinceLastActivity = $lastActivityDateNormalized->diffInDays($todayNormalized, false);
                
                // Get date strings for direct comparison
                $lastActivityDateStr = $lastActivityDate->toDateString();
                    
                // Count activities today to determine what streak was used for first activity
                $activitiesTodayCount = Activity::where('user_id', $user->id)
                    ->whereDate('date', $todayString)
                    ->count();
                
                // Check if last activity was exactly 1 day ago
                // Primary check: direct date string comparison (most reliable)
                // Also check diffInDays as fallback
                $isYesterday = $lastActivityDateStr === $yesterdayString
                    || ($daysSinceLastActivity == 1)
                    || $lastActivityDateNormalized->equalTo($yesterdayNormalized)
                    || $lastActivityDate->isYesterday();
                
                if ($isYesterday) {
                    // First activity used: current_streak - activities_today_count
                    // Ensure we use the actual current_streak value
                    $currentStreak = $user->current_streak ?? 0;
                    $streakForMultiplier = max(1, $currentStreak - $activitiesTodayCount);
                } else {
                    // Gap or first activity - first activity used streak 1
                    $streakForMultiplier = 1;
                }
            }
            $predictedStreak = max(1, $user->current_streak ?? 1);
        }
        
        // Calculate points with streak multiplier
        $pointsEarned = $this->calculatePointsWithStreakBonus($basePoints, $streakForMultiplier);
        
        // Check for milestone bonus based on the predicted NEW streak (only for first activity of the day)
        $milestones = config('gamification.milestone_bonuses', []);
        $milestoneBonus = 0;
        
        if ($isFirstActivityToday && $predictedStreak > ($user->current_streak ?? 0)) {
            // Only give milestone bonus if streak is increasing
            $milestoneBonus = $milestones[$predictedStreak] ?? 0;
        }
        
        if ($milestoneBonus > 0) {
            $pointsEarned += $milestoneBonus;
        }

        $activity = Activity::create([
            'user_id' => Auth::id(),
            'habit_id' => $validated['habit_id'],
            'date' => $todayString,
            'points_earned' => $pointsEarned,
            'notes' => $validated['notes'] ?? null,
            'memory_url' => $validated['memory_url'] ?? null,
        ]);

        // Invalidate home page cache for this user
        Cache::forget("home_data_user_{$user->id}");

        // Build success message
        $message = 'Activity logged successfully!';
        if ($milestoneBonus > 0) {
            $message .= " 🎉 Milestone bonus: +{$milestoneBonus} points for reaching {$predictedStreak} day streak!";
        }

        return back()->with('success', __('success.activity_logged'));
    }

    /**
     * Calculate points with streak bonus applied using a specific streak value
     */
    private function calculatePointsWithStreakBonus(int $basePoints, int $streak): int
    {
        $tiers = config('gamification.streak_tiers', []);
        
        foreach ($tiers as $tier) {
            if ($streak >= $tier['min'] && $streak <= $tier['max']) {
                $multiplier = $tier['multiplier'] ?? 1.0;
                return (int) round($basePoints * $multiplier);
            }
        }
        
        // Fallback to first tier if no match
        $multiplier = $tiers[0]['multiplier'] ?? 1.0;
        return (int) round($basePoints * $multiplier);
    }

    /**
     * Update an existing activity
     */
    public function update(Request $request, Activity $activity)
    {
        // Verify the activity belongs to the authenticated user
        if ($activity->user_id !== Auth::id()) {
            abort(403);
        }

        // Users can only update notes and memory_url, not points
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
            'memory_url' => ['nullable', 'url', 'max:255'],
        ]);

        $activity->update($validated);

        // Invalidate home page cache for this user
        Cache::forget("home_data_user_{$activity->user_id}");

        return back()->with('success', __('success.activity_updated'));
    }

    /**
     * Delete an activity
     */
    public function destroy(Activity $activity)
    {
        // Verify the activity belongs to the authenticated user
        if ($activity->user_id !== Auth::id()) {
            abort(403);
        }

        $userId = $activity->user_id;
        $activity->delete();

        // Invalidate home page cache for this user
        Cache::forget("home_data_user_{$userId}");

        return back()->with('success', __('success.activity_deleted'));
    }

    /**
     * Get activities for a specific date for the authenticated user
     */
    public function getByDate(Request $request, string $date)
    {
        // Validate the date format
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        // Get activities for the date - include both active and inactive habits
        $activities = Activity::where('activities.user_id', Auth::id())
            ->whereDate('activities.date', $date)
            ->join('habits', 'activities.habit_id', '=', 'habits.id')
            ->join('activity_types', 'habits.activity_type_id', '=', 'activity_types.id')
            ->select([
                'activities.id',
                'activities.habit_id',
                'activities.date',
                'activities.points_earned',
                'activities.notes',
                'activities.memory_url',
                'habits.custom_name',
                'habits.is_active',
                'activity_types.name as activity_type_name',
                'activity_types.icon as activity_type_icon',
            ])
            ->orderBy('activities.created_at', 'desc')
            ->orderBy('activities.id', 'desc')
            ->get();

        // Format activities for response
        $formattedActivities = $activities->map(function ($activity) {
            // Parse activity type name
            $activityTypeName = null;
            if ($activity->activity_type_name) {
                $nameData = is_string($activity->activity_type_name) 
                    ? json_decode($activity->activity_type_name, true) 
                    : $activity->activity_type_name;
                $activityTypeName = $nameData[app()->getLocale()] ?? $nameData['en'] ?? 'Unknown Activity';
            }

            $isInactive = !$activity->is_active;

            return [
                'id' => $activity->id,
                'activity_type_name' => $activityTypeName,
                'activity_type_icon' => $activity->activity_type_icon,
                'custom_name' => $activity->custom_name,
                'date' => $activity->date->format('Y-m-d'),
                'points_earned' => $activity->points_earned,
                'notes' => $activity->notes,
                'memory_url' => $activity->memory_url,
                'is_inactive' => $isInactive,
            ];
        });

        return response()->json([
            'activities' => $formattedActivities,
            'date' => $date,
            'count' => $formattedActivities->count(),
        ]);
    }
}

