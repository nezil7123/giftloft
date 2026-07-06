<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    /**
     * Receive Razorpay webhook events (payment.captured / payment.failed).
     *
     * Authenticated by the X-Razorpay-Signature HMAC — configure the same
     * secret in the Razorpay dashboard and RAZORPAY_WEBHOOK_SECRET. Acts as
     * the source of truth if the buyer's browser dies mid-checkout.
     */
    public function __invoke(Request $request, RazorpayService $razorpay)
    {
        $payload = $request->getContent();

        if (! $razorpay->verifyWebhookSignature($payload, $request->header('X-Razorpay-Signature'))) {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        $event = json_decode($payload, true);
        $entity = $event['payload']['payment']['entity'] ?? [];
        $orderId = $entity['order_id'] ?? null;
        $paymentId = $entity['id'] ?? null;

        if ($orderId) {
            $payment = Payment::where('razorpay_order_id', $orderId)->first();

            match ($event['event'] ?? null) {
                'payment.captured' => $payment?->update([
                    'status' => 'paid',
                    'razorpay_payment_id' => $payment->razorpay_payment_id ?: $paymentId,
                ]),
                'payment.failed' => $payment && $payment->status !== 'paid'
                    ? $payment->update(['status' => 'failed'])
                    : null,
                default => Log::info('Unhandled Razorpay webhook event', ['event' => $event['event'] ?? null]),
            };
        }

        return response()->json(['status' => 'ok']);
    }
}
