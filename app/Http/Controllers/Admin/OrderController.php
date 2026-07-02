<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Payment;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Every gift order on the platform, with payment detail and totals.
     */
    public function index()
    {
        $orders = Gift::query()
            ->with([
                'buyer:id,name,email',
                'recipient:id,name',
                'wishlistItem:id,title',
                'payment:id,gift_id,razorpay_payment_id,status',
                'event:id,title',
            ])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Orders', [
            'orders' => $orders,
            'totals' => [
                'revenue' => (float) Gift::where('status', 'completed')->sum('amount'),
                'orders' => Gift::count(),
                'completed' => Gift::where('status', 'completed')->count(),
                'payments' => Payment::where('status', 'paid')->count(),
            ],
        ]);
    }
}
