<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_is_public_and_lists_templates_with_per_type_samples(): void
    {
        $this->get('/templates')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Templates/Index')
                ->has('websiteTemplates', 12)
                ->has('invitationTemplates', 11)
                ->has('eventTypes', 8)
                ->where('samples.wedding.title', 'Sarah & James')
                ->where('samples.birthday.type', 'birthday')
                ->where('samples.baby_shower.type', 'baby_shower'));
    }

    public function test_website_preview_themes_to_event_type(): void
    {
        $this->get('/templates/website/festive?type=birthday')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('eventType', 'birthday')
                ->where('event.type', 'birthday'));
    }

    public function test_website_preview_falls_back_for_unknown_type(): void
    {
        $this->get('/templates/website/festive?type=bogus')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('eventType', 'wedding'));
    }

    public function test_website_preview_renders_with_dummy_data(): void
    {
        $this->get('/templates/website/midnight')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Templates/WebsitePreview')
                ->where('templateKey', 'midnight')
                ->where('embed', false)
                ->where('event.template', 'midnight'));
    }

    public function test_website_preview_supports_embed_flag(): void
    {
        $this->get('/templates/website/festive?embed=1')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('embed', true));
    }

    public function test_new_premium_website_templates_are_previewable(): void
    {
        foreach (['aurora', 'cinema', 'royal', 'prism', 'ember', 'nova'] as $key) {
            $this->get("/templates/website/{$key}")
                ->assertOk()
                ->assertInertia(fn ($page) => $page->where('templateKey', $key));
        }
    }

    public function test_unknown_template_key_404s(): void
    {
        $this->get('/templates/website/nope')->assertNotFound();
    }
}
