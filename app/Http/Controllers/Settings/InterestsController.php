<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InterestsController extends Controller
{
    /**
     * Show the interests management page.
     */
    public function edit()
    {
        $user = Auth::user();

        // Get user's current interests with activity type details
        $userInterests = $user->interests()
            ->with('activityType')
            ->orderBy('display_order')
            ->get()
            ->map(function ($interest) {
                return [
                    'id' => $interest->id,
                    'activity_type_id' => $interest->activity_type_id,
                    'custom_name' => $interest->custom_name,
                    'notes' => $interest->notes,
                    'display_order' => $interest->display_order,
                    'activity_type' => [
                        'id' => $interest->activityType->id,
                        'name' => $interest->activityType->name,
                        'category' => $interest->activityType->category->value,
                        'base_points' => $interest->activityType->base_points,
                        'icon' => $interest->activityType->icon,
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

        return Inertia::render('settings/Interests', [
            'interests' => $userInterests,
            'availableActivityTypes' => $activityTypes,
        ]);
    }

    /**
     * Update user's interests.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'interests' => ['required', 'array', 'min:1'],
            'interests.*.activity_type_id' => ['required', 'exists:activity_types,id'],
            'interests.*.custom_name' => ['required', 'string', 'max:100'],
            'interests.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();

        // Get IDs of interests to keep
        $activityTypeIds = collect($validated['interests'])
            ->pluck('activity_type_id')
            ->toArray();

        // Delete interests that are no longer selected
        $user->interests()
            ->whereNotIn('activity_type_id', $activityTypeIds)
            ->delete();

        // Update or create interests
        foreach ($validated['interests'] as $index => $interestData) {
            $user->interests()->updateOrCreate(
                [
                    'activity_type_id' => $interestData['activity_type_id'],
                ],
                [
                    'custom_name' => $interestData['custom_name'],
                    'notes' => $interestData['notes'] ?? null,
                    'display_order' => $index,
                ]
            );
        }

        return back()->with('success', 'Interests updated successfully!');
    }
}

