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
                    'today' => $this->getTodayRankingsWithUser(20, $currentUser),
                    'yesterday' => $this->getYesterdayRankingsWithUser(20, $currentUser),
                    'season' => $this->getCurrentSeasonRankingsWithUser(20, $currentUser),
                    'year' => $this->getCurrentYearRankingsWithUser(20, $currentUser),
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

        $rankings = $users->map(function ($user, $index) {
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

        return $rankings;
    }

    /**
     * Get today's rankings including current user if not in top list.
     */
    private function getTodayRankingsWithUser($limit, $currentUser)
    {
        $rankings = $this->getTodayRankings($limit);
        
        // Check if current user is already in the rankings
        $userInRankings = $rankings->contains(function ($ranking) use ($currentUser) {
            return $ranking['user']['id'] === $currentUser->id;
        });
        
        // If user is not in top rankings but has activities today, add them
        if (!$userInRankings) {
            $today = now()->toDateString();
            $userPoints = $currentUser->activities()
                ->whereDate('date', $today)
                ->sum('points_earned');
                
            if ($userPoints > 0) {
                $userRank = $this->getCurrentUserRankToday($currentUser);
                if ($userRank) {
                    $rankings->push([
                        'rank' => $userRank['rank'],
                        'user' => [
                            'id' => $currentUser->id,
                            'username' => $currentUser->username,
                            'name' => $currentUser->name ?: $currentUser->username,
                            'avatar' => $currentUser->avatar,
                        ],
                        'points' => $userPoints,
                        'activities_count' => $currentUser->activities()
                            ->whereDate('date', $today)
                            ->count(),
                    ]);
                }
            }
        }
        
        return $rankings;
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
     * Get yesterday's rankings including current user if not in top list.
     */
    private function getYesterdayRankingsWithUser($limit, $currentUser)
    {
        $rankings = $this->getYesterdayRankings($limit);
        
        // Check if current user is already in the rankings
        $userInRankings = $rankings->contains(function ($ranking) use ($currentUser) {
            return $ranking['user']['id'] === $currentUser->id;
        });
        
        // If user is not in top rankings but has activities yesterday, add them
        if (!$userInRankings) {
            $yesterday = now()->subDay()->toDateString();
            $userPoints = $currentUser->activities()
                ->whereDate('date', $yesterday)
                ->sum('points_earned');
                
            if ($userPoints > 0) {
                $userRank = $this->getCurrentUserRankYesterday($currentUser);
                if ($userRank) {
                    $rankings->push([
                        'rank' => $userRank['rank'],
                        'user' => [
                            'id' => $currentUser->id,
                            'username' => $currentUser->username,
                            'name' => $currentUser->name ?: $currentUser->username,
                            'avatar' => $currentUser->avatar,
                        ],
                        'points' => $userPoints,
                        'activities_count' => $currentUser->activities()
                            ->whereDate('date', $yesterday)
                            ->count(),
                    ]);
                }
            }
        }
        
        return $rankings;
    }

    /**
     * Get current season rankings.
     */
    private function getCurrentSeasonRankings($limit = 20)
    {
        $currentYear = now()->year;
        $currentSeasonName = ceil(now()->month / 3);
        
        $seasons = Season::where('year', $currentYear)
            ->where('quarter_number', $currentSeasonName)
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
                'season' => 'Q' . $season->quarter_number,
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
     * Get current season rankings including current user if not in top list.
     */
    private function getCurrentSeasonRankingsWithUser($limit, $currentUser)
    {
        $rankings = $this->getCurrentSeasonRankings($limit);
        
        // Check if current user is already in the rankings
        $userInRankings = $rankings->contains(function ($ranking) use ($currentUser) {
            return $ranking['user']['id'] === $currentUser->id;
        });
        
        // If user is not in top rankings but has season data, add them
        if (!$userInRankings) {
            $userRank = $this->getCurrentUserRankSeason($currentUser);
            if ($userRank) {
                $rankings->push([
                    'rank' => $userRank['rank'],
                    'user' => [
                        'id' => $currentUser->id,
                        'username' => $currentUser->username,
                        'name' => $currentUser->name ?: $currentUser->username,
                        'avatar' => $currentUser->avatar,
                    ],
                    'points' => $userRank['points'],
                    'season' => $userRank['season'],
                    'year' => now()->year,
                ]);
            }
        }
        
        return $rankings;
    }

    /**
     * Get current year rankings including current user if not in top list.
     */
    private function getCurrentYearRankingsWithUser($limit, $currentUser)
    {
        $rankings = $this->getCurrentYearRankings($limit);
        
        // Check if current user is already in the rankings
        $userInRankings = $rankings->contains(function ($ranking) use ($currentUser) {
            return $ranking['user']['id'] === $currentUser->id;
        });
        
        // If user is not in top rankings but has year data, add them
        if (!$userInRankings) {
            $userRank = $this->getCurrentUserRankYear($currentUser);
            if ($userRank) {
                $rankings->push([
                    'rank' => $userRank['rank'],
                    'user' => [
                        'id' => $currentUser->id,
                        'username' => $currentUser->username,
                        'name' => $currentUser->name ?: $currentUser->username,
                        'avatar' => $currentUser->avatar,
                    ],
                    'points' => $userRank['points'],
                    'year' => now()->year,
                ]);
            }
        }
        
        return $rankings;
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

        // Count users with more points than current user
        $usersWithMorePoints = User::whereHas('activities', function ($query) use ($today) {
                $query->whereDate('date', $today);
            })
            ->withSum(['activities as today_points' => function ($query) use ($today) {
                $query->whereDate('date', $today);
            }], 'points_earned')
            ->get()
            ->filter(function ($u) use ($userPoints) {
                return ($u->today_points ?? 0) > $userPoints;
            })
            ->count();

        $rank = $usersWithMorePoints + 1;

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

        // Count users with more points than current user
        $usersWithMorePoints = User::whereHas('activities', function ($query) use ($yesterday) {
                $query->whereDate('date', $yesterday);
            })
            ->withSum(['activities as yesterday_points' => function ($query) use ($yesterday) {
                $query->whereDate('date', $yesterday);
            }], 'points_earned')
            ->get()
            ->filter(function ($u) use ($userPoints) {
                return ($u->yesterday_points ?? 0) > $userPoints;
            })
            ->count();

        $rank = $usersWithMorePoints + 1;

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
            ->where('quarter_number', $currentSeasonName)
            ->first();

        if (!$season) {
            return null;
        }

        // Count users with more points in the same season
        $usersWithMorePoints = Season::where('year', $currentYear)
            ->where('quarter_number', $currentSeasonName)
            ->where('points', '>', $season->points)
            ->count();

        $rank = $usersWithMorePoints + 1;

        return [
            'rank' => $rank,
            'points' => $season->points,
            'season' => 'Q' . $currentSeasonName,
        ];
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

        if (!$season) {
            return null;
        }

        // Count users with more season_year_points in the same year
        $usersWithMorePoints = Season::where('year', $currentYear)
            ->where('season_year_points', '>', $season->season_year_points)
            ->count();

        $rank = $usersWithMorePoints + 1;

        return [
            'rank' => $rank,
            'points' => $season->season_year_points,
        ];
    }
}
