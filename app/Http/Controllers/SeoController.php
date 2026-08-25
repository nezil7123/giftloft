<?php

namespace App\Http\Controllers;

use App\Support\EventTemplates;
use App\Support\HelpGuides;
use Illuminate\Http\Response;

/**
 * robots.txt and sitemap.xml are served from here rather than as static files
 * so both can use the configured APP_URL instead of a hard-coded domain.
 */
class SeoController extends Controller
{
    /**
     * Private app areas and share-link pages that must stay out of search results.
     */
    private const DISALLOWED = [
        '/dashboard', '/admin', '/profile', '/events', '/wishlists', '/gifts',
        '/orders', '/cart', '/checkout', '/login', '/register', '/forgot-password',
        '/reset-password', '/verify-email', '/confirm-password',
        '/e/', '/r/', '/people',
    ];

    public function robots(): Response
    {
        $lines = ['User-agent: *'];
        foreach (self::DISALLOWED as $path) {
            $lines[] = "Disallow: {$path}";
        }
        $lines[] = '';
        $lines[] = 'Sitemap: '.url('/sitemap.xml');

        return response(implode("\n", $lines)."\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        // [path, change frequency, priority]
        $urls = [
            ['/', 'weekly', '1.0'],
            ['/templates', 'weekly', '0.9'],
            ['/help', 'monthly', '0.7'],
            ['/privacy', 'yearly', '0.3'],
            ['/terms', 'yearly', '0.3'],
        ];

        foreach (array_keys(HelpGuides::visible()) as $slug) {
            $urls[] = ["/help/{$slug}", 'monthly', '0.6'];
        }

        foreach (EventTemplates::websiteKeys() as $key) {
            $urls[] = ["/templates/website/{$key}", 'monthly', '0.6'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as [$path, $freq, $priority]) {
            $xml .= '  <url>'."\n"
                .'    <loc>'.e(url($path)).'</loc>'."\n"
                .'    <changefreq>'.$freq.'</changefreq>'."\n"
                .'    <priority>'.$priority.'</priority>'."\n"
                .'  </url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
