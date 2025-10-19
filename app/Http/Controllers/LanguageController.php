<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|in:en,ar',
        ]);

        $locale = $request->input('locale');
        
        // Set the application locale
        App::setLocale($locale);
        
        // Store in session
        Session::put('locale', $locale);
        
        // Update user's locale if authenticated
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        // For Inertia requests, redirect back to the current page
        if ($request->header('X-Inertia')) {
            return redirect()->back();
        }

        // For AJAX requests, return JSON
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'message' => __('common.success')
        ]);
    }

    /**
     * Get current language
     */
    public function current()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'available_locales' => ['en', 'ar']
        ]);
    }
}