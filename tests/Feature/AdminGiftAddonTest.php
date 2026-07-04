<?php

namespace Tests\Feature;

use App\Models\GiftAddon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGiftAddonTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function addon(array $attrs = []): GiftAddon
    {
        return GiftAddon::create(array_merge([
            'type' => 'packaging', 'name' => 'Standard Box', 'price' => 0,
            'is_default' => true, 'is_active' => true, 'sort_order' => 0,
        ], $attrs));
    }

    public function test_guest_is_redirected_from_gift_addons(): void
    {
        $this->get('/admin/gift-addons')->assertRedirect('/login');
    }

    public function test_non_admin_gets_403(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/gift-addons')
            ->assertForbidden();
    }

    public function test_admin_can_view_gift_addons_grouped_by_type(): void
    {
        $this->addon(['type' => 'packaging', 'name' => 'Standard Box']);
        $this->addon(['type' => 'message_sticker', 'name' => 'Message sticker on box', 'is_default' => false]);

        $this->actingAs($this->admin())
            ->get('/admin/gift-addons')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/GiftAddons')
                ->has('addons.packaging', 1)
                ->has('addons.message_sticker', 1)
                ->has('types'));
    }

    public function test_admin_can_create_addon(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/gift-addons', [
                'type' => 'custom_card', 'name' => 'Custom greeting card',
                'description' => 'A printed card.', 'price' => 79,
                'is_default' => false, 'is_active' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('gift_addons', ['name' => 'Custom greeting card', 'type' => 'custom_card', 'price' => 79]);
    }

    public function test_admin_can_update_and_delete_addon(): void
    {
        $admin = $this->admin();
        $addon = $this->addon(['name' => 'Eco Kraft Box', 'price' => 99]);

        $this->actingAs($admin)
            ->put("/admin/gift-addons/{$addon->id}", [
                'type' => 'packaging', 'name' => 'Eco Kraft Box', 'price' => 149,
                'is_default' => false, 'is_active' => false,
            ])
            ->assertRedirect();

        $fresh = $addon->fresh();
        $this->assertEquals(149, $fresh->price);
        $this->assertFalse($fresh->is_active);

        $this->actingAs($admin)->delete("/admin/gift-addons/{$addon->id}")->assertRedirect();
        $this->assertDatabaseMissing('gift_addons', ['id' => $addon->id]);
    }

    public function test_addon_validation_rejects_bad_type(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/gift-addons', [
                'type' => 'not-a-type', 'name' => 'X', 'price' => 10,
                'is_default' => false, 'is_active' => true,
            ])
            ->assertSessionHasErrors('type');
    }

    public function test_admin_can_upload_a_preview_image_for_an_addon(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/gift-addons', [
                'type' => 'packaging', 'name' => 'Premium Gift Box',
                'price' => 149, 'is_default' => false, 'is_active' => true,
                'image' => UploadedFile::fake()->image('box.jpg'),
            ])
            ->assertRedirect();

        $addon = GiftAddon::firstWhere('name', 'Premium Gift Box');
        $this->assertNotNull($addon->image);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $addon->image));
    }

    public function test_admin_can_replace_and_remove_addon_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $addon = $this->addon();

        $this->actingAs($admin)->put("/admin/gift-addons/{$addon->id}", [
            'type' => 'packaging', 'name' => $addon->name, 'price' => $addon->price,
            'is_default' => false, 'is_active' => true,
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]);
        $firstImage = $addon->fresh()->image;
        $this->assertNotNull($firstImage);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $firstImage));

        $this->actingAs($admin)->put("/admin/gift-addons/{$addon->id}", [
            'type' => 'packaging', 'name' => $addon->name, 'price' => $addon->price,
            'is_default' => false, 'is_active' => true, 'remove_image' => true,
        ]);
        $this->assertNull($addon->fresh()->image);
        Storage::disk('public')->assertMissing(str_replace('/storage/', '', $firstImage));
    }

    public function test_non_admin_cannot_manage_addons(): void
    {
        $addon = $this->addon();
        $user = User::factory()->create();

        $this->actingAs($user)->post('/admin/gift-addons', [])->assertForbidden();
        $this->actingAs($user)->put("/admin/gift-addons/{$addon->id}", [])->assertForbidden();
        $this->actingAs($user)->delete("/admin/gift-addons/{$addon->id}")->assertForbidden();
    }
}
