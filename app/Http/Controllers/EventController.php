<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display a listing of the user's events.
     */
    public function index(Request $request)
    {
        $events = Event::where('user_id', $request->user()->id)
            ->withCount('wishlists', 'gifts')
            ->latest()
            ->get();

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return Inertia::render('Events/Create', [
            'eventTypes' => EventRequest::types(),
        ]);
    }

    /**
     * Store a newly created event.
     */
    public function store(EventRequest $request)
    {
        $event = Event::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'share_code' => $this->uniqueShareCode(),
        ]);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event->loadCount('wishlists', 'gifts');

        return Inertia::render('Events/Show', [
            'event' => $event,
            'eventTypes' => EventRequest::types(),
        ]);
    }

    /**
     * Show the form for editing the event.
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        return Inertia::render('Events/Edit', [
            'event' => $event,
            'eventTypes' => EventRequest::types(),
        ]);
    }

    /**
     * Update the specified event.
     */
    public function update(EventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted.');
    }

    /**
     * Generate a unique, URL-friendly share code.
     */
    protected function uniqueShareCode(): string
    {
        do {
            $code = Str::lower(Str::random(8));
        } while (Event::where('share_code', $code)->exists());

        return $code;
    }
}
