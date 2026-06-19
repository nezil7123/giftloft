<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Gift;
use App\Models\Payment;
use App\Models\WishlistItem;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __construct(protected RazorpayService $razorpay)
    {
    }

    /**
     * Show the checkout page for gifting a wishlist item.
     */
    public function show(WishlistItem $item)
    {
        $item->load('wishlist.user:id,name', 'wishlist.event:id,title');

        abort_unless($item->is_giftable, 404);
        abort_if($this->isFullyClaimed($item), 410, 'This gift has already been fully claimed.');

        $amount = (float) ($item->price ?? 0);
        $order = $this->razorpay->createOrder($amount, 'INR', ['wishlist_item_id' => $item->id]);

        return Inertia::render('Gifts/Checkout', [
            'item' => $item,
            'recipient' => $item->wishlist->user,
            'amount' => $amount,
            'order' => $order,
            'razorpayKey' => $this->razorpay->publicKey(),
            'testMode' => ! $this->razorpay->isLive(),
        ]);
    }

    /**
     * Process the (stubbed) payment and record the gift.
     */
    public function store(CheckoutRequest $request, WishlistItem $item)
    {
        $item->load('wishlist');

        abort_unless($item->is_giftable, 404);
        abort_if($this->isFullyClaimed($item), 410, 'This gift has already been fully claimed.');

        $data = $request->validated();

        // Verify the payment with Razorpay (auto-approves in test mode).
        $verified = $this->razorpay->verifyPayment(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature'] ?? null,
        );

        if (! $verified) {
            return back()->with('error', 'Payment could not be verified. Please try again.');
        }

        $amount = (float) ($item->price ?? 0);

        $gift = DB::transaction(function () use ($request, $item, $data, $amount) {
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature'] ?? null,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'paid',
                'metadata' => ['test_mode' => ! $this->razorpay->isLive()],
            ]);

            $gift = Gift::create([
                'buyer_id' => $request->user()->id,
                'recipient_id' => $item->wishlist->user_id,
                'wishlist_item_id' => $item->id,
                'event_id' => $item->wishlist->event_id,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'completed',
                'message' => $data['message'] ?? null,
                'delivery_address' => [
                    'recipient_name' => $data['recipient_name'],
                    'address_line' => $data['address_line'],
                    'city' => $data['city'],
                    'postal_code' => $data['postal_code'],
                ],
                'claimed_at' => now(),
            ]);

            $payment->update(['gift_id' => $gift->id]);

            // Claim the item.
            $item->increment('claimed_quantity');
            if ($item->fresh()->claimed_quantity >= $item->quantity) {
                $item->update(['status' => 'claimed']);
            }

            return $gift;
        });

        return redirect()
            ->route('gifts.show', $gift)
            ->with('success', 'Thank you! Your gift has been sent. 🎁');
    }

    protected function isFullyClaimed(WishlistItem $item): bool
    {
        return $item->claimed_quantity >= $item->quantity;
    }
}
