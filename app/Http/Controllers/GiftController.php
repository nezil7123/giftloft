<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GiftController extends Controller
{
    /**
     * Purchase history — gifts the user has sent and received.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $sent = Gift::where('buyer_id', $userId)
            ->with(['recipient:id,name', 'wishlistItem:id,title,image_url', 'event:id,title'])
            ->latest()
            ->get();

        $received = Gift::where('recipient_id', $userId)
            ->with(['buyer:id,name', 'wishlistItem:id,title,image_url', 'event:id,title'])
            ->latest()
            ->get();

        return Inertia::render('Gifts/Index', [
            'sent' => $sent,
            'received' => $received,
        ]);
    }

    /**
     * Show a single gift with tracking details.
     */
    public function show(Request $request, Gift $gift)
    {
        abort_unless(
            in_array($request->user()->id, [$gift->buyer_id, $gift->recipient_id], true),
            403
        );

        $gift->load([
            'buyer:id,name',
            'recipient:id,name',
            'wishlistItem:id,title,image_url,product_url',
            'event:id,title',
            'payment:id,status,amount,razorpay_payment_id,created_at',
        ]);

        return Inertia::render('Gifts/Show', [
            'gift' => $gift,
            'isBuyer' => $request->user()->id === $gift->buyer_id,
        ]);
    }
}
