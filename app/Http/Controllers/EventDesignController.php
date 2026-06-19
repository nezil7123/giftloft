<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventDesignRequest;
use App\Models\Event;
use App\Support\EventTemplates;
use Inertia\Inertia;

class EventDesignController extends Controller
{
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

        $event->update($request->validated());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Design saved.');
    }
}
