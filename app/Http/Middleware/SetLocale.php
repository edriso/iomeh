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
        
        // Check if language has already been detected (first visit detection)
        $hasDetectedLanguage = Session::get('language_detected', false);

        // Determine locale priority:
        // 1. User's manual preference (if authenticated AND explicitly set)
        // 2. Session preference 
        // 3. IP-based detection (ONLY for first-time visitors)
        // 4. Browser language detection (ONLY for first-time visitors)
        // 5. Default to English

        if ($userLocale && $userLocale !== 'auto') {
            // Only use user preference if it's explicitly set (not null and not 'auto')
            $locale = $userLocale;
            Log::info("SetLocale: Using user preference: {$locale}");
        } elseif ($sessionLocale && $sessionLocale !== 'auto') {
            $locale = $sessionLocale;
            Log::info("SetLocale: Using session preference: {$locale}");
        } elseif (!$hasDetectedLanguage) {
            // Only detect language for first-time visitors
            $detectedLocale = $this->detectLocale($request, $arabicCountries);
            $locale = $detectedLocale;
            
            // Mark that language has been detected to prevent future auto-detection
            Session::put('language_detected', true);
            
            Log::info("SetLocale: First-time auto-detected locale: {$locale} (IP: {$request->ip()})");
        } else {
            // For returning visitors who haven't set a preference, default to English
            $locale = 'en';
            Log::info("SetLocale: Returning visitor, using default English");
        }

        // Set the application locale
        App::setLocale($locale);

        // Store in session for consistency
        Session::put('locale', $locale);
        
        // Log for debugging (always log for now to debug)
        Log::info("SetLocale middleware: IP={$request->ip()}, Final Locale={$locale}, Session={$sessionLocale}, User={$userLocale}");

        return $next($request);
    }

    /**
     * Detect locale using multiple methods (IP + Browser)
     */
    private function detectLocale(Request $request, array $arabicCountries): string
    {
        // First try IP-based detection
        $ipLocale = $this->detectLocaleFromIP($request, $arabicCountries);
        if ($ipLocale === 'ar') {
            Log::info("DetectLocale: IP detection returned Arabic");
            return 'ar';
        }

        // Fallback to browser language detection
        $browserLocale = $this->detectLocaleFromBrowser($request);
        if ($browserLocale === 'ar') {
            Log::info("DetectLocale: Browser detection returned Arabic");
            return 'ar';
        }

        Log::info("DetectLocale: No Arabic locale detected, defaulting to English");
        return 'en';
    }

    /**
     * Detect locale from browser Accept-Language header
     */
    private function detectLocaleFromBrowser(Request $request): string
    {
        try {
            $acceptLanguage = $request->header('Accept-Language');
            if (!$acceptLanguage) {
                return 'en';
            }

            // Parse Accept-Language header
            // Format: "ar-SA,ar;q=0.9,en-US;q=0.8,en;q=0.7"
            $languages = [];
            $parts = explode(',', $acceptLanguage);
            
            foreach ($parts as $part) {
                $part = trim($part);
                if (strpos($part, ';q=') !== false) {
                    [$lang, $quality] = explode(';q=', $part, 2);
                    $languages[trim($lang)] = (float) $quality;
                } else {
                    $languages[trim($part)] = 1.0;
                }
            }

            // Sort by quality (preference)
            arsort($languages);

            // Check for Arabic languages
            foreach (array_keys($languages) as $lang) {
                $lang = strtolower($lang);
                
                // Check for Arabic language codes
                if (str_starts_with($lang, 'ar') || in_array($lang, ['ar-sa', 'ar-eg', 'ar-ae', 'ar-jo', 'ar-lb', 'ar-sy', 'ar-iq', 'ar-kw', 'ar-qa', 'ar-bh', 'ar-om', 'ar-ye', 'ar-ly', 'ar-tn', 'ar-dz', 'ar-ma', 'ar-sd'])) {
                    Log::info("DetectLocaleFromBrowser: Found Arabic language: {$lang}");
                    return 'ar';
                }
            }

            Log::info("DetectLocaleFromBrowser: No Arabic found in: {$acceptLanguage}");
        } catch (\Exception $e) {
            Log::error("Browser language detection failed: " . $e->getMessage());
        }

        return 'en';
    }

    /**
     * Detect locale from user's IP address
     */
    private function detectLocaleFromIP(Request $request, array $arabicCountries): string
    {
        try {
            // Get user's IP with better proxy detection
            $ip = $this->getRealClientIP($request);
            
            Log::info("detectLocaleFromIP: Detected IP={$ip}");
            
            // For local development, allow simulation for testing
            if ($this->isLocalIP($ip)) {
                if ($request->has('test_ip')) {
                    $ip = $request->get('test_ip');
                    Log::info("detectLocaleFromIP: Using test IP={$ip}");
                } elseif ($request->has('simulate_real_ip')) {
                    $ip = '154.180.211.69'; // Example real IP for testing
                    Log::info("detectLocaleFromIP: Using simulated IP={$ip}");
                } elseif (!$request->has('force_detect')) {
                    Log::info("detectLocaleFromIP: Skipping detection for local IP");
                    return 'en';
                }
            }

            // Check cache first (24 hour cache)
            $cacheKey = "ip_country_{$ip}";
            $countryCode = Cache::remember($cacheKey, 86400, function () use ($ip) {
                return $this->getCountryFromIP($ip);
            });
            
            Log::info("detectLocaleFromIP: Country code for IP {$ip}: {$countryCode}");
            
            if ($countryCode && in_array($countryCode, $arabicCountries)) {
                Log::info("detectLocaleFromIP: Arabic country detected: {$countryCode}");
                return 'ar';
            }
        } catch (\Exception $e) {
            Log::error('IP detection failed: ' . $e->getMessage());
        }

        return 'en';
    }

    /**
     * Get real client IP with better proxy detection
     */
    private function getRealClientIP(Request $request): string
    {
        $ip = $request->ip();
        
        // If it's a local IP, try to get real IP from headers
        if ($this->isLocalIP($ip)) {
            $headers = [
                'X-Forwarded-For',
                'X-Real-IP', 
                'CF-Connecting-IP',
                'X-Client-IP',
                'X-Cluster-Client-IP',
                'Forwarded'
            ];
            
            foreach ($headers as $header) {
                $headerValue = $request->header($header);
                if ($headerValue) {
                    // Handle comma-separated IPs (get first one)
                    $realIp = trim(explode(',', $headerValue)[0]);
                    
                    // Validate IP format and ensure it's not local
                    if (filter_var($realIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        Log::info("getRealClientIP: Found real IP in {$header}: {$realIp}");
                        return $realIp;
                    }
                }
            }
        }
        
        return $ip;
    }

    /**
     * Check if IP is local/private
     */
    private function isLocalIP(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost']) || 
               !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Get country code from IP address using multiple services for better reliability
     */
    private function getCountryFromIP(string $ip): ?string
    {
        // List of IP geolocation services with fallbacks
        $services = [
            // Free services with good reliability
            [
                'url' => "http://ip-api.com/line/{$ip}?fields=countryCode",
                'timeout' => 5,
            ],
            [
                'url' => "https://ipapi.co/{$ip}/country/",
                'timeout' => 5,
            ],
            [
                'url' => "http://ipinfo.io/{$ip}/country",
                'timeout' => 5,
            ],
            [
                'url' => "https://ipwhois.app/json/{$ip}?fields=country_code",
                'timeout' => 5,
                'parse_json' => true,
                'field' => 'country_code'
            ]
        ];

        foreach ($services as $index => $service) {
            try {
                Log::info("getCountryFromIP: Trying service " . ($index + 1) . ": {$service['url']}");
                
                $context = stream_context_create([
                    'http' => [
                        'timeout' => $service['timeout'],
                        'user_agent' => 'IOMeH/1.0 (Fitness App)',
                        'method' => 'GET',
                        'header' => [
                            'Accept: text/plain, application/json',
                            'Connection: close'
                        ]
                    ]
                ]);
                
                $response = file_get_contents($service['url'], false, $context);
                
                if ($response === false) {
                    Log::warning("getCountryFromIP: Service " . ($index + 1) . " returned false");
                    continue;
                }
                
                // Handle JSON responses
                if (!empty($service['parse_json'])) {
                    $data = json_decode($response, true);
                    if ($data && isset($data[$service['field']])) {
                        $countryCode = strtoupper(trim($data[$service['field']]));
                    } else {
                        Log::warning("getCountryFromIP: Service " . ($index + 1) . " JSON parse failed or missing field");
                        continue;
                    }
                } else {
                    $countryCode = strtoupper(trim($response));
                }
                
                // Skip HTML responses or error messages
                if (strpos($countryCode, '<') !== false || 
                    strpos($countryCode, 'DOCTYPE') !== false ||
                    strpos($countryCode, 'ERROR') !== false) {
                    Log::warning("getCountryFromIP: Service " . ($index + 1) . " returned HTML/error: " . substr($countryCode, 0, 100));
                    continue;
                }
                
                // Validate country code (should be 2 characters and alphabetic)
                if (strlen($countryCode) === 2 && ctype_alpha($countryCode) && $countryCode !== 'XX') {
                    Log::info("getCountryFromIP: Service " . ($index + 1) . " returned valid country code: {$countryCode}");
                    return $countryCode;
                } else {
                    Log::warning("getCountryFromIP: Service " . ($index + 1) . " returned invalid country code: {$countryCode}");
                }
                
            } catch (\Exception $e) {
                Log::warning("getCountryFromIP: Service " . ($index + 1) . " failed: " . $e->getMessage());
                continue;
            }
        }

        Log::error("getCountryFromIP: All services failed for IP: {$ip}");
        return null;
    }
}