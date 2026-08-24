<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HelpTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_help_pages(): void
    {
        // Onboarding guides are public so prospective users can learn the
        // product before signing up.
        $this->get('/help')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Help/Index'));

        $this->get('/help/wishlist')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Help/Guide'));
    }

    public function test_help_hub_lists_website_and_invitation_guides(): void
    {
        // Wishlist/gifting are hidden from this listing while that side of
        // the product is disabled for the phase 1 launch (see
        // HelpController::index) — the guides themselves stay reachable
        // directly, covered by test_each_guide_page_renders below.
        $this->actingAs(User::factory()->create())
            ->get('/help')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Help/Index')
                ->missing('guides.wishlist')
                ->missing('guides.gifting')
                ->has('guides.website')
                ->has('guides.invitation'));
    }

    #[DataProvider('guideSlugs')]
    public function test_each_guide_page_renders(string $slug): void
    {
        $this->actingAs(User::factory()->create())
            ->get("/help/{$slug}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Help/Guide')
                ->where('guide.slug', $slug)
                ->has('guide.steps')
                ->has('guide.cta.route'));
    }

    public static function guideSlugs(): array
    {
        return [['wishlist'], ['gifting'], ['website'], ['invitation']];
    }

    public function test_unknown_guide_is_404(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/help/not-a-real-guide')
            ->assertNotFound();
    }
}
