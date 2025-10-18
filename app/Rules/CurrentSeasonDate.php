<?php

namespace App\Rules;

use App\Models\Season;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class CurrentSeasonDate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $date = Carbon::parse($value);
        } catch (\Exception $e) {
            $fail('The :attribute must be a valid date.');
            return;
        }

        if (!Season::isDateInCurrentSeason($date)) {
            $seasonDates = Season::getCurrentSeasonDates();
            $seasonName = 'Q' . $seasonDates['season'];
            $startDate = $seasonDates['start']->format('M j, Y');
            $endDate = $seasonDates['end']->format('M j, Y');
            
            $fail("The :attribute must be within the current season ({$seasonName}: {$startDate} - {$endDate}).");
        }
    }
}
