<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Inertia\Inertia;

class WishlistController extends Controller
{
    /**
     * Display a public wishlist by slug. Only public, active wishlists are shown.
     */
    public function show(string $slug)
    {
        $wishlist = Wishlist::where('slug', $slug)
            ->where('visibility', 'public')
            ->where('active', true)
            ->with([
                'user:id,name',
                'event:id,title,share_code',
                'items' => fn ($q) => $q->where('is_giftable', true)->latest(),
            ])
            ->firstOrFail();

        return Inertia::render('Public/WishlistShow', [
            'wishlist' => $wishlist, // delivery_address stays hidden (model $hidden)
            'hasSavedAddress' => (bool) $wishlist->delivery_address,
            'isAuthenticated' => auth()->check(),
        ]);
    }
}
