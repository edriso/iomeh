<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use App\Models\Ranking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RankingsController extends Controller
{
    /**
     * Display the rankings page.
     */
    public function index(Request $request)
    {
        $currentUser = $request->user();

        return Inertia::render('Rankings', [
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
        ]);
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
        $currentSeason = ceil(now()->month / 3);
        
        $rankings = Ranking::where('year', $currentYear)
            ->where('season', $currentSeason)
            ->with('user:id,username,name,avatar')
            ->orderBy('rank')
            ->limit($limit)
            ->get();

        return $rankings->map(function ($ranking) {
            return [
                'rank' => $ranking->rank,
                'user' => [
                    'id' => $ranking->user->id,
                    'username' => $ranking->user->username,
                    'name' => $ranking->user->name ?: $ranking->user->username,
                    'avatar' => $ranking->user->avatar,
                ],
                'points' => $ranking->points,
                'season' => 'Q' . $ranking->season,
                'year' => $ranking->year,
            ];
        });
    }

    /**
     * Get current year rankings.
     */
    private function getCurrentYearRankings($limit = 20)
    {
        $currentYear = now()->year;
        
        $rankings = Ranking::where('year', $currentYear)
            ->whereNull('season')
            ->with('user:id,username,name,avatar')
            ->orderBy('rank')
            ->limit($limit)
            ->get();

        return $rankings->map(function ($ranking) {
            return [
                'rank' => $ranking->rank,
                'user' => [
                    'id' => $ranking->user->id,
                    'username' => $ranking->user->username,
                    'name' => $ranking->user->name ?: $ranking->user->username,
                    'avatar' => $ranking->user->avatar,
                ],
                'points' => $ranking->points,
                'year' => $ranking->year,
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
        $currentSeason = ceil(now()->month / 3);
        
        $ranking = $user->rankings()
            ->where('year', $currentYear)
            ->where('season', $currentSeason)
            ->first();

        return $ranking ? [
            'rank' => $ranking->rank,
            'points' => $ranking->points,
            'season' => 'Q' . $currentSeason,
        ] : null;
    }

    /**
     * Get current user's rank for current year.
     */
    private function getCurrentUserRankYear($user)
    {
        $currentYear = now()->year;
        
        $ranking = $user->rankings()
            ->where('year', $currentYear)
            ->whereNull('season')
            ->first();

        return $ranking ? [
            'rank' => $ranking->rank,
            'points' => $ranking->points,
        ] : null;
    }
}
