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
     * Resolve a public, published event by share code or 404.
     */
    protected function publicEvent(string $shareCode): Event
    {
        return Event::where('share_code', $shareCode)
            ->where('is_public', true)
            ->where('status', 'published')
            ->with([
                'user:id,name',
                'wishlists' => fn ($q) => $q->where('visibility', 'public')->where('active', true)->select('id', 'event_id', 'name', 'slug'),
            ])
            ->firstOrFail();
    }
}
