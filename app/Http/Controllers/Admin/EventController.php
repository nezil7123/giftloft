<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Moderation overview — all events and registries on the platform.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $events = Event::query()
            ->when($q !== '', fn ($query) => $query->where('title', 'like', "%{$q}%"))
            ->with('user:id,name,email')
            ->withCount(['wishlists', 'gifts'])
            ->latest()
            ->paginate(10, ['*'], 'events_page')
            ->withQueryString();

        $wishlists = Wishlist::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->with('user:id,name,email', 'event:id,title')
            ->withCount('items')
            ->latest()
            ->paginate(10, ['*'], 'wishlists_page')
            ->withQueryString();

        return Inertia::render('Admin/Moderation', [
            'events' => $events,
            'wishlists' => $wishlists,
            'filters' => ['q' => $q],
        ]);
    }

    /**
     * Take an event offline (unpublish + make private).
     */
    public function takedown(Event $event)
    {
        $event->update(['status' => 'draft', 'is_public' => false]);

        return back()->with('success', "“{$event->title}” has been taken offline.");
    }

    /**
     * Restore a taken-down event.
     */
    public function restore(Event $event)
    {
        $event->update(['status' => 'published', 'is_public' => true]);

        return back()->with('success', "“{$event->title}” is live again.");
    }

    /**
     * Toggle a registry's active state.
     */
    public function toggleWishlist(Wishlist $wishlist)
    {
        $wishlist->update(['active' => ! $wishlist->active]);

        return back()->with('success', $wishlist->active ? 'Registry reactivated.' : 'Registry deactivated.');
    }
}
