<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Link-preview crawlers (Facebook, WhatsApp, X, LinkedIn, Slack) do not run
 * JavaScript, so every tag below must be present in the server-rendered HTML.
 * These tests fail if the meta ever moves back behind Inertia's client-side
 * <Head> component.
 */
class SocialMetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_ships_full_open_graph_and_twitter_tags_without_javascript(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('<meta property="og:url"', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:site_name" content="ComeYay">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('<meta name="twitter:image"', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('rel="canonical"', false);
    }

    public function test_og_image_is_an_absolute_url(): void
    {
        // Relative image paths are rejected by every major crawler.
        $this->get('/')->assertSee(
            '<meta property="og:image" content="'.rtrim(config('app.url'), '/').'/brand/comeyay-og.png">',
            false
        );
    }

    public function test_public_pages_have_distinct_titles_and_descriptions(): void
    {
        $seen = [];

        foreach (['/', '/templates', '/help', '/privacy', '/terms'] as $path) {
            $html = $this->get($path)->getContent();

            preg_match('/<title[^>]*>(.*?)<\/title>/s', $html, $title);
            preg_match('/<meta name="description" content="(.*?)">/s', $html, $description);

            $this->assertNotEmpty($title[1] ?? '', "No title on {$path}");
            $this->assertNotEmpty($description[1] ?? '', "No description on {$path}");

            $key = $title[1].'|'.$description[1];
            $this->assertNotContains($key, $seen, "Duplicate title/description on {$path}");
            $seen[] = $key;
        }
    }

    public function test_shared_event_link_previews_with_its_own_details_but_stays_out_of_search(): void
    {
        $user = User::factory()->create(['name' => 'Sarah']);
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Sarah & James',
            'type' => 'wedding',
            'venue' => 'The Palm Court',
            'location' => 'Mumbai',
            'starts_at' => now()->addDays(30),
            'status' => 'published',
            'is_public' => true,
            'share_code' => 'sharetest',
            'cover_photo_url' => '/storage/events/1/cover.jpg',
            'template' => 'classic',
            'invitation_template' => 'botanical',
            'template_data' => ['hosts' => 'Sarah & James', 'tagline' => 'We are getting married!'],
        ]);

        $response = $this->get("/e/{$event->share_code}");

        // The preview shows the couple's own words and their own cover photo…
        $response->assertSee('We are getting married!', false);
        $response->assertSee(
            '<meta property="og:image" content="'.rtrim(config('app.url'), '/').'/storage/events/1/cover.jpg">',
            false
        );
        $response->assertSee('<meta property="og:type" content="article">', false);

        // …but a private share link must never be indexed.
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_signed_in_app_pages_are_not_indexable(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_icon_and_manifest_assets_are_published(): void
    {
        foreach ([
            'favicon.ico', 'favicon.svg', 'favicon-16x16.png', 'favicon-32x32.png',
            'apple-touch-icon.png', 'android-chrome-192x192.png',
            'android-chrome-512x512.png', 'maskable-icon-512x512.png',
            'site.webmanifest', 'brand/comeyay-og.png',
        ] as $asset) {
            $this->assertFileExists(public_path($asset), "Missing public/{$asset}");
        }

        $manifest = json_decode(file_get_contents(public_path('site.webmanifest')), true);
        $this->assertSame('ComeYay', $manifest['name']);
        $this->assertNotEmpty($manifest['icons']);
    }
}
