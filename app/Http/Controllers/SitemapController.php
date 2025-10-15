<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Static pages
        $staticPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/rankings', 'priority' => '0.9', 'changefreq' => 'daily'],
        ];
        
        foreach ($staticPages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>' . config('app.url') . $page['url'] . '</loc>';
            $xml .= '<lastmod>' . now()->toISOString() . '</lastmod>';
            $xml .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $page['priority'] . '</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600', // Cache for 1 hour
        ]);
    }
}
