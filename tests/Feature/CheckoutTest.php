<?php

namespace Tests\Feature;

use App\Models\Gift;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeItem(array $attrs = []): WishlistItem
    {
        $owner = User::factory()->create();
        $wishlist = Wishlist::create([
            'user_id' => $owner->id, 'name' => 'Registry', 'visibility' => 'public',
            'active' => true, 'slug' => 'reg-'.uniqid(),
        ]);

        return $wishlist->items()->create(array_merge([
            'title' => 'Espresso Machine', 'price' => 1000, 'quantity' => 1,
            'claimed_quantity' => 0, 'is_giftable' => true, 'status' => 'available',
        ], $attrs));
    }

    private function checkoutPayload(string $orderId): array
    {
        return [
            'recipient_name' => 'Sarah', 'address_line' => '1 Main St', 'city' => 'Mumbai',
            'postal_code' => '400001', 'message' => 'Congrats!',
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => 'pay_test_123',
            'razorpay_signature' => 'test_signature',
        ];
    }

    private array $savedAddress = [
        'recipient_name' => 'Priya Owner', 'phone' => '9876543210',
        'address_line' => '7 Rose Villa, Lane 4', 'city' => 'Pune', 'postal_code' => '411001',
    ];

    public function test_gifter_can_ship_to_the_wishlists_saved_address_without_entering_it(): void
    {
        $item = $this->makeItem();
        $item->wishlist->update(['delivery_address' => $this->savedAddress]);
        $buyer = User::factory()->create();

        // Checkout page hints at the saved address without leaking it.
        $this->actingAs($buyer)
            ->get("/checkout/item/{$item->id}")
            ->assertOk()
            ->assertDontSee('7 Rose Villa')
            ->assertInertia(fn ($page) => $page
                ->where('savedAddress.recipient_name', 'Priya Owner')
                ->where('savedAddress.city', 'Pune')
                ->missing('savedAddress.address_line')
                ->missing('item.wishlist.delivery_address'));

        // Paying with use_saved_address and no address fields succeeds.
        $this->actingAs($buyer)->post("/checkout/item/{$item->id}", [
            'use_saved_address' => true, 'message' => 'Enjoy!',
            'razorpay_order_id' => 'order_test_saved',
            'razorpay_payment_id' => 'pay_test_123', 'razorpay_signature' => 'test_signature',
        ])->assertRedirect();

        $gift = Gift::first();
        $this->assertSame('7 Rose Villa, Lane 4', $gift->delivery_address['address_line']);
        $this->assertSame('Priya Owner', $gift->delivery_address['recipient_name']);
        $this->assertSame('411001', $gift->delivery_address['postal_code']);
    }

    public function test_saved_address_flag_fails_when_wishlist_has_none(): void
    {
        $item = $this->makeItem();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->post("/checkout/item/{$item->id}", [
            'use_saved_address' => true,
            'razorpay_order_id' => 'order_test_none',
            'razorpay_payment_id' => 'pay_test_123', 'razorpay_signature' => 'test_signature',
        ])->assertSessionHasErrors('use_saved_address');

        $this->assertDatabaseCount('gifts', 0);
    }

    public function test_address_fields_still_required_when_not_using_saved_address(): void
    {
        $item = $this->makeItem();
        $item->wishlist->update(['delivery_address' => $this->savedAddress]);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->post("/checkout/item/{$item->id}", [
            'use_saved_address' => false,
            'razorpay_order_id' => 'order_test_req',
            'razorpay_payment_id' => 'pay_test_123', 'razorpay_signature' => 'test_signature',
        ])->assertSessionHasErrors(['recipient_name', 'address_line', 'city', 'postal_code']);
    }

    public function test_owner_can_save_and_clear_a_wishlist_delivery_address(): void
    {
        $owner = User::factory()->create();
        $wishlist = Wishlist::create([
            'user_id' => $owner->id, 'name' => 'Registry', 'visibility' => 'public',
            'active' => true, 'slug' => 'reg-'.uniqid(),
        ]);

        $base = ['name' => 'Registry', 'visibility' => 'public', 'active' => true];

        $this->actingAs($owner)->put("/wishlists/{$wishlist->id}", [
            ...$base, 'delivery_address' => $this->savedAddress,
        ])->assertSessionHasNoErrors();
        $this->assertSame('Pune', $wishlist->fresh()->delivery_address['city']);

        // Incomplete address (street given, city missing) is rejected.
        $this->actingAs($owner)->put("/wishlists/{$wishlist->id}", [
            ...$base, 'delivery_address' => ['address_line' => '7 Rose Villa'],
        ])->assertSessionHasErrors(['delivery_address.recipient_name', 'delivery_address.city', 'delivery_address.postal_code']);

        // Blanking the street clears the saved address.
        $this->actingAs($owner)->put("/wishlists/{$wishlist->id}", [
            ...$base, 'delivery_address' => ['recipient_name' => '', 'address_line' => '', 'city' => '', 'postal_code' => '', 'phone' => ''],
        ])->assertSessionHasNoErrors();
        $this->assertNull($wishlist->fresh()->delivery_address);
    }

    public function test_public_wishlist_page_flags_saved_address_but_never_exposes_it(): void
    {
        $item = $this->makeItem();
        $item->wishlist->update(['delivery_address' => $this->savedAddress]);

        $this->get('/r/'.$item->wishlist->slug)
            ->assertOk()
            ->assertDontSee('7 Rose Villa')
            ->assertInertia(fn ($page) => $page
                ->where('hasSavedAddress', true)
                ->missing('wishlist.delivery_address'));
    }

    public function test_checkout_page_loads_with_a_stub_order(): void
    {
        $item = $this->makeItem();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->get("/checkout/item/{$item->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Gifts/Checkout')
                ->where('testMode', true)
                ->where('amount', fn ($a) => (float) $a === 1000.0)
                ->where('order.id', fn ($id) => str_starts_with($id, 'order_test_')));
    }

    public function test_guest_is_redirected_to_login_for_checkout(): void
    {
        $item = $this->makeItem();
        $this->get("/checkout/item/{$item->id}")->assertRedirect('/login');
    }

    public function test_completing_checkout_creates_gift_and_payment_and_claims_item(): void
    {
        $item = $this->makeItem();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post("/checkout/item/{$item->id}", $this->checkoutPayload('order_test_abc'))
            ->assertRedirect();

        $gift = Gift::first();
        $this->assertNotNull($gift);
        $this->assertSame($buyer->id, $gift->buyer_id);
        $this->assertSame($item->wishlist->user_id, $gift->recipient_id);
        $this->assertSame('completed', $gift->status);
        $this->assertNotNull($gift->payment_id);

        $this->assertDatabaseHas('payments', ['gift_id' => $gift->id, 'status' => 'paid']);
        $this->assertSame(1, $item->fresh()->claimed_quantity);
        $this->assertSame('claimed', $item->fresh()->status);
    }

    public function test_invalid_payment_order_is_rejected(): void
    {
        $item = $this->makeItem();
        $buyer = User::factory()->create();

        // An order id that does not look like a real/stub order fails verification.
        $this->actingAs($buyer)
            ->from("/checkout/item/{$item->id}")
            ->post("/checkout/item/{$item->id}", $this->checkoutPayload('bogus_order'))
            ->assertRedirect("/checkout/item/{$item->id}");

        $this->assertDatabaseCount('gifts', 0);
    }

    public function test_fully_claimed_item_cannot_be_checked_out(): void
    {
        $item = $this->makeItem(['quantity' => 1, 'claimed_quantity' => 1, 'status' => 'claimed']);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->get("/checkout/item/{$item->id}")->assertStatus(410);
        $this->actingAs($buyer)->post("/checkout/item/{$item->id}", $this->checkoutPayload('order_test_x'))->assertStatus(410);
    }

    public function test_checkout_requires_delivery_fields(): void
    {
        $item = $this->makeItem();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post("/checkout/item/{$item->id}", ['razorpay_order_id' => 'order_test_x', 'razorpay_payment_id' => 'pay_x'])
            ->assertSessionHasErrors(['recipient_name', 'address_line', 'city', 'postal_code']);
    }

    public function test_only_buyer_or_recipient_can_view_gift(): void
    {
        $item = $this->makeItem();
        $buyer = User::factory()->create();
        $this->actingAs($buyer)->post("/checkout/item/{$item->id}", $this->checkoutPayload('order_test_abc'));
        $gift = Gift::first();

        // Buyer can view
        $this->actingAs($buyer)->get("/gifts/{$gift->id}")->assertOk();
        // Recipient can view
        $this->actingAs($item->wishlist->user)->get("/gifts/{$gift->id}")->assertOk();
        // A stranger cannot
        $this->actingAs(User::factory()->create())->get("/gifts/{$gift->id}")->assertForbidden();
    }
}
