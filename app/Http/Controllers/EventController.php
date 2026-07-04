<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\StoresEventPhotos;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    use StoresEventPhotos;

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
        $data = $request->validated();
        $coverPhoto = $data['cover_photo'] ?? null;
        $venuePhoto = $data['venue_photo'] ?? null;
        $venueFields = $this->extractVenueFields($data);
        unset($data['cover_photo'], $data['remove_cover_photo'], $data['venue_photo'], $data['remove_venue_photo']);

        $event = Event::create([
            ...$data,
            'user_id' => $request->user()->id,
            'share_code' => $this->uniqueShareCode(),
        ]);

        if ($coverPhoto) {
            $event->update(['cover_photo_url' => $this->storeEventFile($event, $coverPhoto)]);
        }

        if ($venuePhoto || $venueFields) {
            $templateData = $venueFields;
            if ($venuePhoto) {
                $templateData['venue_photo_url'] = $this->storeEventFile($event, $venuePhoto);
            }
            $event->update(['template_data' => $templateData]);
        }

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

        $data = $request->validated();
        $coverPhoto = $data['cover_photo'] ?? null;
        $removeCover = (bool) ($data['remove_cover_photo'] ?? false);
        $venuePhoto = $data['venue_photo'] ?? null;
        $removeVenuePhoto = (bool) ($data['remove_venue_photo'] ?? false);
        $venueFields = $this->extractVenueFields($data);
        unset($data['cover_photo'], $data['remove_cover_photo'], $data['venue_photo'], $data['remove_venue_photo']);

        $event->update($data);

        if ($coverPhoto) {
            $this->deleteEventFile($event->cover_photo_url);
            $event->update(['cover_photo_url' => $this->storeEventFile($event, $coverPhoto)]);
        } elseif ($removeCover) {
            $this->deleteEventFile($event->cover_photo_url);
            $event->update(['cover_photo_url' => null]);
        }

        if ($venuePhoto || $removeVenuePhoto || $venueFields) {
            // Merge — never wholesale-replace template_data, since the design
            // editor (hosts/tagline/schedule/faqs/…) shares this same column.
            $templateData = [...($event->template_data ?? []), ...$venueFields];

            if ($venuePhoto) {
                $this->deleteEventFile($event->template_data['venue_photo_url'] ?? null);
                $templateData['venue_photo_url'] = $this->storeEventFile($event, $venuePhoto);
            } elseif ($removeVenuePhoto) {
                $this->deleteEventFile($event->template_data['venue_photo_url'] ?? null);
                $templateData['venue_photo_url'] = null;
            }

            $event->update(['template_data' => $templateData]);
        }

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

    /**
     * Pull the venue text fields that were actually submitted (so unrelated
     * saves don't blank out venue info set elsewhere, e.g. the design editor).
     *
     * @return array<string, mixed>
     */
    protected function extractVenueFields(array $data): array
    {
        return collect(['venue_note', 'venue_map_url', 'travel', 'stay'])
            ->filter(fn ($key) => array_key_exists($key, $data))
            ->mapWithKeys(fn ($key) => [$key => $data[$key]])
            ->all();
    }
}
