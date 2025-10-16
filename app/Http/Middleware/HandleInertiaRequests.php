<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'username' => $request->user()->username,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'avatar' => $request->user()->avatar,
                    'current_streak' => $request->user()->current_streak ?? 0,
                    'streak_tier' => $request->user()->getStreakTier(),
                    'streak_multiplier' => $request->user()->streak_multiplier ?? 1.0,
                ] : null,
                'habits' => $request->user() 
                    ? $request->user()->habits()
                        ->with('activityType:id,name,icon,category,base_points')
                        ->get()
                        ->map(function ($habit) {
                            return [
                                'id' => $habit->id,
                                'name' => $habit->activityType->name,
                                'icon' => $habit->activityType->icon,
                                'activity_type_id' => $habit->activity_type_id,
                                'base_points' => $habit->activityType->base_points,
                                'category' => $habit->activityType->category,
                            ];
                        })
                    : [],
            ],
            'session' => [
                'expires_at' => $request->session()->get('login_web_' . sha1('web')) 
                    ? time() + (config('session.lifetime', 120) * 60) 
                    : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                    'warning' => $request->session()->get('warning'),
                    'info' => $request->session()->get('info'),
                    'status' => $request->session()->get('status'),
                ];
            },
        ];
    }
}
