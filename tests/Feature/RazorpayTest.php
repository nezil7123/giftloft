<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RazorpayTest extends TestCase
{
    use RefreshDatabase;

    // ── Service (live-mode logic, no network needed) ──

    public function test_live_signature_verification_uses_hmac(): void
    {
        $service = new RazorpayService(key: 'rzp_test_key', secret: 'topsecret');

        $orderId = 'order_live_abc';
        $paymentId = 'pay_live_xyz';
        $valid = hash_hmac('sha256', "{$orderId}|{$paymentId}", 'topsecret');

        $this->assertTrue($service->isLive());
        $this->assertTrue($service->verifyPayment($orderId, $paymentId, $valid));
        $this->assertFalse($service->verifyPayment($orderId, $paymentId, 'forged-signature'));
        $this->assertFalse($service->verifyPayment($orderId, $paymentId, null));
        // Stub-mode order ids don't get a free pass in live mode.
        $this->assertFalse($service->verifyPayment('order_test_fake', $paymentId, null));
    }

    public function test_stub_mode_without_keys(): void
    {
        $service = new RazorpayService(key: '', secret: '');

        $this->assertFalse($service->isLive());
        $order = $service->createOrder(1234.50);
        $this->assertTrue($order['test_mode']);
        $this->assertSame(123450, $order['amount']);
        $this->assertTrue($service->verifyPayment($order['id'], 'pay_test_1', null));
        $this->assertNull($service->orderAmount($order['id']));
    }

    // ── Webhook ──

    protected function webhookPayload(string $event, string $orderId, string $paymentId = 'pay_wh_1'): string
    {
        return json_encode([
            'event' => $event,
            'payload' => ['payment' => ['entity' => ['id' => $paymentId, 'order_id' => $orderId]]],
        ]);
    }

    public function test_webhook_rejects_missing_or_invalid_signature(): void
    {
        config(['services.razorpay.webhook_secret' => 'whsec']);

        $payload = $this->webhookPayload('payment.captured', 'order_x');

        $this->call('POST', '/webhooks/razorpay', [], [], [], [], $payload)->assertStatus(400);

        $this->call('POST', '/webhooks/razorpay', [], [], [], [
            'HTTP_X-Razorpay-Signature' => 'wrong',
        ], $payload)->assertStatus(400);
    }

    public function test_webhook_marks_payment_paid_and_failed(): void
    {
        config(['services.razorpay.webhook_secret' => 'whsec']);
        $user = User::factory()->create();

        $pending = Payment::create([
            'user_id' => $user->id, 'razorpay_order_id' => 'order_wh_1',
            'amount' => 500, 'currency' => 'INR', 'status' => 'pending',
        ]);

        $payload = $this->webhookPayload('payment.captured', 'order_wh_1');
        $this->call('POST', '/webhooks/razorpay', [], [], [], [
            'HTTP_X-Razorpay-Signature' => hash_hmac('sha256', $payload, 'whsec'),
        ], $payload)->assertOk();
        $this->assertSame('paid', $pending->fresh()->status);
        $this->assertSame('pay_wh_1', $pending->fresh()->razorpay_payment_id);

        $failing = Payment::create([
            'user_id' => $user->id, 'razorpay_order_id' => 'order_wh_2',
            'amount' => 500, 'currency' => 'INR', 'status' => 'pending',
        ]);
        $payload = $this->webhookPayload('payment.failed', 'order_wh_2');
        $this->call('POST', '/webhooks/razorpay', [], [], [], [
            'HTTP_X-Razorpay-Signature' => hash_hmac('sha256', $payload, 'whsec'),
        ], $payload)->assertOk();
        $this->assertSame('failed', $failing->fresh()->status);

        // A failed event never downgrades an already-paid payment.
        $payload = $this->webhookPayload('payment.failed', 'order_wh_1');
        $this->call('POST', '/webhooks/razorpay', [], [], [], [
            'HTTP_X-Razorpay-Signature' => hash_hmac('sha256', $payload, 'whsec'),
        ], $payload)->assertOk();
        $this->assertSame('paid', $pending->fresh()->status);
    }

    // ── Submit-time cart order endpoint ──

    public function test_cart_order_endpoint_prices_in_selected_addons(): void
    {
        $product = Product::create([
            'name' => 'Vase', 'slug' => 'vase-'.uniqid(), 'category' => 'wedding',
            'price' => 1000, 'emoji' => '🏺', 'accent' => 'neutral',
            'description' => 'A vase.', 'is_active' => true,
        ]);
        $addon = \App\Models\GiftAddon::create([
            'type' => 'packaging', 'name' => 'Premium Box', 'price' => 149,
            'is_default' => false, 'is_active' => true, 'sort_order' => 0,
        ]);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->post(route('cart.add', $product->slug));

        $this->actingAs($buyer)
            ->postJson('/checkout/cart/order', ['is_gift' => true, 'packaging_addon_id' => $addon->id])
            ->assertOk()
            ->assertJsonPath('order.amount', 114900) // (1000 + 149) × 100
            ->assertJsonPath('order.test_mode', true);

        // Addons ignored when not gifting.
        $this->actingAs($buyer)
            ->postJson('/checkout/cart/order', ['is_gift' => false, 'packaging_addon_id' => $addon->id])
            ->assertOk()
            ->assertJsonPath('order.amount', 100000);
    }
}
