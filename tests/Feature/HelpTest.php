<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HelpTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_help_pages(): void
    {
        $this->get('/help')->assertRedirect('/login');
        $this->get('/help/wishlist')->assertRedirect('/login');
    }

    public function test_help_hub_lists_all_guides(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/help')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Help/Index')
                ->has('guides.wishlist')
                ->has('guides.gifting')
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
