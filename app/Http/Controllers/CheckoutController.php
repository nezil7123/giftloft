<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartCheckoutRequest;
use App\Http\Requests\CheckoutRequest;
use App\Models\Gift;
use App\Models\GiftAddon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\WishlistItem;
use App\Services\RazorpayService;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

        // Tell the gifter a saved address exists — but only reveal who it
        // ships to and the city, never the address itself.
        $saved = $item->wishlist->delivery_address;

        return Inertia::render('Gifts/Checkout', [
            'item' => $item,
            'recipient' => $item->wishlist->user,
            'amount' => $amount,
            'order' => $order,
            'razorpayKey' => $this->razorpay->publicKey(),
            'testMode' => ! $this->razorpay->isLive(),
            'savedAddress' => $saved ? [
                'recipient_name' => $saved['recipient_name'] ?? $item->wishlist->user->name,
                'city' => $saved['city'] ?? '',
            ] : null,
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

        // Live mode: the paid order's amount must match this item's price —
        // rejects payments made against a stale or cheaper order.
        if ($this->razorpay->isLive()
            && $this->razorpay->orderAmount($data['razorpay_order_id']) !== (int) round($amount * 100)) {
            return back()->with('error', 'Payment amount mismatch. Please try again.');
        }

        // Resolve the shipping address server-side: either the wishlist
        // owner's saved address (never shown to the gifter) or the address
        // the gifter typed.
        $useSaved = (bool) ($data['use_saved_address'] ?? false);
        $saved = $item->wishlist->delivery_address;

        if ($useSaved && ! $saved) {
            throw ValidationException::withMessages([
                'use_saved_address' => 'This wishlist has no saved delivery address — please enter one.',
            ]);
        }

        $deliveryAddress = $useSaved
            ? [
                'recipient_name' => $saved['recipient_name'],
                'address_line' => $saved['address_line'],
                'city' => $saved['city'],
                'postal_code' => $saved['postal_code'],
                'phone' => $saved['phone'] ?? null,
            ]
            : [
                'recipient_name' => $data['recipient_name'],
                'address_line' => $data['address_line'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
            ];

        $gift = DB::transaction(function () use ($request, $item, $data, $amount, $deliveryAddress) {
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
                'delivery_address' => $deliveryAddress,
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

    /**
     * Show the checkout page for the shopping cart — buy for yourself or
     * send everything as a personal gift to someone else.
     */
    public function cart(Request $request)
    {
        $items = Cart::items();

        abort_if($items->isEmpty(), 404);

        $subtotal = Cart::subtotal();
        $order = $this->razorpay->createOrder($subtotal, 'INR', ['type' => 'cart']);
        $user = $request->user();

        return Inertia::render('Cart/Checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'order' => $order,
            'razorpayKey' => $this->razorpay->publicKey(),
            'testMode' => ! $this->razorpay->isLive(),
            'self' => [
                'name' => $user->name,
                'phone' => $user->mobile,
                'email' => $user->email,
                'address_line' => $user->address_line_1,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
            ],
            'giftAddons' => GiftAddon::active()->orderBy('sort_order')->get()->groupBy('type'),
        ]);
    }

    /**
     * Create the Razorpay order at pay time, priced from the live cart plus
     * the validated addon selection — the amount the widget charges always
     * matches what the server will expect in storeCart().
     */
    public function createCartOrder(Request $request)
    {
        $items = Cart::items();

        abort_if($items->isEmpty(), 404);

        $data = $request->validate(CartCheckoutRequest::addonRules());
        $isGift = (bool) ($data['is_gift'] ?? false);
        $selectedAddons = $isGift ? $this->resolveGiftAddons($data) : collect();
        $total = Cart::subtotal() + $selectedAddons->sum(fn ($row) => (float) $row['addon']->price);

        $order = $this->razorpay->createOrder($total, 'INR', [
            'type' => 'cart',
            'user_id' => (string) $request->user()->id,
        ]);

        return response()->json([
            'order' => $order,
            'key' => $this->razorpay->publicKey(),
        ]);
    }

    /**
     * Process the payment for the cart and record the order.
     */
    public function storeCart(CartCheckoutRequest $request)
    {
        $items = Cart::items();

        abort_if($items->isEmpty(), 404);

        $data = $request->validated();
        $subtotal = Cart::subtotal();
        $isGift = (bool) ($data['is_gift'] ?? false);

        // Never trust client-submitted prices — resolve addons from the live catalog.
        $selectedAddons = $isGift ? $this->resolveGiftAddons($data) : collect();
        $addonsTotal = $selectedAddons->sum(fn ($row) => (float) $row['addon']->price);
        $total = $subtotal + $addonsTotal;

        $verified = $this->razorpay->verifyPayment(
            $data['razorpay_order_id'],
            $data['razorpay_payment_id'],
            $data['razorpay_signature'] ?? null,
        );

        if (! $verified) {
            return back()->with('error', 'Payment could not be verified. Please try again.');
        }

        // Live mode: what was actually paid must equal the recomputed total
        // (cart + addons) — rejects stale orders created before the cart or
        // addon selection changed.
        if ($this->razorpay->isLive()
            && $this->razorpay->orderAmount($data['razorpay_order_id']) !== (int) round($total * 100)) {
            return back()->with('error', 'Payment amount mismatch — your cart may have changed. Please try again.');
        }

        $order = DB::transaction(function () use ($request, $items, $data, $subtotal, $addonsTotal, $total, $isGift, $selectedAddons) {
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature'] ?? null,
                'amount' => $total,
                'currency' => 'INR',
                'status' => 'paid',
                'metadata' => ['test_mode' => ! $this->razorpay->isLive(), 'type' => 'cart'],
            ]);

            $order = Order::create([
                'user_id' => $request->user()->id,
                'payment_id' => $payment->id,
                'subtotal' => $subtotal,
                'addons_total' => $addonsTotal,
                'total' => $total,
                'currency' => 'INR',
                'status' => 'completed',
                'is_gift' => $isGift,
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'] ?? null,
                'recipient_email' => $data['recipient_email'] ?? null,
                'address_line' => $data['address_line'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'gift_message' => $data['gift_message'] ?? null,
            ]);

            foreach ($items as $row) {
                $order->items()->create([
                    'product_id' => $row['product']->id,
                    'name' => $row['product']->name,
                    'image_url' => $row['product']->image_url,
                    'price' => $row['product']->price,
                    'quantity' => $row['quantity'],
                ]);
            }

            foreach ($selectedAddons as $row) {
                $order->addons()->create([
                    'gift_addon_id' => $row['addon']->id,
                    'type' => $row['addon']->type,
                    'name' => $row['addon']->name,
                    'image' => $row['addon']->image,
                    'price' => $row['addon']->price,
                    'note' => $row['note'],
                ]);
            }

            $payment->update(['order_id' => $order->id]);

            return $order;
        });

        Cart::clear();

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $order->is_gift ? 'Your gift is on its way! 🎁' : 'Order placed! 🎉');
    }

    /**
     * Resolve the buyer's selected gift addons (packaging, message sticker,
     * custom card) into their live catalog rows + any accompanying note.
     *
     * @return Collection<int, array{addon: GiftAddon, note: ?string}>
     */
    protected function resolveGiftAddons(array $data): Collection
    {
        $picks = [
            ['id' => $data['packaging_addon_id'] ?? null, 'type' => 'packaging', 'note' => null],
            ['id' => $data['message_sticker_addon_id'] ?? null, 'type' => 'message_sticker', 'note' => $data['message_sticker_note'] ?? null],
            ['id' => $data['custom_card_addon_id'] ?? null, 'type' => 'custom_card', 'note' => $data['custom_card_note'] ?? null],
        ];

        return collect($picks)
            ->filter(fn ($pick) => $pick['id'])
            ->map(function ($pick) {
                $addon = GiftAddon::where('id', $pick['id'])->where('type', $pick['type'])->where('is_active', true)->first();

                return $addon ? ['addon' => $addon, 'note' => $pick['note']] : null;
            })
            ->filter()
            ->values();
    }
}
