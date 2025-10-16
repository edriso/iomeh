<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return Inertia::render('Landing', ['stats' => $stats]);
    }

    /**
     * Display the authenticated home page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get user's interests with activity types
        $interests = $user->interests()
            ->with('activityType')
            ->orderBy('display_order')
            ->get()
            ->map(function ($interest) {
                return [
                    'id' => $interest->id,
                    'name' => $interest->custom_name,
                    'icon' => $interest->activityType->icon,
                    'category' => $interest->activityType->category->value,
                    'activity_type_id' => $interest->activity_type_id,
                    'base_points' => $interest->activityType->base_points,
                    'has_activity_today' => $interest->hasActivityToday(),
                ];
            });

        // Get today's activities
        $todayActivities = $user->getTodayActivities()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'interest_name' => $activity->interest->custom_name,
                    'points_earned' => $activity->points_earned,
                    'notes' => $activity->notes,
                    'created_at' => $activity->created_at->format('g:i A'),
                ];
            });

        // Get user's current seasons
        $currentSeasonName = ceil(now()->month / 3);
        $userSeason = $user->seasons()
            ->where('year', now()->year)
            ->where('name', $currentSeasonName)
            ->first();

        $yearSeason = $user->seasons()
            ->where('year', now()->year)
            ->orderBy('season_year_points', 'desc')
            ->first();

        // Get recent activities (last 7 days)
        $recentActivities = Activity::where('user_id', $user->id)
            ->with(['interest.activityType'])
            ->whereDate('date', '>=', now()->subDays(7))
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'date' => $activity->date->format('M j'),
                    'interest_name' => $activity->interest->custom_name,
                    'icon' => $activity->interest->activityType->icon,
                    'points' => $activity->points_earned,
                    'notes' => $activity->notes,
                ];
            });

        return Inertia::render('Home', [
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
            'interests' => $interests,
            'today_activities' => $todayActivities,
            'recent_activities' => $recentActivities,
        ]);
    }

    /**
     * Get recent activities API endpoint.
     */
    public function getRecentActivities(Request $request)
    {
        $user = $request->user();
        $days = $request->get('days', 7);

        $activities = Activity::where('user_id', $user->id)
            ->with(['interest.activityType'])
            ->whereDate('date', '>=', now()->subDays($days))
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'date' => $activity->date->format('M j, Y'),
                    'interest_name' => $activity->interest->custom_name,
                    'icon' => $activity->interest->activityType->icon,
                    'points' => $activity->points_earned,
                    'notes' => $activity->notes,
                    'proof_url' => $activity->proof_url,
                ];
            });

        return response()->json(['activities' => $activities]);
    }
}
