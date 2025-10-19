<?php

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\RankingHistory;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    // Disable activity auto-updates for faster tests
    Activity::$skipAutoUpdate = true;
});

afterEach(function () {
    // Re-enable activity auto-updates
    Activity::$skipAutoUpdate = false;
});

test('user profile page can be viewed', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create([
        'username' => 'testuser',
        'name' => 'Test User',
        'bio' => 'Test bio',
    ]);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('user')
        ->where('user.username', 'testuser')
        ->where('user.name', 'Test User')
        ->where('user.bio', 'Test bio')
    );
});

test('non-existent profile returns 404 page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get(route('profile', ['username' => 'nonexistent']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('errors/404')
        ->has('title')
        ->has('message')
    );
});

test('profile displays current season points correctly', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);
    
    // Create current season
    $currentYear = now()->year;
    $currentSeason = ceil(now()->month / 3);
    
    Season::create([
        'user_id' => $user->id,
        'year' => $currentYear,
        'quarter_number' => $currentSeason,
        'points' => 150,
        'season_year_points' => 150,
    ]);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->where('user.current_season_points', 150)
    );
});

test('profile displays streak information', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create([
        'username' => 'testuser',
        'current_streak' => 5,
        'longest_streak' => 10,
    ]);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->where('user.current_streak', 5)
        ->where('user.longest_streak', 10)
        ->where('stats.current_streak', 5)
        ->where('stats.longest_streak', 10)
    );
});

test('profile displays recent activities', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);
    
    // Create activity type and habit
    $activityType = ActivityType::factory()->create([
        'name' => 'Running',
        'icon' => '🏃',
    ]);
    
    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType->id,
        'custom_name' => 'Morning Run',
    ]);

    // Create activities
    Activity::factory()->create([
        'user_id' => $user->id,
        'habit_id' => $habit->id,
        'date' => now(),
        'points_earned' => 30,
    ]);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('recent_activities', 1)
        ->where('recent_activities.0.custom_name', 'Morning Run')
        ->where('recent_activities.0.points_earned', 30)
    );
});

test('profile displays calendar data', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);
    
    // Create activity types and habits
    $activityType1 = ActivityType::factory()->create();
    $activityType2 = ActivityType::factory()->create();
    
    $habit1 = Habit::factory()->create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType1->id,
    ]);
    
    $habit2 = Habit::factory()->create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType2->id,
    ]);

    // Create activities on specific dates with different habits
    Activity::factory()->create([
        'user_id' => $user->id,
        'habit_id' => $habit1->id,
        'date' => now()->subDays(1),
        'points_earned' => 20,
    ]);

    Activity::factory()->create([
        'user_id' => $user->id,
        'habit_id' => $habit2->id,
        'date' => now()->subDays(1),
        'points_earned' => 30,
    ]);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('calendar_data')
        ->has('calendar_data', 1)
        ->where('calendar_data.0.activities_count', 2)
        ->where('calendar_data.0.points', 50)
    );
});

test('profile displays user habits', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);
    
    // Create activity types and habits
    $activityType1 = ActivityType::factory()->create([
        'name' => 'Running',
        'icon' => '🏃',
    ]);
    
    $activityType2 = ActivityType::factory()->create([
        'name' => 'Meditation',
        'icon' => '🧘',
    ]);
    
    Habit::factory()->create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType1->id,
        'custom_name' => 'Morning Run',
        'display_order' => 1,
    ]);
    
    Habit::factory()->create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType2->id,
        'custom_name' => 'Evening Meditation',
        'display_order' => 2,
    ]);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('habits', 2)
        ->where('habits.0.name', 'Morning Run')
        ->where('habits.1.name', 'Evening Meditation')
    );
});

test('profile displays ranking histories', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);
    
    // Create ranking histories
    RankingHistory::create([
        'user_id' => $user->id,
        'season' => 1,
        'year' => 2024,
        'points' => 500,
        'rank' => 5,
        'season_name' => 'Q1',
        'display_name' => '2024 Q1 #5',
    ]);
    
    RankingHistory::create([
        'user_id' => $user->id,
        'season' => null,
        'year' => 2024,
        'points' => 2000,
        'rank' => 10,
        'season_name' => '2024',
        'display_name' => '2024 #10',
    ]);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->has('ranking_histories', 2)
        ->where('ranking_histories.0.display_name', '2024 Q1 #5')
        ->where('ranking_histories.1.display_name', '2024 #10')
    );
});

test('own profile is flagged correctly', function () {
    $user = User::factory()->create(['username' => 'testuser']);

    // View own profile
    $response = $this->actingAs($user)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->where('is_own_profile', true)
    );

    // View another user's profile
    $otherUser = User::factory()->create(['username' => 'otheruser']);
    $response = $this->actingAs($user)->get(route('profile', ['username' => 'otheruser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->where('is_own_profile', false)
    );
});

test('profile stats are calculated correctly', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);
    
    // Create activity types and habits
    $activityType1 = ActivityType::factory()->create();
    $activityType2 = ActivityType::factory()->create();
    
    $habit1 = Habit::factory()->create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType1->id,
    ]);
    
    $habit2 = Habit::factory()->create([
        'user_id' => $user->id,
        'activity_type_id' => $activityType2->id,
    ]);

    // Create activities on different dates with different habits
    Activity::factory()->create([
        'user_id' => $user->id,
        'habit_id' => $habit1->id,
        'date' => now()->subDays(1),
        'points_earned' => 20,
    ]);

    Activity::factory()->create([
        'user_id' => $user->id,
        'habit_id' => $habit2->id,
        'date' => now()->subDays(1),
        'points_earned' => 30,
    ]);

    Activity::factory()->create([
        'user_id' => $user->id,
        'habit_id' => $habit1->id,
        'date' => now()->subDays(2),
        'points_earned' => 25,
    ]);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->where('stats.total_activities', 3)
        ->where('stats.active_days', 2)
        ->where('stats.habits_count', 2)
    );
});

test('authenticated user can view another user profile', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->where('is_own_profile', false)
    );
});

test('profile handles user with no activities', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->where('stats.total_activities', 0)
        ->where('stats.active_days', 0)
        ->has('calendar_data', 0)
        ->has('recent_activities', 0)
    );
});

test('profile handles user with no season data', function () {
    $viewer = User::factory()->create();
    $user = User::factory()->create(['username' => 'testuser']);

    $response = $this->actingAs($viewer)->get(route('profile', ['username' => 'testuser']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Profile')
        ->where('user.current_season_points', 0)
        ->where('user.current_year_points', 0)
        ->where('stats.current_season_points', 0)
        ->where('stats.current_year_points', 0)
    );
});

