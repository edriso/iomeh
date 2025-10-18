<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use App\Models\Season;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class RankingsController extends Controller
{
    /**
     * Display the rankings page.
     */
    public function index(Request $request)
    {
        $currentUser = $request->user();
        
        // Cache rankings for 5 minutes to improve performance
        $cacheKey = 'rankings_page_' . $currentUser->id;
        
        $data = Cache::remember($cacheKey, 300, function () use ($currentUser) {
            return [
                'rankings' => [
                    'today' => $this->getTodayRankings(20),
                    'yesterday' => $this->getYesterdayRankings(20),
                    'season' => $this->getCurrentSeasonRankings(20),
                    'year' => $this->getCurrentYearRankings(20),
                ],
                'current_user_rank' => [
                    'today' => $this->getCurrentUserRankToday($currentUser),
                    'yesterday' => $this->getCurrentUserRankYesterday($currentUser),
                    'season' => $this->getCurrentUserRankSeason($currentUser),
                    'year' => $this->getCurrentUserRankYear($currentUser),
                ],
                'user' => [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name ?: $currentUser->username,
                    'username' => $currentUser->username,
                ],
                'current_season' => 'Q' . ceil(now()->month / 3),
                'current_year' => now()->year,
            ];
        });

        return Inertia::render('Rankings', $data);
    }

    /**
     * Get today's rankings API.
     */
    public function getToday(Request $request)
    {
        $limit = $request->get('limit', 50);
        return response()->json([
            'rankings' => $this->getTodayRankings($limit),
        ]);
    }

    /**
     * Get yesterday's rankings API.
     */
    public function getYesterday(Request $request)
    {
        $limit = $request->get('limit', 50);
        return response()->json([
            'rankings' => $this->getYesterdayRankings($limit),
        ]);
    }

    /**
     * Get current season rankings API.
     */
    public function getSeason(Request $request)
    {
        $limit = $request->get('limit', 50);
        return response()->json([
            'rankings' => $this->getCurrentSeasonRankings($limit),
        ]);
    }

    /**
     * Get current year rankings API.
     */
    public function getYear(Request $request)
    {
        $limit = $request->get('limit', 50);
        return response()->json([
            'rankings' => $this->getCurrentYearRankings($limit),
        ]);
    }

    /**
     * Get today's rankings.
     */
    private function getTodayRankings($limit = 20)
    {
        $today = now()->toDateString();
        
        $users = User::whereHas('activities', function ($query) use ($today) {
                $query->whereDate('date', $today);
            })
            ->withSum(['activities as today_points' => function ($query) use ($today) {
                $query->whereDate('date', $today);
            }], 'points_earned')
            ->orderBy('today_points', 'desc')
            ->limit($limit)
            ->get();

        return $users->map(function ($user, $index) {
            return [
                'rank' => $index + 1,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name ?: $user->username,
                    'avatar' => $user->avatar,
                ],
                'points' => $user->today_points ?? 0,
                'activities_count' => $user->activities()
                    ->whereDate('date', now()->toDateString())
                    ->count(),
            ];
        });
    }

    /**
     * Get yesterday's rankings.
     */
    private function getYesterdayRankings($limit = 20)
    {
        $yesterday = now()->subDay()->toDateString();
        
        $users = User::whereHas('activities', function ($query) use ($yesterday) {
                $query->whereDate('date', $yesterday);
            })
            ->withSum(['activities as yesterday_points' => function ($query) use ($yesterday) {
                $query->whereDate('date', $yesterday);
            }], 'points_earned')
            ->orderBy('yesterday_points', 'desc')
            ->limit($limit)
            ->get();

        return $users->map(function ($user, $index) {
            return [
                'rank' => $index + 1,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name ?: $user->username,
                    'avatar' => $user->avatar,
                ],
                'points' => $user->yesterday_points ?? 0,
                'activities_count' => $user->activities()
                    ->whereDate('date', now()->subDay()->toDateString())
                    ->count(),
            ];
        });
    }

    /**
     * Get current season rankings.
     */
    private function getCurrentSeasonRankings($limit = 20)
    {
        $currentYear = now()->year;
        $currentSeasonName = ceil(now()->month / 3);
        
        $seasons = Season::where('year', $currentYear)
            ->where('name', $currentSeasonName)
            ->with('user:id,username,name,avatar')
            ->orderBy('points', 'desc')
            ->limit($limit)
            ->get();

        return $seasons->map(function ($season, $index) {
            return [
                'rank' => $index + 1,
                'user' => [
                    'id' => $season->user->id,
                    'username' => $season->user->username,
                    'name' => $season->user->name ?: $season->user->username,
                    'avatar' => $season->user->avatar,
                ],
                'points' => $season->points,
                'season' => 'Q' . $season->name,
                'year' => $season->year,
            ];
        });
    }

    /**
     * Get current year rankings.
     */
    private function getCurrentYearRankings($limit = 20)
    {
        $currentYear = now()->year;
        
        // Get max season_year_points for each user (from any season in the current year)
        $seasons = Season::where('year', $currentYear)
            ->selectRaw('user_id, MAX(season_year_points) as season_year_points')
            ->groupBy('user_id')
            ->with('user:id,username,name,avatar')
            ->orderBy('season_year_points', 'desc')
            ->limit($limit)
            ->get();

        return $seasons->map(function ($season, $index) {
            return [
                'rank' => $index + 1,
                'user' => [
                    'id' => $season->user->id,
                    'username' => $season->user->username,
                    'name' => $season->user->name ?: $season->user->username,
                    'avatar' => $season->user->avatar,
                ],
                'points' => $season->season_year_points,
                'year' => now()->year,
            ];
        });
    }

    /**
     * Get current user's rank for today.
     */
    private function getCurrentUserRankToday($user)
    {
        $today = now()->toDateString();
        $userPoints = $user->activities()
            ->whereDate('date', $today)
            ->sum('points_earned');

        if ($userPoints == 0) {
            return null;
        }

        $rank = User::whereHas('activities', function ($query) use ($today) {
                $query->whereDate('date', $today);
            })
            ->withSum(['activities as today_points' => function ($query) use ($today) {
                $query->whereDate('date', $today);
            }], 'points_earned')
            ->get()
            ->sortByDesc('today_points')
            ->search(function ($u) use ($user) {
                return $u->id === $user->id;
            }) + 1;

        return [
            'rank' => $rank,
            'points' => $userPoints,
        ];
    }

    /**
     * Get current user's rank for yesterday.
     */
    private function getCurrentUserRankYesterday($user)
    {
        $yesterday = now()->subDay()->toDateString();
        $userPoints = $user->activities()
            ->whereDate('date', $yesterday)
            ->sum('points_earned');

        if ($userPoints == 0) {
            return null;
        }

        $rank = User::whereHas('activities', function ($query) use ($yesterday) {
                $query->whereDate('date', $yesterday);
            })
            ->withSum(['activities as yesterday_points' => function ($query) use ($yesterday) {
                $query->whereDate('date', $yesterday);
            }], 'points_earned')
            ->get()
            ->sortByDesc('yesterday_points')
            ->search(function ($u) use ($user) {
                return $u->id === $user->id;
            }) + 1;

        return [
            'rank' => $rank,
            'points' => $userPoints,
        ];
    }

    /**
     * Get current user's rank for current season.
     */
    private function getCurrentUserRankSeason($user)
    {
        $currentYear = now()->year;
        $currentSeasonName = ceil(now()->month / 3);
        
        $season = $user->seasons()
            ->where('year', $currentYear)
            ->where('name', $currentSeasonName)
            ->first();

        return $season ? [
            'rank' => $season->season_rank,
            'points' => $season->points,
            'season' => 'Q' . $currentSeasonName,
        ] : null;
    }

    /**
     * Get current user's rank for current year.
     */
    private function getCurrentUserRankYear($user)
    {
        $currentYear = now()->year;
        
        $season = $user->seasons()
            ->where('year', $currentYear)
            ->orderBy('season_year_points', 'desc')
            ->first();

        return $season ? [
            'rank' => $season->year_rank,
            'points' => $season->season_year_points,
        ] : null;
    }
}
