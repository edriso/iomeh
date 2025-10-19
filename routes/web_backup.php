<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RankingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// SEO routes
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Home route - Landing for guests, Home for authenticated users
Route::get('/', [HomeController::class, 'home'])->name('home');

// Public routes
Route::get('rankings', [RankingsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('rankings');

// API routes
Route::prefix('api')->middleware(['auth', 'verified'])->group(function () {
    // Rankings API
    Route::get('rankings/today', [RankingsController::class, 'getToday'])->name('api.rankings.today');
    Route::get('rankings/yesterday', [RankingsController::class, 'getYesterday'])->name('api.rankings.yesterday');
    Route::get('rankings/season', [RankingsController::class, 'getSeason'])->name('api.rankings.season');
    Route::get('rankings/year', [RankingsController::class, 'getYear'])->name('api.rankings.year');
    
    // User activities
    Route::get('activities/recent', [HomeController::class, 'getRecentActivities'])->name('api.activities.recent');
    Route::get('activities/date/{date}', [ActivityController::class, 'getByDate'])->name('api.activities.date');
});

// CSRF token refresh endpoint
Route::get('csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->middleware(['web']);

// Language switching routes
Route::post('language/switch', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('language/current', [LanguageController::class, 'current'])->name('language.current');

// Clear user locale preference for testing
Route::get('debug/clear-locale', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $userId = \Illuminate\Support\Facades\Auth::id();
        \App\Models\User::where('id', $userId)->update(['locale' => null]);
        session()->forget('locale');
        session()->forget('language_detected'); // Clear detection flag
        return response()->json([
            'success' => true,
            'message' => 'User locale preference and detection flag cleared. Refresh the page to test first-time auto-detection.',
            'user_id' => $userId,
            'redirect_to' => url('/')
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
})->name('debug.clear.locale');

// Debug route for locale detection (remove in production)
Route::get('debug/locale', function (Illuminate\Http\Request $request) {
    $ip = $request->ip();
    $headers = $request->headers->all();
    $locale = app()->getLocale();
    $session = session('locale');
    $userLocale = \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->locale : null;
    
    // Get real IP detection
    $realIp = $ip;
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
        $proxyHeaders = [
            'X-Forwarded-For' => $request->header('X-Forwarded-For'),
            'X-Real-IP' => $request->header('X-Real-IP'),
            'CF-Connecting-IP' => $request->header('CF-Connecting-IP'),
            'X-Client-IP' => $request->header('X-Client-IP'),
        ];
        
        foreach ($proxyHeaders as $header => $value) {
            if ($value) {
                $realIp = trim(explode(',', $value)[0]);
                break;
            }
        }
    }
    
    // Test IP geolocation
    $countryCode = null;
    try {
        $testIp = $request->get('test_ip', $realIp);
        $response = file_get_contents("http://ip-api.com/line/{$testIp}?fields=countryCode", false, stream_context_create([
            'http' => ['timeout' => 5]
        ]));
        $countryCode = $response ? trim($response) : null;
    } catch (\Exception $e) {
        $countryCode = 'Error: ' . $e->getMessage();
    }
    
    // Browser language detection
    $acceptLanguage = $request->header('Accept-Language');
    $browserLanguages = [];
    if ($acceptLanguage) {
        $parts = explode(',', $acceptLanguage);
        foreach ($parts as $part) {
            $part = trim($part);
            if (strpos($part, ';q=') !== false) {
                [$lang, $quality] = explode(';q=', $part, 2);
                $browserLanguages[trim($lang)] = (float) $quality;
            } else {
                $browserLanguages[trim($part)] = 1.0;
            }
        }
        arsort($browserLanguages);
    }
    
    return response()->json([
        'current_locale' => $locale,
        'session_locale' => $session,
        'user_locale' => $userLocale,
        'is_authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'ip_info' => [
            'detected_ip' => $ip,
            'real_ip' => $realIp,
            'country_code' => $countryCode,
            'test_url' => url("/debug/locale?test_ip=154.180.211.69"), // Example Arabic country IP
        ],
        'browser_info' => [
            'accept_language' => $acceptLanguage,
            'parsed_languages' => $browserLanguages,
            'user_agent' => $request->userAgent(),
        ],
        'headers' => [
            'X-Forwarded-For' => $request->header('X-Forwarded-For'),
            'X-Real-IP' => $request->header('X-Real-IP'),
            'CF-Connecting-IP' => $request->header('CF-Connecting-IP'),
            'X-Client-IP' => $request->header('X-Client-IP'),
        ],
        'test_urls' => [
            'force_arabic_ip' => url("/debug/locale?test_ip=156.252.110.1"), // Morocco IP
            'force_saudi_ip' => url("/debug/locale?test_ip=188.161.175.200"), // Saudi Arabia IP
            'simulate_real_ip' => url("/?simulate_real_ip=1"),
            'logout_first' => url("/logout"),
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.locale');

// Test route for anonymous users (completely bypass auth)
Route::get('debug/locale-anonymous', function (Illuminate\Http\Request $request) {
    // Temporarily clear session and auth to test pure detection
    session()->forget('locale');
    session()->forget('login_web_' . sha1('web'));
    
    $ip = $request->ip();
    $locale = app()->getLocale();
    
    // Test IP geolocation for Arabic countries
    $testIPs = [
        'morocco' => '156.252.110.1',
        'saudi' => '188.161.175.200', 
        'egypt' => '197.62.154.1',
        'uae' => '78.110.96.1'
    ];
    
    $results = [];
    foreach ($testIPs as $country => $testIp) {
        try {
            $response = file_get_contents("http://ip-api.com/line/{$testIp}?fields=countryCode", false, stream_context_create([
                'http' => ['timeout' => 5]
            ]));
            $results[$country] = [
                'ip' => $testIp,
                'country_code' => $response ? trim($response) : 'Failed'
            ];
        } catch (\Exception $e) {
            $results[$country] = [
                'ip' => $testIp,
                'country_code' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    return response()->json([
        'message' => 'Testing anonymous locale detection',
        'current_ip' => $ip,
        'current_locale' => $locale,
        'session_cleared' => true,
        'arabic_country_tests' => $results,
        'test_with_arabic_ip' => url("/debug/locale-anonymous?force_ip=156.252.110.1"),
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.locale.anonymous');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile routes
    Route::get('profile/{username}', [ProfileController::class, 'show'])->name('profile');
    
    // Activity routes
    Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::patch('activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
});

// Include other route files
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Fallback route for 404s - must be last
Route::fallback(function () {
    return Inertia::render('errors/404');
});
