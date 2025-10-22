<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class HabitsController extends Controller
{
    /**
     * Show the habits management page.
     */
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();

        // Get user's active habits with activity type details
        $userHabits = $user->activeHabits()
            ->with('activityType')
            ->get()
            ->map(function ($habit) {
                return [
                    'id' => $habit->id,
                    'activity_type_id' => $habit->activity_type_id,
                    'custom_name' => $habit->custom_name,
                    'notes' => $habit->notes,
                    'display_order' => $habit->display_order,
                    'activity_type' => [
                        'id' => $habit->activityType->id,
                        'name' => $habit->activityType->translated_name,
                        'category' => $habit->activityType->category->value,
                        'base_points' => $habit->activityType->base_points,
                        'icon' => $habit->activityType->icon,
                    ],
                ];
            });

        // Get all available activity types grouped by category
        $activityTypes = ActivityType::active()
            ->ordered()
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->translated_name,
                    'category' => $type->category->value,
                    'base_points' => $type->base_points,
                    'icon' => $type->icon,
                    'description' => $type->translated_description,
                ];
            })
            ->groupBy('category');

        return Inertia::render('settings/Habits', [
            'habits' => $userHabits,
            'availableActivityTypes' => $activityTypes,
        ]);
    }

    /**
     * Update user's habits.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'habits' => ['required', 'array', 'min:1', 'max:15'],
            'habits.*.id' => ['nullable', 'integer', 'exists:habits,id'],
            'habits.*.activity_type_id' => ['nullable', 'exists:activity_types,id'],
            'habits.*.custom_name' => ['required', 'string', 'max:100'],
            'habits.*.custom_icon' => ['nullable', 'string', 'max:50'],
            'habits.*.notes' => ['nullable', 'string', 'max:500'],
        ], [
            'habits.required' => __('validation.required'),
            'habits.max' => __('habits.maximum_reached'),
            'habits.*.id.exists' => __('validation.exists'),
            'habits.*.activity_type_id.exists' => __('validation.in'),
            'habits.*.custom_name.required' => __('validation.required'),
            'habits.*.custom_name.max' => __('validation.max.string', ['max' => 100]),
            'habits.*.custom_icon.max' => __('validation.max.string', ['max' => 50]),
            'habits.*.notes.max' => __('validation.max.string', ['max' => 500]),
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Get current active habits with their activity types for comparison
        $currentHabits = $user->activeHabits()->with('activityType')->get();
        $newHabitIds = collect($validated['habits'])->pluck('id')->filter()->toArray();
        
        // Track activity type mappings for transferring activities
        $activityTypeToOldHabitMap = [];
        foreach ($currentHabits as $habit) {
            $activityTypeToOldHabitMap[$habit->activity_type_id] = $habit->id;
        }
        
        // Track new habit mappings
        $activityTypeToNewHabitMap = [];
        
        // Soft delete habits that are not in the new list
        foreach ($currentHabits as $habit) {
            if (!in_array($habit->id, $newHabitIds)) {
                $habit->softDelete();
            }
        }

        // Update or create habits
        foreach ($validated['habits'] as $index => $habitData) {
            $newHabit = null;
            
            if (isset($habitData['id']) && $habitData['id']) {
                // Update existing habit
                $habit = $user->habits()->find($habitData['id']);
                if ($habit) {
                    $habit->update([
                        'activity_type_id' => $habitData['activity_type_id'],
                        'custom_name' => $habitData['custom_name'],
                        'custom_icon' => $habitData['custom_icon'] ?? null,
                        'notes' => $habitData['notes'] ?? null,
                        'display_order' => $index,
                        'is_active' => true, // Ensure it's active
                    ]);
                    $newHabit = $habit;
                }
            } else {
                // Create new habit
                $newHabit = $user->habits()->create([
                    'activity_type_id' => $habitData['activity_type_id'],
                    'custom_name' => $habitData['custom_name'],
                    'custom_icon' => $habitData['custom_icon'] ?? null,
                    'notes' => $habitData['notes'] ?? null,
                    'display_order' => $index,
                    'is_active' => true,
                ]);
            }
            
            // Track the new habit for this activity type
            if ($newHabit) {
                $activityTypeToNewHabitMap[$habitData['activity_type_id']] = $newHabit->id;
            }
        }

        // Transfer activities from old habits to new habits for the same activity types
        foreach ($activityTypeToOldHabitMap as $activityTypeId => $oldHabitId) {
            if (isset($activityTypeToNewHabitMap[$activityTypeId])) {
                $newHabitId = $activityTypeToNewHabitMap[$activityTypeId];
                
                // Only transfer if it's a different habit (avoid self-update)
                if ($oldHabitId !== $newHabitId) {
                    // Update activities from old habit to new habit
                    Activity::where('user_id', $user->id)
                        ->where('habit_id', $oldHabitId)
                        ->update(['habit_id' => $newHabitId]);
                }
            }
        }

        // Clear the home page cache for this user
        $cacheKey = "home_data_user_{$user->id}";
        Cache::forget($cacheKey);

        return back()->with('success', __('success.habits_updated'));
    }
}
