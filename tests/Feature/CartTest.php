<?php

namespace Tests\Feature;

use App\Models\GiftAddon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Marble Board', 'slug' => 'marble-board-'.uniqid(),
            'category' => 'wedding', 'price' => 1500, 'emoji' => '🧀',
            'accent' => 'neutral', 'description' => 'Nice board.', 'is_active' => true,
        ], $attrs));
    }

    private function giftAddon(array $attrs = []): GiftAddon
    {
        return GiftAddon::create(array_merge([
            'type' => 'packaging', 'name' => 'Premium Gift Box', 'price' => 149,
            'image' => '/storage/gift-addons/box-premium.svg',
            'is_default' => false, 'is_active' => true, 'sort_order' => 0,
        ], $attrs));
    }

    private function checkoutPayload(string $orderId, array $overrides = []): array
    {
        return array_merge([
            'is_gift' => false,
            'recipient_name' => 'Alex Buyer', 'address_line' => '1 Main St', 'city' => 'Mumbai',
            'postal_code' => '400001',
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => 'pay_test_123',
            'razorpay_signature' => 'test_signature',
        ], $overrides);
    }

    public function test_guest_can_add_to_cart_and_view_it(): void
    {
        $product = $this->product();

        $this->post(route('cart.add', $product->slug))->assertRedirect();

        $this->get('/cart')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Cart')
                ->has('items', 1)
                ->where('items.0.product.id', $product->id)
                ->where('items.0.quantity', 1)
                ->where('subtotal', fn ($s) => (float) $s === 1500.0));
    }

    public function test_adding_twice_increments_quantity(): void
    {
        $product = $this->product();

        $this->post(route('cart.add', $product->slug));
        $this->post(route('cart.add', $product->slug));

        $this->get('/cart')->assertInertia(fn ($page) => $page->where('items.0.quantity', 2));
    }

    public function test_inactive_product_cannot_be_added(): void
    {
        $product = $this->product(['is_active' => false]);
        $this->post(route('cart.add', $product->slug))->assertNotFound();
    }

    public function test_cart_quantity_can_be_updated_and_removed(): void
    {
        $product = $this->product();
        $this->post(route('cart.add', $product->slug));

        $this->put(route('cart.update', $product->slug), ['quantity' => 4]);
        $this->get('/cart')->assertInertia(fn ($page) => $page->where('items.0.quantity', 4));

        $this->delete(route('cart.remove', $product->slug));
        $this->get('/cart')->assertInertia(fn ($page) => $page->has('items', 0));
    }

    public function test_guest_is_redirected_to_login_for_cart_checkout(): void
    {
        $product = $this->product();
        $this->post(route('cart.add', $product->slug));

        $this->get(route('checkout.cart'))->assertRedirect('/login');
    }

    public function test_checkout_page_loads_with_cart_contents(): void
    {
        $product = $this->product();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)
            ->get(route('checkout.cart'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Cart/Checkout')
                ->where('testMode', true)
                ->where('subtotal', fn ($s) => (float) $s === 1500.0)
                ->where('order.id', fn ($id) => str_starts_with($id, 'order_test_')));
    }

    public function test_empty_cart_checkout_is_404(): void
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer)->get(route('checkout.cart'))->assertNotFound();
    }

    public function test_completing_cart_checkout_creates_order_with_items_and_clears_cart(): void
    {
        $product = $this->product(['price' => 800]);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));
        $this->actingAs($buyer)->put(route('cart.update', $product->slug), ['quantity' => 2]);

        $this->actingAs($buyer)
            ->post(route('checkout.cart.store'), $this->checkoutPayload('order_test_abc'))
            ->assertRedirect();

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame($buyer->id, $order->user_id);
        $this->assertSame('completed', $order->status);
        $this->assertFalse($order->is_gift);
        $this->assertEquals(1600.0, (float) $order->subtotal);
        $this->assertCount(1, $order->items);
        $this->assertSame(2, $order->items->first()->quantity);

        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'status' => 'paid']);

        // Cart is now empty.
        $this->actingAs($buyer)->get('/cart')->assertInertia(fn ($page) => $page->has('items', 0));
    }

    public function test_gift_personally_checkout_stores_recipient_details(): void
    {
        $product = $this->product();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)->post(route('checkout.cart.store'), $this->checkoutPayload('order_test_gift', [
            'is_gift' => true,
            'recipient_name' => 'Priya Recipient',
            'address_line' => '99 Garden Rd',
            'city' => 'Pune',
            'postal_code' => '411001',
            'gift_message' => 'Happy birthday!',
        ]));

        $order = Order::first();
        $this->assertTrue($order->is_gift);
        $this->assertSame('Priya Recipient', $order->recipient_name);
        $this->assertSame('Happy birthday!', $order->gift_message);
    }

    public function test_invalid_payment_order_is_rejected(): void
    {
        $product = $this->product();
        $buyer = User::factory()->create();
        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)
            ->from(route('checkout.cart'))
            ->post(route('checkout.cart.store'), $this->checkoutPayload('bogus_order'))
            ->assertRedirect(route('checkout.cart'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_page_includes_active_gift_addons_grouped_by_type(): void
    {
        $product = $this->product();
        $buyer = User::factory()->create();
        $this->giftAddon(['type' => 'packaging', 'name' => 'Premium Gift Box', 'price' => 149]);
        $this->giftAddon(['type' => 'message_sticker', 'name' => 'Message sticker on box', 'price' => 49]);
        $this->giftAddon(['type' => 'custom_card', 'name' => 'Custom greeting card', 'price' => 79, 'is_active' => false]);

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)
            ->get(route('checkout.cart'))
            ->assertInertia(fn ($page) => $page
                ->has('giftAddons.packaging', 1)
                ->has('giftAddons.message_sticker', 1)
                ->missing('giftAddons.custom_card')); // inactive addon excluded
    }

    public function test_gift_checkout_with_addons_creates_order_addons_and_correct_total(): void
    {
        $product = $this->product(['price' => 1000]);
        $buyer = User::factory()->create();
        $packaging = $this->giftAddon(['type' => 'packaging', 'name' => 'Premium Gift Box', 'price' => 149]);
        $sticker = $this->giftAddon(['type' => 'message_sticker', 'name' => 'Message sticker on box', 'price' => 49]);
        $card = $this->giftAddon(['type' => 'custom_card', 'name' => 'Custom greeting card', 'price' => 79]);

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)->post(route('checkout.cart.store'), $this->checkoutPayload('order_test_addons', [
            'is_gift' => true,
            'recipient_name' => 'Priya Recipient',
            'packaging_addon_id' => $packaging->id,
            'message_sticker_addon_id' => $sticker->id,
            'message_sticker_note' => 'Happy Birthday!',
            'custom_card_addon_id' => $card->id,
            'custom_card_note' => 'Wishing you all the best.',
        ]))->assertRedirect();

        $order = Order::first();
        $this->assertEquals(1000.0, (float) $order->subtotal);
        $this->assertEquals(277.0, (float) $order->addons_total); // 149 + 49 + 79
        $this->assertEquals(1277.0, (float) $order->total);
        $this->assertCount(3, $order->addons);

        $stickerAddon = $order->addons->firstWhere('type', 'message_sticker');
        $this->assertSame('Happy Birthday!', $stickerAddon->note);
        $this->assertEquals(49.0, (float) $stickerAddon->price);
        $this->assertSame($sticker->image, $stickerAddon->image);

        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'amount' => 1277.0]);
    }

    public function test_addons_are_ignored_when_not_sending_as_a_gift(): void
    {
        $product = $this->product(['price' => 1000]);
        $buyer = User::factory()->create();
        $packaging = $this->giftAddon(['type' => 'packaging', 'name' => 'Premium Gift Box', 'price' => 149]);

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)->post(route('checkout.cart.store'), $this->checkoutPayload('order_test_noaddons', [
            'is_gift' => false,
            'packaging_addon_id' => $packaging->id,
        ]));

        $order = Order::first();
        $this->assertEquals(0.0, (float) $order->addons_total);
        $this->assertEquals(1000.0, (float) $order->total);
        $this->assertCount(0, $order->addons);
    }

    public function test_message_sticker_note_is_required_when_sticker_selected(): void
    {
        $product = $this->product();
        $buyer = User::factory()->create();
        $sticker = $this->giftAddon(['type' => 'message_sticker', 'name' => 'Message sticker on box', 'price' => 49]);

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)->post(route('checkout.cart.store'), $this->checkoutPayload('order_test_missingnote', [
            'is_gift' => true,
            'message_sticker_addon_id' => $sticker->id,
        ]))->assertSessionHasErrors('message_sticker_note');
    }

    public function test_inactive_or_wrong_type_addon_id_is_rejected(): void
    {
        $product = $this->product();
        $buyer = User::factory()->create();
        $inactive = $this->giftAddon(['type' => 'packaging', 'is_active' => false]);
        $wrongType = $this->giftAddon(['type' => 'custom_card', 'name' => 'Custom card']);

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)->post(route('checkout.cart.store'), $this->checkoutPayload('order_test_bad', [
            'is_gift' => true,
            'packaging_addon_id' => $inactive->id,
        ]))->assertSessionHasErrors('packaging_addon_id');

        $this->actingAs($buyer)->post(route('checkout.cart.store'), $this->checkoutPayload('order_test_bad2', [
            'is_gift' => true,
            'packaging_addon_id' => $wrongType->id,
        ]))->assertSessionHasErrors('packaging_addon_id');
    }

    public function test_user_can_view_their_own_order_but_not_someone_elses(): void
    {
        $product = $this->product();
        $buyer = User::factory()->create();
        $stranger = User::factory()->create();

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));
        $this->actingAs($buyer)->post(route('checkout.cart.store'), $this->checkoutPayload('order_test_view'));
        $order = Order::first();

        $this->actingAs($buyer)->get(route('orders.show', $order))->assertOk();
        $this->actingAs($stranger)->get(route('orders.show', $order))->assertForbidden();
    }
}
