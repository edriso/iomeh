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

