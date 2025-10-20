<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Handle the home route - show landing page for guests, home page for authenticated users.
     */
    public function home(Request $request)
    {
        // Show landing page for guests
        if (!Auth::check()) {
            return $this->landing();
        }

        // Check email verification
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // Show authenticated home page
        return $this->index($request);
    }

    /**
     * Display the landing page with stats.
     */
    private function landing()
    {
        $stats = [
            'active_users' => User::where('is_active', true)->count(),
            'total_activities' => Activity::count(),
            'combined_streak' => User::sum('current_streak'),
        ];

        return Inertia::render('Landing', [
            'stats' => $stats,
        ]);
    }

    /**
     * Display the authenticated home page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Cache key for user-specific data
        $cacheKey = "home_data_user_{$user->id}";
        
        // Get cached data or compute fresh data
        $homeData = Cache::remember($cacheKey, 300, function () use ($user) { // 5 minutes cache
            return $this->getHomeData($user);
        });

        return Inertia::render('Home', $homeData);
    }

    /**
     * Get home page data with caching
     */
    private function getHomeData(User $user): array
    {
        // Get today's date for activity checking
        $today = now()->toDateString();
        
        // Get user's active habits with activity types and today's activities
        $habits = $user->activeHabits()
            ->with(['activityType', 'activities' => function ($query) use ($today) {
                $query->whereDate('date', $today);
            }])
            ->get()
            ->map(function ($habit) {
                return [
                    'id' => $habit->id,
                    'name' => $habit->custom_name,
                    'icon' => $habit->getEffectiveIcon(),
                    'custom_icon' => $habit->custom_icon,
                    'category' => $habit->activityType->category->value,
                    'activity_type_id' => $habit->activity_type_id,
                    'base_points' => $habit->activityType->base_points,
                    'has_activity_today' => $habit->activities->isNotEmpty(),
                    'notes' => $habit->notes,
                ];
            });

        // Get today's activities
        $todayActivities = $user->getTodayActivities()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'habit_name' => $activity->habit->custom_name,
                    'points_earned' => $activity->points_earned,
                    'notes' => $activity->notes,
                ];
            });

        // Get user's current seasons
        $currentSeasonName = ceil(now()->month / 3);
        $userSeason = $user->seasons()
            ->where('year', now()->year)
            ->where('quarter_number', $currentSeasonName)
            ->first();

        $yearSeason = $user->seasons()
            ->where('year', now()->year)
            ->orderBy('season_year_points', 'desc')
            ->first();

        // Get recent activities (last 7 days) - include both active and inactive habits
        $recentActivities = Activity::where('activities.user_id', $user->id)
            ->join('habits', 'activities.habit_id', '=', 'habits.id')
            ->join('activity_types', 'habits.activity_type_id', '=', 'activity_types.id')
            ->select([
                'activities.id',
                'activities.habit_id',
                'activities.date',
                'activities.points_earned',
                'activities.notes',
                'habits.custom_name',
                'habits.is_active',
                'activity_types.icon as activity_type_icon',
            ])
            ->whereDate('activities.date', '>=', now()->subDays(7))
            ->orderBy('activities.date', 'desc')
            ->orderBy('activities.created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($activity) {
                $isInactive = !$activity->is_active;
                
                return [
                    'id' => $activity->id,
                    'date' => $activity->date->format('M j'),
                    'habit_name' => $activity->custom_name,
                    'icon' => $activity->activity_type_icon,
                    'points' => $activity->points_earned,
                    'notes' => $activity->notes,
                    'is_inactive' => $isInactive,
                ];
            });

        // Get streak tier information
        $streakTier = $user->getStreakTier();

        return [
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name ?: $user->username,
                'avatar' => $user->avatar,
                'current_season_points' => $userSeason?->points ?? 0,
                'current_year_points' => $yearSeason?->season_year_points ?? 0,
                'lifetime_points' => $user->lifetime_points,
                'current_streak' => $user->current_streak,
                'longest_streak' => $user->longest_streak,
                'streak_tier' => [
                    'name' => $streakTier['name'],
                    'multiplier' => $streakTier['multiplier'],
                    'icon' => $streakTier['icon'],
                ],
                'season_rank' => $userSeason ? [
                    'rank' => $userSeason->season_rank,
                    'season' => 'Q' . $currentSeasonName,
                    'year' => now()->year,
                ] : null,
                'year_rank' => $yearSeason ? [
                    'rank' => $yearSeason->year_rank,
                    'year' => now()->year,
                ] : null,
            ],
            'habits' => $habits,
            'today_activities' => $todayActivities,
            'recent_activities' => $recentActivities,
        ];
    }

    /**
     * Get recent activities API endpoint.
     */
    public function getRecentActivities(Request $request)
    {
        $user = $request->user();
        $days = $request->get('days', 7);

        $activities = Activity::where('activities.user_id', $user->id)
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
                'activity_types.icon as activity_type_icon',
            ])
            ->whereDate('activities.date', '>=', now()->subDays($days))
            ->orderBy('activities.date', 'desc')
            ->orderBy('activities.created_at', 'desc')
            ->get()
            ->map(function ($activity) {
                $isInactive = !$activity->is_active;
                
                return [
                    'id' => $activity->id,
                    'date' => $activity->date->format('M j, Y'),
                    'habit_name' => $activity->custom_name,
                    'icon' => $activity->activity_type_icon,
                    'points' => $activity->points_earned,
                    'notes' => $activity->notes,
                    'memory_url' => $activity->memory_url,
                    'is_inactive' => $isInactive,
                ];
            });

        return response()->json(['activities' => $activities]);
    }
}
