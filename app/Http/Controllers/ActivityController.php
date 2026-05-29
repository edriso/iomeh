<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Store a new activity for today.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'habit_id' => ['required', 'exists:habits,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'memory_url' => ['nullable', 'url', 'max:255'],
        ]);

        // Ensure the habit belongs to the authenticated user and load its activity type.
        $habit = Habit::with('activityType')
            ->where('id', $validated['habit_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Load the user fresh so streak/points reflect the latest persisted state.
        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        $today = now();
        $todayString = $today->toDateString();

        // A habit may only be logged once per day.
        $alreadyLogged = Activity::where('user_id', $user->id)
            ->where('habit_id', $habit->id)
            ->whereDate('date', $todayString)
            ->exists();

        if ($alreadyLogged) {
            return back()->withErrors([
                'habit_id' => __('activity.already_logged_today'),
            ]);
        }

        $isFirstActivityToday = ! Activity::where('user_id', $user->id)
            ->whereDate('date', $todayString)
            ->exists();

        [$multiplierStreak, $predictedStreak] = $this->determineStreaks($user, $today, $isFirstActivityToday);

        // Base points scaled by the streak multiplier, plus any one-time milestone bonus.
        $pointsEarned = $this->calculatePointsWithStreakBonus($habit->activityType->base_points, $multiplierStreak);

        if ($isFirstActivityToday && $predictedStreak > ($user->current_streak ?? 0)) {
            $pointsEarned += config('gamification.milestone_bonuses', [])[$predictedStreak] ?? 0;
        }

        // The activity's "created" observer updates the user's streak and season
        // points, so create it inside a transaction to keep activity and user
        // state consistent if anything fails midway.
        DB::transaction(fn () => Activity::create([
            'user_id' => $user->id,
            'habit_id' => $habit->id,
            'date' => $todayString,
            'points_earned' => $pointsEarned,
            'notes' => $validated['notes'] ?? null,
            'memory_url' => $validated['memory_url'] ?? null,
        ]));

        // Invalidate the cached home page so the new activity shows immediately.
        Cache::forget("home_data_user_{$user->id}");

        return back()->with('success', __('success.activity_logged'));
    }

    /**
     * Determine the streak used for the points multiplier and the streak the user
     * is predicted to reach by logging this activity.
     *
     * The multiplier is based on the streak coming into today, so every activity
     * logged on the same day earns the same multiplier, and the streak only grows
     * once per day regardless of how many activities are logged.
     *
     * @return array{0:int,1:int} [streak for multiplier, predicted streak]
     */
    private function determineStreaks(User $user, \Carbon\Carbon $today, bool $isFirstActivityToday): array
    {
        $currentStreak = $user->current_streak ?? 0;

        // Additional activity on a day that already has one: reuse the streak the
        // first activity was scored with. The observer has already counted today
        // once, so the streak coming into today is current_streak - 1.
        if (! $isFirstActivityToday) {
            return [max(1, $currentStreak - 1), max(1, $currentStreak)];
        }

        // First activity ever: start a fresh streak of 1.
        if (! $user->last_activity_date) {
            return [1, 1];
        }

        $lastActivityDate = $user->last_activity_date instanceof \Carbon\Carbon
            ? $user->last_activity_date
            : \Carbon\Carbon::parse($user->last_activity_date);

        // Consecutive day: score with the streak coming into today; it then grows by one.
        if ($lastActivityDate->toDateString() === $today->copy()->subDay()->toDateString()) {
            $streak = max(1, $currentStreak);

            return [$streak, $streak + 1];
        }

        // Gap since the last activity: the streak resets to 1.
        return [1, 1];
    }

    /**
     * Calculate points with the streak bonus applied for a specific streak value.
     */
    private function calculatePointsWithStreakBonus(int $basePoints, int $streak): int
    {
        $tiers = config('gamification.streak_tiers', []);

        foreach ($tiers as $tier) {
            if ($streak >= $tier['min'] && $streak <= $tier['max']) {
                return (int) round($basePoints * ($tier['multiplier'] ?? 1.0));
            }
        }

        // Fallback to the first tier's multiplier if no tier matched.
        return (int) round($basePoints * ($tiers[0]['multiplier'] ?? 1.0));
    }

    /**
     * Update an existing activity.
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
     * Delete an activity.
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
     * Get activities for a specific date for the authenticated user.
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

            $isInactive = ! $activity->is_active;

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
