<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Order;
use App\Models\Payment;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Every purchase on the platform — wishlist-item gifts and shop cart
     * orders, with payment detail and combined totals.
     */
    public function index()
    {
        $giftOrders = Gift::query()
            ->with([
                'buyer:id,name,email',
                'recipient:id,name',
                'wishlistItem:id,title',
                'payment:id,gift_id,razorpay_payment_id,status',
                'event:id,title',
            ])
            ->latest()
            ->paginate(15, ['*'], 'gifts_page');

        $shopOrders = Order::query()
            ->with([
                'user:id,name,email',
                'items:id,order_id,name,quantity',
                'addons:id,order_id,name',
                'payment:id,order_id,razorpay_payment_id,status',
            ])
            ->latest()
            ->paginate(15, ['*'], 'shop_page');

        return Inertia::render('Admin/Orders', [
            'orders' => $giftOrders,
            'shopOrders' => $shopOrders,
            'totals' => [
                'revenue' => (float) Gift::where('status', 'completed')->sum('amount')
                    + (float) Order::where('status', 'completed')->sum('total'),
                'orders' => Gift::count() + Order::count(),
                'completed' => Gift::where('status', 'completed')->count()
                    + Order::where('status', 'completed')->count(),
                'payments' => Payment::where('status', 'paid')->count(),
            ],
        ]);
    }
}
