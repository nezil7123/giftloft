<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Wishlist;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * A user's public profile — only their published public events and
     * active public wishlists. Never exposes email or private content.
     *
     * Reachable by username (/u/sarah-james) or by id (/u/5).
     */
    public function show(string $identifier)
    {
        $user = ctype_digit($identifier)
            ? User::findOrFail($identifier)
            : User::where('username', mb_strtolower($identifier))->firstOrFail();

        abort_unless($user->profile_visible, 404);

        $events = Event::where('user_id', $user->id)
            ->where('is_public', true)
            ->where('status', 'published')
            ->orderByRaw('starts_at IS NULL, starts_at DESC')
            ->get(['id', 'title', 'type', 'share_code', 'starts_at', 'location', 'cover_photo_url']);

        $wishlists = Wishlist::where('user_id', $user->id)
            ->where('visibility', 'public')
            ->where('active', true)
            ->withCount('items')
            ->latest()
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Public/Profile', [
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'joined' => $user->created_at?->toDateString(),
            ],
            'events' => $events,
            'wishlists' => $wishlists,
        ]);
    }
}
