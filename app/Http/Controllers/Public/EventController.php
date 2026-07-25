<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display the public event page for a given share code.
     * Only public, published events are accessible.
     */
    public function show(string $shareCode)
    {
        $event = $this->publicEvent($shareCode);

        return Inertia::render('Public/EventShow', [
            'event' => $event,
            'isAuthenticated' => auth()->check(),
        ]);
    }

    /**
     * Display the shareable invitation for an event.
     */
    public function invitation(string $shareCode)
    {
        $event = $this->publicEvent($shareCode);

        return Inertia::render('Public/EventInvitation', [
            'event' => $event,
        ]);
    }

    /**
     * Resolve an event by share code, or 404.
     *
     * Strangers only see public, published events. The owner can always view
     * their own event this way too — so the Design page's "Preview" links
     * and the share-link box work before the event is published.
     */
    protected function publicEvent(string $shareCode): Event
    {
        $event = Event::where('share_code', $shareCode)
            ->with([
                'user:id,name',
                'wishlists' => fn ($q) => $q->where('visibility', 'public')->where('active', true)->select('id', 'event_id', 'name', 'slug'),
            ])
            ->firstOrFail();

        $viewer = auth()->user();
        $canBypass = $viewer && ($viewer->id === $event->user_id || $viewer->is_admin);

        abort_unless($canBypass || ($event->is_public && $event->status === 'published'), 404);

        return $event;
    }
}
