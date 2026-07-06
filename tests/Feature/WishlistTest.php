<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    private function makeWishlist(User $user, array $attrs = []): Wishlist
    {
        return Wishlist::create(array_merge([
            'user_id' => $user->id,
            'name' => 'My Registry',
            'visibility' => 'public',
            'active' => true,
            'slug' => 'my-registry-'.uniqid(),
        ], $attrs));
    }

    public function test_user_can_create_wishlist_with_unique_slug(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/wishlists', [
            'name' => 'Honeymoon Fund',
            'description' => 'Help us travel',
            'visibility' => 'public',
            'active' => true,
            'event_id' => null,
        ]);

        $wishlist = Wishlist::first();
        $this->assertSame('honeymoon-fund', $wishlist->slug);
        $this->assertSame($user->id, $wishlist->user_id);
    }

    public function test_slug_is_made_unique_on_collision(): void
    {
        $user = User::factory()->create();
        $this->makeWishlist($user, ['slug' => 'honeymoon-fund']);

        $this->actingAs($user)->post('/wishlists', [
            'name' => 'Honeymoon Fund', 'visibility' => 'public', 'active' => true,
        ]);

        $this->assertDatabaseHas('wishlists', ['slug' => 'honeymoon-fund-2']);
    }

    public function test_wishlist_can_only_link_to_own_event(): void
    {
        $user = User::factory()->create();
        $othersEvent = Event::create(['user_id' => User::factory()->create()->id, 'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft']);

        $this->actingAs($user)
            ->post('/wishlists', ['name' => 'R', 'visibility' => 'public', 'active' => true, 'event_id' => $othersEvent->id])
            ->assertSessionHasErrors('event_id');
    }

    public function test_non_owner_cannot_view_wishlist(): void
    {
        $wishlist = $this->makeWishlist(User::factory()->create());

        $this->actingAs(User::factory()->create())
            ->get("/wishlists/{$wishlist->id}")
            ->assertForbidden();
    }

    private function makeProduct(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Espresso Machine',
            'slug' => 'espresso-'.uniqid(),
            'category' => 'home',
            'price' => 12999.00,
            'emoji' => '☕',
            'accent' => 'neutral',
            'is_active' => true,
        ], $attrs));
    }

    public function test_owner_can_add_catalog_item(): void
    {
        $user = User::factory()->create();
        $wishlist = $this->makeWishlist($user);
        $product = $this->makeProduct();

        $this->actingAs($user)
            ->post(route('registry.products.add', $product->slug), ['wishlist_id' => $wishlist->id])
            ->assertRedirect();

        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'title' => 'Espresso Machine',
        ]);
    }

    public function test_adding_the_same_product_twice_bumps_quantity(): void
    {
        $user = User::factory()->create();
        $wishlist = $this->makeWishlist($user);
        $product = $this->makeProduct();

        $this->actingAs($user)->post(route('registry.products.add', $product->slug), ['wishlist_id' => $wishlist->id]);
        $this->actingAs($user)->post(route('registry.products.add', $product->slug), ['wishlist_id' => $wishlist->id]);

        $this->assertDatabaseCount('wishlist_items', 1);
        $this->assertDatabaseHas('wishlist_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_non_owner_cannot_add_item(): void
    {
        $wishlist = $this->makeWishlist(User::factory()->create());
        $product = $this->makeProduct();

        $this->actingAs(User::factory()->create())
            ->post(route('registry.products.add', $product->slug), ['wishlist_id' => $wishlist->id])
            ->assertForbidden();
    }

    public function test_item_must_belong_to_wishlist_on_update(): void
    {
        $user = User::factory()->create();
        $wishlist = $this->makeWishlist($user);
        $otherList = $this->makeWishlist($user);
        $item = WishlistItem::create(['wishlist_id' => $otherList->id, 'title' => 'A', 'quantity' => 1, 'is_giftable' => true]);

        $this->actingAs($user)
            ->put("/wishlists/{$wishlist->id}/items/{$item->id}", ['quantity' => 1, 'is_giftable' => true])
            ->assertNotFound();
    }

    public function test_owner_can_delete_item(): void
    {
        $user = User::factory()->create();
        $wishlist = $this->makeWishlist($user);
        $item = WishlistItem::create(['wishlist_id' => $wishlist->id, 'title' => 'A', 'quantity' => 1, 'is_giftable' => true]);

        $this->actingAs($user)->delete("/wishlists/{$wishlist->id}/items/{$item->id}")->assertRedirect();
        $this->assertDatabaseMissing('wishlist_items', ['id' => $item->id]);
    }

    public function test_public_registry_is_viewable_by_slug(): void
    {
        $wishlist = $this->makeWishlist(User::factory()->create(), ['slug' => 'public-reg', 'visibility' => 'public']);
        WishlistItem::create(['wishlist_id' => $wishlist->id, 'title' => 'Gift', 'quantity' => 1, 'is_giftable' => true]);

        $this->get('/r/public-reg')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Public/WishlistShow')->where('wishlist.name', 'My Registry'));
    }

    public function test_private_registry_is_not_public(): void
    {
        $wishlist = $this->makeWishlist(User::factory()->create(), ['slug' => 'priv-reg', 'visibility' => 'private']);

        $this->get('/r/priv-reg')->assertNotFound();
    }
}
