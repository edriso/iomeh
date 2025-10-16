<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Season;
use Illuminate\Database\Seeder;

class RankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seasons are now stored in the seasons table with points and season_year_points
        // Ranks are calculated dynamically when needed
        // This seeder can be used to verify seasons are correct or to recalculate season_year_points if needed
        
        $currentYear = now()->year;
        
        // Recalculate season_year_points for all users in current year
        $users = User::all();
        
        foreach ($users as $user) {
            $yearTotal = Season::where('user_id', $user->id)
                ->where('year', $currentYear)
                ->sum('points');
            
            if ($yearTotal > 0) {
                Season::where('user_id', $user->id)
                    ->where('year', $currentYear)
                    ->update(['season_year_points' => $yearTotal]);
            }
        }

        $this->command->info('Recalculated season_year_points for current year');
    }
}

