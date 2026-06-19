<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Thin wrapper around Razorpay.
 *
 * Runs in TEST/STUB mode when no API credentials are configured: orders are
 * fabricated locally and every payment verifies as successful. When real
 * RAZORPAY_KEY / RAZORPAY_SECRET are present, the live branches (marked below)
 * should be wired to the razorpay/razorpay PHP SDK and webhook verification.
 */
class RazorpayService
{
    public function __construct(
        protected ?string $key = null,
        protected ?string $secret = null,
    ) {
        $this->key ??= config('services.razorpay.key');
        $this->secret ??= config('services.razorpay.secret');
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

        // LIVE: $api = new \Razorpay\Api\Api($this->key, $this->secret);
        //       $order = $api->order->create([...]); return [...];
        throw new \RuntimeException('Live Razorpay order creation not yet wired.');
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

        // LIVE: hash_hmac('sha256', $orderId.'|'.$paymentId, $this->secret) === $signature
        return false;
    }
}
