<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Marble Board', 'slug' => 'marble-board-'.uniqid(),
            'category' => 'wedding', 'price' => 2499, 'emoji' => '🧀',
            'accent' => 'neutral', 'description' => 'Nice board.', 'is_active' => true,
        ], $attrs));
    }

    private function wishlist(User $user, array $attrs = []): Wishlist
    {
        return Wishlist::create(array_merge([
            'user_id' => $user->id, 'name' => 'Our Registry', 'visibility' => 'public',
            'slug' => 'reg-'.uniqid(), 'active' => true,
        ], $attrs));
    }

    public function test_shop_lists_active_products(): void
    {
        $this->product(['name' => 'Visible']);
        $this->product(['name' => 'Hidden', 'is_active' => false]);

        $this->get('/shop')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Shop')
                ->has('products', 1)
                ->has('categories'));
    }

    public function test_product_detail_renders(): void
    {
        $product = $this->product();

        $this->get("/shop/{$product->slug}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/ProductDetail')
                ->where('product.slug', $product->slug));
    }

    public function test_inactive_product_detail_is_404(): void
    {
        $product = $this->product(['is_active' => false]);
        $this->get("/shop/{$product->slug}")->assertNotFound();
    }

    public function test_owner_can_add_product_to_their_registry(): void
    {
        $user = User::factory()->create();
        $wishlist = $this->wishlist($user);
        $product = $this->product();

        $this->actingAs($user)
            ->post(route('registry.products.add', $product->slug), ['wishlist_id' => $wishlist->id])
            ->assertRedirect();

        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'title' => $product->name,
            'price' => $product->price,
            'status' => 'available',
        ]);
    }

    public function test_cannot_add_product_to_someone_elses_registry(): void
    {
        $owner = User::factory()->create();
        $wishlist = $this->wishlist($owner);
        $product = $this->product();

        $this->actingAs(User::factory()->create())
            ->post(route('registry.products.add', $product->slug), ['wishlist_id' => $wishlist->id])
            ->assertForbidden();

        $this->assertDatabaseCount('wishlist_items', 0);
    }

    public function test_guest_cannot_add_product_to_registry(): void
    {
        $product = $this->product();
        $this->post(route('registry.products.add', $product->slug), ['wishlist_id' => 1])
            ->assertRedirect('/login');
    }
}
