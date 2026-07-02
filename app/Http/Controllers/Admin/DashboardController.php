<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Gift;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::count(),
                'events' => Event::count(),
                'published_events' => Event::where('status', 'published')->count(),
                'wishlists' => Wishlist::count(),
                'products' => Product::count(),
                'active_products' => Product::where('is_active', true)->count(),
                'gifts' => Gift::count(),
                'revenue' => (float) Gift::where('status', 'completed')->sum('amount'),
            ],
            'recentUsers' => User::latest()->take(5)->get(['id', 'name', 'email', 'created_at', 'is_admin']),
            'recentGifts' => Gift::with('buyer:id,name', 'recipient:id,name', 'wishlistItem:id,title')
                ->latest()->take(5)->get(),
        ]);
    }
}
