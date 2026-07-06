<?php

namespace App\Services;

use Illuminate\Support\Str;
use Razorpay\Api\Api;

/**
 * Thin wrapper around Razorpay.
 *
 * Runs in TEST/STUB mode when no API credentials are configured: orders are
 * fabricated locally and every payment verifies as successful. With real
 * RAZORPAY_KEY / RAZORPAY_SECRET in .env it talks to the live Razorpay API
 * and enforces signature + amount verification.
 */
class RazorpayService
{
    public function __construct(
        protected ?string $key = null,
        protected ?string $secret = null,
        protected ?string $webhookSecret = null,
    ) {
        $this->key ??= config('services.razorpay.key');
        $this->secret ??= config('services.razorpay.secret');
        $this->webhookSecret ??= config('services.razorpay.webhook_secret');
    }

    /**
     * Whether real Razorpay credentials are configured.
     */
    public function isLive(): bool
    {
        return ! empty($this->key) && ! empty($this->secret);
    }

    public function publicKey(): ?string
    {
        return $this->key;
    }

    protected function api(): Api
    {
        return new Api($this->key, $this->secret);
    }

    /**
     * Create a payment order. Amount is in the major currency unit (e.g. rupees).
     *
     * @return array{id:string, amount:int, currency:string, status:string, test_mode:bool}
     */
    public function createOrder(float $amount, string $currency = 'INR', array $notes = []): array
    {
        $amountInSubunit = (int) round($amount * 100);

        if (! $this->isLive()) {
            // STUB: fabricate an order id locally.
            return [
                'id' => 'order_test_'.Str::lower(Str::random(14)),
                'amount' => $amountInSubunit,
                'currency' => $currency,
                'status' => 'created',
                'test_mode' => true,
            ];
        }

        $order = $this->api()->order->create([
            'amount' => $amountInSubunit,
            'currency' => $currency,
            'notes' => $notes,
        ]);

        return [
            'id' => $order['id'],
            'amount' => (int) $order['amount'],
            'currency' => $order['currency'],
            'status' => $order['status'],
            'test_mode' => false,
        ];
    }

    /**
     * Verify a payment signature returned by the checkout widget.
     */
    public function verifyPayment(string $orderId, string $paymentId, ?string $signature): bool
    {
        if (! $this->isLive()) {
            // STUB: any test payment is considered valid.
            return str_starts_with($orderId, 'order_test_');
        }

        if (blank($signature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $orderId.'|'.$paymentId, $this->secret);

        return hash_equals($expected, $signature);
    }

    /**
     * The amount (in currency subunits) of an order as Razorpay knows it —
     * lets checkout reject payments made against a stale or cheaper order.
     * Returns null in stub mode (nothing to check against).
     */
    public function orderAmount(string $orderId): ?int
    {
        if (! $this->isLive()) {
            return null;
        }

        try {
            return (int) $this->api()->order->fetch($orderId)['amount'];
        } catch (\Throwable) {
            return -1; // an unfetchable order can never match a real total
        }
    }

    /**
     * Verify the signature on an incoming webhook request body.
     */
    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        if (blank($this->webhookSecret) || blank($signature)) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $payload, $this->webhookSecret), $signature);
    }
}
