<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Habit;
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

        // Always use today's date
        $today = now()->toDateString();

        // Verify the habit belongs to the authenticated user and load activity type
        $habit = Habit::with('activityType')
            ->where('id', $validated['habit_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if activity already exists for today and this habit
        $existingActivity = Activity::where('user_id', Auth::id())
            ->where('habit_id', $validated['habit_id'])
            ->where('date', $today)
            ->first();

        if ($existingActivity) {
            return back()->withErrors([
                'habit_id' => __('activity.already_logged_today')
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Get base points from activity type
        $basePoints = $habit->activityType->base_points;
        
        // Calculate what the NEW streak will be after this activity
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $predictedStreak = $user->current_streak;
        $isNewDay = false;
        
        if ($user->last_activity_date && $user->last_activity_date->toDateString() === $yesterday) {
            // Consecutive day - streak will increment
            $predictedStreak = $user->current_streak + 1;
            $isNewDay = true;
        } elseif (!$user->last_activity_date || $user->last_activity_date->toDateString() !== $today) {
            // Gap or first activity - streak will reset to 1
            $predictedStreak = 1;
            $isNewDay = true;
        }
        // else: same day, streak stays the same (isNewDay = false)
        
        // Calculate the streak that should be used for multiplier calculation
        // This should be the streak that was current at the beginning of the day,
        // before any activities were created today
        
        // Count how many activities have been created today
        $activitiesTodayCount = Activity::where('user_id', $user->id)
            ->where('date', $today)
            ->count();
        
        // Calculate the streak at the beginning of today
        if ($user->last_activity_date && $user->last_activity_date->toDateString() === $yesterday) {
            // If last activity was yesterday, the streak at the beginning of today was current_streak - activities_today_count
            $streakForMultiplier = $user->current_streak - $activitiesTodayCount;
        } else {
            // If gap or first activity, the streak at the beginning of today was 0 (so first activity used 1)
            $streakForMultiplier = 1;
        }
        
        $pointsEarned = $this->calculatePointsWithStreakBonus($basePoints, $streakForMultiplier);
        
        // Check for milestone bonus based on the NEW streak
        $milestones = config('gamification.milestone_bonuses', []);
        $milestoneBonus = $milestones[$predictedStreak] ?? 0;
        
        if ($milestoneBonus > 0) {
            $pointsEarned += $milestoneBonus;
        }

        $activity = Activity::create([
            'user_id' => Auth::id(),
            'habit_id' => $validated['habit_id'],
            'date' => $today,
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

