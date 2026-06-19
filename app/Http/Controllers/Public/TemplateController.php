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
            'sample' => EventTemplates::sampleEvent(),
        ]);
    }

    /**
     * Full-page live preview of a single website template, filled with dummy data.
     * Pass ?embed=1 to render the bare design (used inside gallery iframes).
     */
    public function website(Request $request, string $key)
    {
        abort_unless(in_array($key, EventTemplates::websiteKeys(), true), 404);

        return Inertia::render('Public/Templates/WebsitePreview', [
            'event' => EventTemplates::sampleEvent($key),
            'templateKey' => $key,
            'embed' => $request->boolean('embed'),
        ]);
    }
}
