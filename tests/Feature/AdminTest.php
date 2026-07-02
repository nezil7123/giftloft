<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function product(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Marble Board', 'slug' => 'marble-board-'.uniqid(),
            'category' => 'wedding', 'price' => 2499, 'emoji' => '🧀',
            'accent' => 'neutral', 'is_active' => true,
        ], $attrs));
    }

    // ── Access control ──────────────────────────────────────

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_non_admin_gets_403(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/admin', '/admin/products', '/admin/users', '/admin/moderation', '/admin/orders'] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_admin_can_open_all_panel_pages(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin')->assertOk()->assertInertia(fn ($p) => $p->component('Admin/Dashboard')->has('stats'));
        $this->get('/admin/products')->assertOk()->assertInertia(fn ($p) => $p->component('Admin/Products'));
        $this->get('/admin/users')->assertOk()->assertInertia(fn ($p) => $p->component('Admin/Users'));
        $this->get('/admin/moderation')->assertOk()->assertInertia(fn ($p) => $p->component('Admin/Moderation'));
        $this->get('/admin/orders')->assertOk()->assertInertia(fn ($p) => $p->component('Admin/Orders'));
    }

    // ── Products ────────────────────────────────────────────

    public function test_admin_can_create_product(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/products', [
                'name' => 'Velvet Throw', 'category' => 'home', 'price' => 1999,
                'description' => 'Soft.', 'emoji' => '🧶', 'accent' => 'violet',
                'is_active' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['name' => 'Velvet Throw', 'slug' => 'velvet-throw']);
    }

    public function test_admin_can_update_and_delete_product(): void
    {
        $admin = $this->admin();
        $product = $this->product();

        $this->actingAs($admin)
            ->put("/admin/products/{$product->slug}", [
                'name' => $product->name, 'category' => 'home', 'price' => 999,
                'emoji' => '🧀', 'accent' => 'amber', 'is_active' => false,
            ])
            ->assertRedirect();

        $fresh = $product->fresh();
        $this->assertSame('home', $fresh->category);
        $this->assertFalse($fresh->is_active);
        $this->assertSame($product->slug, $fresh->slug); // slug stays stable

        $this->actingAs($admin)->delete("/admin/products/{$product->slug}")->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_validation_rejects_bad_category(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/products', [
                'name' => 'X', 'category' => 'nope', 'price' => 10,
                'accent' => 'indigo', 'is_active' => true,
            ])
            ->assertSessionHasErrors('category');
    }

    // ── Users ───────────────────────────────────────────────

    public function test_admin_can_toggle_admin_on_another_user(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create();

        $this->actingAs($admin)->put("/admin/users/{$user->id}/admin")->assertRedirect();
        $this->assertTrue($user->fresh()->is_admin);
    }

    public function test_admin_cannot_toggle_or_delete_self(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put("/admin/users/{$admin->id}/admin")->assertForbidden();
        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin())->delete("/admin/users/{$user->id}")->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    // ── Moderation ──────────────────────────────────────────

    public function test_admin_can_take_down_and_restore_event(): void
    {
        $admin = $this->admin();
        $owner = User::factory()->create();
        $event = Event::create([
            'user_id' => $owner->id, 'title' => 'Big Party', 'type' => 'birthday',
            'is_public' => true, 'status' => 'published', 'share_code' => 'sc'.uniqid(),
        ]);

        $this->actingAs($admin)->put("/admin/events/{$event->id}/takedown")->assertRedirect();
        $fresh = $event->fresh();
        $this->assertSame('draft', $fresh->status);
        $this->assertFalse($fresh->is_public);

        // Public page is now gone
        $this->get("/e/{$fresh->share_code}")->assertNotFound();

        $this->actingAs($admin)->put("/admin/events/{$event->id}/restore")->assertRedirect();
        $this->assertSame('published', $event->fresh()->status);
    }

    public function test_admin_can_toggle_registry(): void
    {
        $owner = User::factory()->create();
        $wishlist = Wishlist::create([
            'user_id' => $owner->id, 'name' => 'Our Registry',
            'visibility' => 'public', 'slug' => 'reg-'.uniqid(), 'active' => true,
        ]);

        $this->actingAs($this->admin())->put("/admin/wishlists/{$wishlist->id}/toggle")->assertRedirect();
        $this->assertFalse($wishlist->fresh()->active);
    }

    // ── Non-admin cannot mutate ─────────────────────────────

    public function test_non_admin_cannot_manage_products_or_users(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();
        $product = $this->product();

        $this->actingAs($user)->post('/admin/products', [])->assertForbidden();
        $this->actingAs($user)->delete("/admin/products/{$product->slug}")->assertForbidden();
        $this->actingAs($user)->put("/admin/users/{$target->id}/admin")->assertForbidden();
    }
}
