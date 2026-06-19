<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_is_public_and_lists_templates_with_sample(): void
    {
        $this->get('/templates')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Templates/Index')
                ->has('websiteTemplates', 6)
                ->has('invitationTemplates', 6)
                ->where('sample.title', 'Sarah & James'));
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

    public function test_unknown_template_key_404s(): void
    {
        $this->get('/templates/website/nope')->assertNotFound();
    }
}
