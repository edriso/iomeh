<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Get user's current habits with activity type details
        $userHabits = $user->habits()
            ->with('activityType')
            ->orderBy('display_order')
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
                        'name' => $habit->activityType->name,
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
                    'name' => $type->name,
                    'category' => $type->category->value,
                    'base_points' => $type->base_points,
                    'icon' => $type->icon,
                    'description' => $type->description,
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
            'habits.*.activity_type_id' => ['required', 'exists:activity_types,id'],
            'habits.*.custom_name' => ['required', 'string', 'max:100'],
            'habits.*.notes' => ['nullable', 'string', 'max:500'],
        ], [
            'habits.max' => 'You can have a maximum of 15 habits. Please remove some habits before adding new ones.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Get IDs of habits to keep
        $activityTypeIds = collect($validated['habits'])
            ->pluck('activity_type_id')
            ->toArray();

        // Delete habits that are no longer selected
        $user->habits()
            ->whereNotIn('activity_type_id', $activityTypeIds)
            ->delete();

        // Update or create habits
        foreach ($validated['habits'] as $index => $habitData) {
            $user->habits()->updateOrCreate(
                [
                    'activity_type_id' => $habitData['activity_type_id'],
                ],
                [
                    'custom_name' => $habitData['custom_name'],
                    'notes' => $habitData['notes'] ?? null,
                    'display_order' => $index,
                ]
            );
        }

        return back()->with('success', 'Habits updated successfully!');
    }
}
