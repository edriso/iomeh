<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    /**
     * Store a new activity
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'interest_id' => ['required', 'exists:interests,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'proof_url' => ['nullable', 'url', 'max:255'],
        ]);

        // Verify the interest belongs to the authenticated user and load activity type
        $interest = Interest::with('activityType')
            ->where('id', $validated['interest_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if activity already exists for this date and interest
        $existingActivity = Activity::where('user_id', Auth::id())
            ->where('interest_id', $validated['interest_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existingActivity) {
            return back()->withErrors([
                'interest_id' => 'You have already logged this activity for this date.'
            ]);
        }

        // Get points from activity type
        $pointsEarned = $interest->activityType->base_points;

        $activity = Activity::create([
            'user_id' => Auth::id(),
            'interest_id' => $validated['interest_id'],
            'date' => $validated['date'],
            'points_earned' => $pointsEarned,
            'notes' => $validated['notes'] ?? null,
            'proof_url' => $validated['proof_url'] ?? null,
        ]);

        return back()->with('success', 'Activity logged successfully!');
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

        // Users can only update notes and proof_url, not points
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
            'proof_url' => ['nullable', 'url', 'max:255'],
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
            ->with(['interest.activityType'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Format activities for response
        $formattedActivities = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'activity_type_name' => $activity->interest->activityType->name,
                'activity_type_icon' => $activity->interest->activityType->icon,
                'custom_name' => $activity->interest->custom_name ?: $activity->interest->activityType->name,
                'date' => $activity->date->format('Y-m-d'),
                'points_earned' => $activity->points_earned,
                'notes' => $activity->notes,
                'proof_url' => $activity->proof_url,
            ];
        });

        return response()->json([
            'activities' => $formattedActivities,
            'date' => $date,
            'count' => $formattedActivities->count(),
        ]);
    }
}

