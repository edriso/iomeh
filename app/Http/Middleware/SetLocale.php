<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Arabic countries list (ISO 3166-1 alpha-2 codes)
        $arabicCountries = [
            // Middle East
            'SA', 'AE', 'EG', 'JO', 'LB', 'SY', 'IQ', 'KW', 'QA', 'BH', 'OM', 'YE', 'PS',
            // North Africa
            'LY', 'TN', 'DZ', 'MA', 'SD', 'SO', 'DJ', 'KM', 'MR',
            // Additional countries with significant Arabic-speaking populations
            'TR', 'IR', 'AF', 'PK', 'BD', 'IN', 'ET', 'KE', 'TZ', 'UG', 'SS', 'TD', 'NE', 'ML', 'BF', 'SN', 'GM', 'GN', 'SL', 'LR', 'CI', 'GH', 'TG', 'BJ', 'NG', 'CM', 'CF'
        ];

        // Check if user has manually set a language preference
        $userLocale = null;
        if (Auth::check()) {
            $userLocale = Auth::user()->locale;
        }

        // Check session for language preference
        $sessionLocale = Session::get('locale');

        // Determine locale priority:
        // 1. User's manual preference (if authenticated)
        // 2. Session preference
        // 3. IP-based detection (for new users)
        // 4. Default to English

        if ($userLocale) {
            $locale = $userLocale;
        } elseif ($sessionLocale) {
            $locale = $sessionLocale;
        } else {
            // Auto-detect based on IP for new users
            $locale = $this->detectLocaleFromIP($request, $arabicCountries);
        }

        // Set the application locale
        App::setLocale($locale);

        // Store in session for consistency
        Session::put('locale', $locale);
        
        // Log for debugging (remove in production)
        // Log::info("SetLocale middleware: IP={$request->ip()}, Locale={$locale}, Session={$sessionLocale}, User={$userLocale}");

        return $next($request);
    }

    /**
     * Detect locale from user's IP address
     */
    private function detectLocaleFromIP(Request $request, array $arabicCountries): string
    {
        try {
            // Get user's IP
            $ip = $request->ip();
            
            // Log::info("detectLocaleFromIP: Original IP={$ip}, simulate_real_ip={$request->has('simulate_real_ip')}");
            
            // For local development, try to get real IP from headers or allow testing
            if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
                // Try to get real IP from common proxy headers
                $realIp = $request->header('X-Forwarded-For') ?: 
                         $request->header('X-Real-IP') ?: 
                         $request->header('CF-Connecting-IP') ?: 
                         $request->header('X-Client-IP');
                
                if ($realIp) {
                    // Get first IP if multiple (comma-separated)
                    $ip = trim(explode(',', $realIp)[0]);
                } else {
                    // Skip detection for local development (unless testing with specific IP or force_detect)
                    if (!$request->has('test_ip') && !$request->has('force_detect') && !$request->has('simulate_real_ip')) {
                        // Log::info("detectLocaleFromIP: Skipping detection for local IP");
                        return 'en';
                    }
                }
            }
            
            // Allow testing with specific IP via query parameter
            if ($request->has('test_ip')) {
                $ip = $request->get('test_ip');
            }
            
            // For testing: use real IP if simulate_real_ip parameter is present
            if ($request->has('simulate_real_ip')) {
                $ip = '154.180.211.69'; // Your real IP for testing
                // Log::info("detectLocaleFromIP: Using simulated IP={$ip}");
            }

            // Check cache first (24 hour cache)
            $cacheKey = "ip_country_{$ip}";
            $countryCode = Cache::remember($cacheKey, 86400, function () use ($ip) {
                return $this->getCountryFromIP($ip);
            });
            
            if ($countryCode && in_array($countryCode, $arabicCountries)) {
                return 'ar';
            }
        } catch (\Exception $e) {
            // If IP detection fails, default to English
            Log::info('IP detection failed: ' . $e->getMessage());
        }

        return 'en';
    }

    /**
     * Get country code from IP address using multiple services for better reliability
     */
    private function getCountryFromIP(string $ip): ?string
    {
        // List of free IP geolocation services (with fallbacks)
        $services = [
            "http://ip-api.com/line/{$ip}?fields=countryCode",
            "http://ipapi.co/{$ip}/country/",
            "https://ipapi.co/{$ip}/country/"
        ];

        foreach ($services as $service) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 3, // 3 second timeout
                        'user_agent' => 'IOMeH/1.0',
                        'method' => 'GET',
                        'header' => 'Accept: text/plain'
                    ]
                ]);
                
                $response = file_get_contents($service, false, $context);
                
                if ($response) {
                    $countryCode = strtoupper(trim($response));
                    
                    // Skip HTML responses (common with some services)
                    if (strpos($countryCode, '<') !== false || strpos($countryCode, 'DOCTYPE') !== false) {
                        continue;
                    }
                    
                    // Validate country code (should be 2 characters and alphabetic)
                    if (strlen($countryCode) === 2 && ctype_alpha($countryCode) && $countryCode !== 'XX') {
                        return $countryCode;
                    }
                }
            } catch (\Exception $e) {
                Log::info("IP geolocation service failed ({$service}): " . $e->getMessage());
                continue; // Try next service
            }
        }

        return null;
    }
}