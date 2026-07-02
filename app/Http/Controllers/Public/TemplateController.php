<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\EventTemplates;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TemplateController extends Controller
{
    /**
     * Public gallery of all website + invitation templates with live previews.
     */
    public function index()
    {
        return Inertia::render('Public/Templates/Index', [
            'websiteTemplates' => EventTemplates::websites(),
            'invitationTemplates' => EventTemplates::invitations(),
            'eventTypes' => EventTemplates::eventTypeLabels(),
            // One themed sample event per event type (for switching previews).
            'samples' => EventTemplates::allSamples(),
        ]);
    }

    /**
     * Full-page live preview of a single website template, filled with dummy data.
     * Pass ?embed=1 to render the bare design (used inside gallery iframes),
     * and ?type= to theme the preview for a given event type.
     */
    public function website(Request $request, string $key)
    {
        abort_unless(in_array($key, EventTemplates::websiteKeys(), true), 404);

        $type = $request->string('type')->toString();
        if (! array_key_exists($type, EventTemplates::eventTypeLabels())) {
            $type = 'wedding';
        }

        return Inertia::render('Public/Templates/WebsitePreview', [
            'event' => EventTemplates::sampleEvent($key, 'elegant', $type),
            'templateKey' => $key,
            'eventType' => $type,
            'embed' => $request->boolean('embed'),
        ]);
    }
}
