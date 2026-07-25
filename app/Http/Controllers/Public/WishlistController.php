<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Inertia\Inertia;

class WishlistController extends Controller
{
    /**
     * Display a wishlist by slug.
     *
     * Strangers only see public, active wishlists. The owner can always view
     * their own registry this way too, so its share link works before the
     * registry is made public/active.
     */
    public function show(string $slug)
    {
        $wishlist = Wishlist::where('slug', $slug)
            ->with([
                'user:id,name',
                'event:id,title,share_code',
                'items' => fn ($q) => $q->where('is_giftable', true)->latest(),
            ])
            ->firstOrFail();

        $viewer = auth()->user();
        $canBypass = $viewer && ($viewer->id === $wishlist->user_id || $viewer->is_admin);

        abort_unless($canBypass || ($wishlist->visibility === 'public' && $wishlist->active), 404);

        return Inertia::render('Public/WishlistShow', [
            'wishlist' => $wishlist, // delivery_address stays hidden (model $hidden)
            'hasSavedAddress' => (bool) $wishlist->delivery_address,
            'isAuthenticated' => auth()->check(),
        ]);
    }
}
