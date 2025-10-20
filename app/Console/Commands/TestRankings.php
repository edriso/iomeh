<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Http\Controllers\RankingsController;
use Illuminate\Console\Command;
use ReflectionClass;

class TestRankings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:test {--user-id= : Test with specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test rankings consistency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Rankings Consistency...');
        $this->newLine();

        // Get test user
        $userId = $this->option('user-id');
        if ($userId) {
            $testUser = User::find($userId);
            if (!$testUser) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
        } else {
            $testUser = User::whereHas('activities', function ($query) {
                $query->whereDate('date', now()->toDateString());
            })->first();

            if (!$testUser) {
                $this->error('No users with activities today found. Please log some activities first.');
                return 1;
            }
        }

        $this->info("👤 Testing with user: {$testUser->name} (ID: {$testUser->id})");
        $this->newLine();

        // Create controller instance
        $controller = new RankingsController();
        $reflection = new ReflectionClass($controller);

        // Test Today Rankings
        $this->info('📅 TODAY RANKINGS:');
        $this->line('==================');

        // Get user's rank
        $getCurrentUserRankToday = $reflection->getMethod('getCurrentUserRankToday');
        $getCurrentUserRankToday->setAccessible(true);
        $userRankToday = $getCurrentUserRankToday->invoke($controller, $testUser);

        if ($userRankToday) {
            $this->info("✅ User rank: #{$userRankToday['rank']} with {$userRankToday['points']} points");
        } else {
            $this->error('❌ User has no activities today');
        }

        // Get top 20 rankings
        $getTodayRankings = $reflection->getMethod('getTodayRankings');
        $getTodayRankings->setAccessible(true);
        $top20Rankings = $getTodayRankings->invoke($controller, 20);

        $this->line('📊 Top 20 rankings:');
        foreach ($top20Rankings->take(5) as $ranking) {
            $isCurrentUser = $ranking['user']['id'] === $testUser->id;
            $marker = $isCurrentUser ? ' 👤' : '';
            $this->line("  #{$ranking['rank']}: {$ranking['user']['name']} - {$ranking['points']} pts{$marker}");
        }

        // Check if user is in top 20
        $userInTop20 = $top20Rankings->contains(function ($ranking) use ($testUser) {
            return $ranking['user']['id'] === $testUser->id;
        });

        if ($userInTop20) {
            $this->info('✅ User is in top 20 rankings');
        } else {
            $this->warn('⚠️  User is NOT in top 20 rankings');
        }

        // Test rankings with user included
        $getTodayRankingsWithUser = $reflection->getMethod('getTodayRankingsWithUser');
        $getTodayRankingsWithUser->setAccessible(true);
        $rankingsWithUser = $getTodayRankingsWithUser->invoke($controller, 20, $testUser);

        $this->newLine();
        $this->line('📋 Rankings with user included:');
        $userFound = false;
        foreach ($rankingsWithUser as $ranking) {
            $isCurrentUser = $ranking['user']['id'] === $testUser->id;
            if ($isCurrentUser) {
                $userFound = true;
                $this->line("  #{$ranking['rank']}: {$ranking['user']['name']} - {$ranking['points']} pts 👤");
            }
        }

        if ($userFound) {
            $this->info('✅ User found in rankings with correct position');
        } else {
            $this->error('❌ User not found in rankings');
        }

        // Verify consistency
        if ($userRankToday && $userFound) {
            $userRankInList = $rankingsWithUser->first(function ($ranking) use ($testUser) {
                return $ranking['user']['id'] === $testUser->id;
            });
            
            if ($userRankToday['rank'] === $userRankInList['rank']) {
                $this->info('✅ RANKINGS ARE CONSISTENT! User rank matches in both places.');
            } else {
                $this->error("❌ RANKINGS ARE INCONSISTENT! User rank: {$userRankToday['rank']}, In list: {$userRankInList['rank']}");
            }
        }

        // Test Season Rankings
        $this->newLine();
        $this->info('🏆 SEASON RANKINGS:');
        $this->line('==================');

        $getCurrentUserRankSeason = $reflection->getMethod('getCurrentUserRankSeason');
        $getCurrentUserRankSeason->setAccessible(true);
        $userRankSeason = $getCurrentUserRankSeason->invoke($controller, $testUser);

        if ($userRankSeason) {
            $this->info("✅ User season rank: #{$userRankSeason['rank']} with {$userRankSeason['points']} points");
        } else {
            $this->error('❌ User has no season data');
        }

        // Get season rankings
        $getCurrentSeasonRankings = $reflection->getMethod('getCurrentSeasonRankings');
        $getCurrentSeasonRankings->setAccessible(true);
        $seasonRankings = $getCurrentSeasonRankings->invoke($controller, 20);

        $this->line('📊 Top 20 season rankings:');
        foreach ($seasonRankings->take(5) as $ranking) {
            $isCurrentUser = $ranking['user']['id'] === $testUser->id;
            $marker = $isCurrentUser ? ' 👤' : '';
            $this->line("  #{$ranking['rank']}: {$ranking['user']['name']} - {$ranking['points']} pts{$marker}");
        }

        $this->newLine();
        $this->info('✅ Rankings test completed!');

        return 0;
    }
}
