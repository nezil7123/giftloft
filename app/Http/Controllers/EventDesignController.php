<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\StoresEventPhotos;
use App\Http\Requests\EventDesignRequest;
use App\Models\Event;
use App\Support\EventTemplates;
use Inertia\Inertia;

class EventDesignController extends Controller
{
    use StoresEventPhotos;

    /**
     * Show the design editor — pick templates and fill content.
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        return Inertia::render('Events/Design', [
            'event' => $event,
            'websiteTemplates' => EventTemplates::websites(),
            'invitationTemplates' => EventTemplates::invitations(),
        ]);
    }

    /**
     * Save the chosen templates and content.
     */
    public function update(EventDesignRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $data = $request->validated();
        $venuePhoto = $data['venue_photo'] ?? null;
        $removeVenuePhoto = (bool) ($data['remove_venue_photo'] ?? false);
        unset($data['venue_photo'], $data['remove_venue_photo']);
        $data['template_data'] ??= [];

        if ($venuePhoto) {
            $this->deleteEventFile($event->template_data['venue_photo_url'] ?? null);
            $data['template_data']['venue_photo_url'] = $this->storeEventFile($event, $venuePhoto);
        } elseif ($removeVenuePhoto) {
            $this->deleteEventFile($event->template_data['venue_photo_url'] ?? null);
            $data['template_data']['venue_photo_url'] = null;
        } else {
            // Not touched this save — keep whatever was already stored.
            $data['template_data']['venue_photo_url'] = $event->template_data['venue_photo_url'] ?? null;
        }

        $event->update($data);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Design saved.');
    }
}
