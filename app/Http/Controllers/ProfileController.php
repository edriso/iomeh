<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function show(string $username): Response
    {
        $user = User::where('username', $username)->first();
        
        if (!$user) {
            return Inertia::render('errors/404', [
                'title' => 'Profile Not Found',
                'message' => 'The profile you\'re looking for does not exist.',
            ]);
        }
        
        return Inertia::render('Profile', [
            'user' => $this->formatUserData($user),
            'is_own_profile' => Auth::id() === $user->id,
            'recent_activities' => $this->getRecentActivities($user),
            'calendar_data' => $this->getCalendarData($user),
            'stats' => $this->getUserStats($user),
        ]);
    }

    /**
     * Format user data for frontend
     */
    private function formatUserData(User $user): array
    {
        $currentSeasonName = ceil(now()->month / 3);
        $userSeason = $user->seasons()
            ->where('year', now()->year)
            ->where('name', $currentSeasonName)
            ->first();
        
        $yearSeason = $user->seasons()
            ->where('year', now()->year)
            ->orderBy('season_year_points', 'desc')
            ->first();
        
        return [
            'id' => $user->id,
            'name' => $user->name ?? $user->username,
            'username' => $user->username,
            'bio' => $user->bio ?? null,
            'title' => $user->title ?? null,
            'website_url' => $user->website_url ?? null,
            'avatar' => $user->avatar ?? null,
            'current_season_points' => $userSeason?->points ?? 0,
            'current_year_points' => $yearSeason?->season_year_points ?? 0,
            'lifetime_points' => $user->lifetime_points ?? 0,
            'current_streak' => $user->current_streak ?? 0,
            'longest_streak' => $user->longest_streak ?? 0,
            'last_activity_date' => $user->last_activity_date?->format('Y-m-d'),
            'joined_at' => $user->created_at->toISOString(),
            'created_at' => $user->created_at->toISOString(),
            'week_starts_on' => $user->week_starts_on ?? 0, // Default to Sunday
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(User $user): array
    {
        return $user->activities()
            ->with(['interest.activityType'])
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'activity_type_name' => $activity->interest->activityType->name,
                    'activity_type_icon' => $activity->interest->activityType->icon,
                    'custom_name' => $activity->interest->custom_name,
                    'date' => $activity->date->format('Y-m-d'),
                    'points_earned' => $activity->points_earned,
                    'notes' => $activity->notes,
                ];
            })
            ->toArray();
    }

    /**
     * Get calendar data for user activity heatmap
     */
    private function getCalendarData(User $user): array
    {
        $activities = Activity::where('user_id', $user->id)
            ->selectRaw('date, SUM(points_earned) as total_points, COUNT(*) as activity_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        if ($activities->isEmpty()) {
            return [];
        }
        
        // Convert to array format expected by frontend
        return $activities->map(function ($activity) {
            return [
                'date' => $activity->date->format('Y-m-d'),
                'activities_count' => $activity->activity_count,
                'points' => $activity->total_points,
            ];
        })->toArray();
    }

    /**
     * Get user statistics
     */
    private function getUserStats(User $user): array
    {
        $totalActivities = Activity::where('user_id', $user->id)->count();
        $activeDays = Activity::where('user_id', $user->id)
            ->selectRaw('COUNT(DISTINCT date) as days')
            ->first()
            ->days ?? 0;
        
        $currentSeasonName = ceil(now()->month / 3);
        $userSeason = $user->seasons()
            ->where('year', now()->year)
            ->where('name', $currentSeasonName)
            ->first();
        
        $yearSeason = $user->seasons()
            ->where('year', now()->year)
            ->orderBy('season_year_points', 'desc')
            ->first();
        
        return [
            'lifetime_points' => $user->lifetime_points ?? 0,
            'current_season_points' => $userSeason?->points ?? 0,
            'current_year_points' => $yearSeason?->season_year_points ?? 0,
            'current_streak' => $user->current_streak ?? 0,
            'longest_streak' => $user->longest_streak ?? 0,
            'total_activities' => $totalActivities,
            'active_days' => $activeDays,
            'interests_count' => $user->interests()->count(),
        ];
    }
}
