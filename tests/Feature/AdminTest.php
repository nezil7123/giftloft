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

    public function test_admin_can_import_products_from_shopify_csv(): void
    {
        $header = 'Handle,Title,Body (HTML),Type,Tags,Published,Status,Variant Price,Image Src';
        $rows = [
            'marble-board,Marble Cheese Board,"<p>Lovely <b>board</b>.</p>",Home Decor,"housewarming, kitchen",TRUE,active,2499.00,https://cdn.shopify.com/board.jpg',
            // second row of the same product (an extra image) — Title/Type/Price blank, should not create a duplicate
            'marble-board,,,,,,,,https://cdn.shopify.com/board-2.jpg',
            'gold-ring,Gold Ring,,Jewelry,,TRUE,active,4999.00,https://cdn.shopify.com/ring.jpg',
            'draft-thing,Unfinished Draft,,Gadgets,,FALSE,draft,999.00,',
            'no-price,Missing Price,,Home,,TRUE,active,,https://cdn.shopify.com/x.jpg',
        ];
        $csv = $header."\n".implode("\n", $rows)."\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('shopify_products_export.csv', $csv);

        $this->actingAs($this->admin())
            ->post('/admin/products/import', [
                'file' => $file,
                'default_category' => 'home',
                'default_gender' => null,
            ])
            ->assertRedirect();

        // marble-board: only one product despite two CSV rows, category guessed from "Home Decor" tag/type
        $this->assertDatabaseHas('products', [
            'slug' => 'marble-board', 'name' => 'Marble Cheese Board', 'category' => 'home',
            'price' => '2499.00', 'image_url' => 'https://cdn.shopify.com/board.jpg', 'is_active' => true,
        ]);
        $this->assertSame(1, Product::where('slug', 'marble-board')->count());

        // "Jewelry" (US spelling) still maps to our "jewellery" category
        $this->assertDatabaseHas('products', ['slug' => 'gold-ring', 'category' => 'jewellery']);

        // draft/unpublished product imports as inactive
        $this->assertDatabaseHas('products', ['slug' => 'draft-thing', 'is_active' => false]);

        // rows with no price are skipped entirely
        $this->assertDatabaseMissing('products', ['slug' => 'no-price']);

        $this->assertSame(3, Product::count());
    }

    public function test_reimporting_the_same_shopify_csv_skips_existing_products(): void
    {
        $this->product(['slug' => 'marble-board']);

        $csv = "Handle,Title,Variant Price\nmarble-board,Marble Cheese Board,2499.00\n";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('export.csv', $csv);

        $this->actingAs($this->admin())
            ->post('/admin/products/import', ['file' => $file, 'default_category' => 'home'])
            ->assertRedirect();

        $this->assertSame(1, Product::where('slug', 'marble-board')->count());
    }

    public function test_non_admin_cannot_import_products(): void
    {
        $csv = "Handle,Title,Variant Price\nx,X,10\n";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('export.csv', $csv);

        $this->actingAs(User::factory()->create())
            ->post('/admin/products/import', ['file' => $file, 'default_category' => 'home'])
            ->assertForbidden();
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

        // Public page is now gone for guests (admins can still open it to moderate)
        auth()->logout();
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
