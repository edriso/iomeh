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
                'title' => __('profile.not_found'),
                'message' => __('profile.not_found_message'),
            ]);
        }
        
        $isOwnProfile = Auth::id() === $user->id;
        
        // Cache season data to avoid duplicate queries
        $currentYear = now()->year;
        $currentSeasonName = ceil(now()->month / 3);
        $seasons = $user->seasons()
            ->where('year', $currentYear)
            ->orderBy('season_year_points', 'desc')
            ->get();
        
        return Inertia::render('Profile', [
            'user' => $this->formatUserData($user, $seasons, $currentSeasonName),
            'is_own_profile' => $isOwnProfile,
            'recent_activities' => $this->getRecentActivities($user),
            'calendar_data' => $this->getCalendarData($user),
            'stats' => $this->getUserStats($user, $seasons, $currentSeasonName),
            'habits' => $this->getUserHabits($user, $isOwnProfile),
            'ranking_histories' => $this->getRankingHistories($user),
        ]);
    }

    /**
     * Format user data for frontend
     */
    private function formatUserData(User $user, $seasons, int $currentSeasonName): array
    {
        $userSeason = $seasons->firstWhere('quarter_number', $currentSeasonName);
        $yearSeason = $seasons->first();
        
        return [
            'id' => $user->id,
            'name' => $user->name ?? $user->username,
            'username' => $user->username,
            'bio' => $user->bio,
            'title' => $user->title,
            'website_url' => $user->website_url,
            'avatar' => $user->avatar,
            'current_season_points' => $userSeason?->points ?? 0,
            'current_year_points' => $yearSeason?->season_year_points ?? 0,
            'lifetime_points' => $user->lifetime_points,
            'current_streak' => $user->current_streak,
            'longest_streak' => $user->longest_streak,
            'last_activity_date' => $user->last_activity_date?->format('Y-m-d'),
            'created_at' => $user->created_at->toISOString(),
            'week_starts_on' => $user->week_starts_on ?? 6,
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(User $user): array
    {
        return $user->activities()
            ->with(['habit.activityType'])
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get()
            ->map(fn($activity) => [
                'id' => $activity->id,
                'activity_type_name' => $activity->habit->activityType->name,
                'activity_type_icon' => $activity->habit->activityType->icon,
                'custom_name' => $activity->habit->custom_name,
                'date' => $activity->date->format('Y-m-d'),
                'points_earned' => $activity->points_earned,
                'notes' => $activity->notes,
            ])
            ->toArray();
    }

    /**
     * Get calendar data for user activity heatmap
     */
    private function getCalendarData(User $user): array
    {
        return Activity::where('user_id', $user->id)
            ->selectRaw('date, SUM(points_earned) as total_points, COUNT(*) as activity_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($activity) => [
                'date' => $activity->date->format('Y-m-d'),
                'activities_count' => $activity->activity_count,
                'points' => $activity->total_points,
            ])
            ->toArray();
    }

    /**
     * Get user statistics
     */
    private function getUserStats(User $user, $seasons, int $currentSeasonName): array
    {
        // Optimize: Single query for activity stats
        $activityStats = Activity::where('user_id', $user->id)
            ->selectRaw('COUNT(*) as total_activities, COUNT(DISTINCT date) as active_days')
            ->first();
        
        $userSeason = $seasons->firstWhere('quarter_number', $currentSeasonName);
        $yearSeason = $seasons->first();
        
        return [
            'lifetime_points' => $user->lifetime_points,
            'current_season_points' => $userSeason?->points ?? 0,
            'current_year_points' => $yearSeason?->season_year_points ?? 0,
            'current_streak' => $user->current_streak,
            'longest_streak' => $user->longest_streak,
            'total_activities' => $activityStats->total_activities ?? 0,
            'active_days' => $activityStats->active_days ?? 0,
            'habits_count' => $user->habits()->count(),
        ];
    }

    /**
     * Get user's habits
     */
    private function getUserHabits(User $user, bool $isOwnProfile): array
    {
        return $user->habits()
            ->with('activityType')
            ->orderBy('display_order')
            ->get()
            ->map(fn($habit) => [
                'id' => $habit->id,
                'name' => $habit->custom_name,
                'icon' => $habit->activityType->icon,
                'category' => $habit->activityType->category->value,
                'activity_type_id' => $habit->activity_type_id,
                'base_points' => $habit->activityType->base_points,
                'has_activity_today' => $isOwnProfile && $habit->hasActivityToday(),
            ])
            ->toArray();
    }

    /**
     * Get user's ranking histories
     */
    private function getRankingHistories(User $user): array
    {
        return $user->rankingHistory()
            ->orderBy('year', 'desc')
            ->orderBy('season', 'desc')
            ->get()
            ->map(fn($history) => [
                'id' => $history->id,
                'season' => $history->season,
                'year' => $history->year,
                'points' => $history->points,
                'rank' => $history->rank,
                'season_name' => $history->season_name,
                'display_name' => $history->display_name,
            ])
            ->toArray();
    }
}
