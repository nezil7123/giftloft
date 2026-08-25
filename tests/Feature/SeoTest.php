<?php

namespace Tests\Feature;

use App\Support\EventTemplates;
use App\Support\HelpGuides;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_is_served_and_blocks_private_areas(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *', false);

        // Private app areas and share-link pages stay out of search results.
        foreach (['/dashboard', '/admin', '/orders', '/cart', '/e/', '/r/'] as $path) {
            $response->assertSee("Disallow: {$path}", false);
        }

        $response->assertSee('Sitemap: '.url('/sitemap.xml'), false);
    }

    public function test_sitemap_lists_public_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        foreach (['/', '/templates', '/help', '/privacy', '/terms'] as $path) {
            $response->assertSee('<loc>'.url($path).'</loc>', false);
        }
    }

    public function test_sitemap_includes_every_website_template(): void
    {
        $response = $this->get('/sitemap.xml');

        foreach (EventTemplates::websiteKeys() as $key) {
            $response->assertSee('<loc>'.url("/templates/website/{$key}").'</loc>', false);
        }
    }

    public function test_sitemap_excludes_guides_for_features_that_ship_in_phase_two(): void
    {
        $response = $this->get('/sitemap.xml');

        foreach (HelpGuides::PHASE_TWO as $slug) {
            $response->assertDontSee(url("/help/{$slug}"), false);
        }

        // …but the guides that are live do get listed.
        foreach (array_keys(HelpGuides::visible()) as $slug) {
            $response->assertSee('<loc>'.url("/help/{$slug}").'</loc>', false);
        }
    }
}
