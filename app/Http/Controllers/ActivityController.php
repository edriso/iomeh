<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                'habit_id' => 'You have already logged this activity for today.'
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Get base points from activity type
        $basePoints = $habit->activityType->base_points;
        
        // Apply streak bonus multiplier (uses CURRENT streak before increment)
        $pointsEarned = $user->calculatePointsWithStreakBonus($basePoints);
        
        // Calculate what the NEW streak will be after this activity
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $predictedStreak = $user->current_streak;
        
        if ($user->last_activity_date && $user->last_activity_date->toDateString() === $yesterday) {
            // Consecutive day - streak will increment
            $predictedStreak = $user->current_streak + 1;
        } elseif (!$user->last_activity_date || $user->last_activity_date->toDateString() !== $today) {
            // Gap or first activity - streak will reset to 1
            $predictedStreak = 1;
        }
        // else: same day, streak stays the same
        
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

        // Build success message
        $message = 'Activity logged successfully!';
        if ($milestoneBonus > 0) {
            $message .= " 🎉 Milestone bonus: +{$milestoneBonus} points for reaching {$predictedStreak} day streak!";
        }

        return back()->with('success', $message);
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

        return back()->with('success', 'Activity updated successfully!');
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

        $activity->delete();

        return back()->with('success', 'Activity deleted successfully!');
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

        // Use the date from the route parameter
        $activities = Activity::where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->with(['habit.activityType'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Format activities for response
        $formattedActivities = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'activity_type_name' => $activity->habit->activityType->name,
                'activity_type_icon' => $activity->habit->activityType->icon,
                'custom_name' => $activity->habit->custom_name ?: $activity->habit->activityType->name,
                'date' => $activity->date->format('Y-m-d'),
                'points_earned' => $activity->points_earned,
                'notes' => $activity->notes,
                'memory_url' => $activity->memory_url,
            ];
        });

        return response()->json([
            'activities' => $formattedActivities,
            'date' => $date,
            'count' => $formattedActivities->count(),
        ]);
    }
}

