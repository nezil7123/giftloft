<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RsvpController extends Controller
{
    /**
     * Record a guest's reply from the public event page or invitation.
     *
     * Guests are not signed in, so a response is keyed on the email address:
     * replying again updates the existing answer instead of creating a second
     * one. Hosts previewing their own unpublished event can test the form too.
     */
    public function store(Request $request, string $shareCode)
    {
        $event = $this->respondableEvent($shareCode);

        abort_unless(self::isOpen($event), 403, 'RSVPs are closed for this event.');

        $settings = self::settings($event);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', Rule::in(Rsvp::STATUSES)],
            'party_size' => ['required', 'integer', 'min:1', 'max:20'],
            'meal_preference' => [
                $settings['ask_meal'] ? 'nullable' : 'prohibited',
                Rule::in(Rsvp::MEALS),
            ],
            'needs_accommodation' => ['boolean'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'meal_preference.prohibited' => 'Meal preferences are not being collected for this event.',
        ]);

        // Only attendees carry a meal choice, a head count or a room request.
        if ($data['status'] !== Rsvp::STATUS_ATTENDING) {
            $data['party_size'] = 1;
            $data['meal_preference'] = null;
            $data['needs_accommodation'] = false;
        }

        if (! $settings['ask_accommodation']) {
            $data['needs_accommodation'] = false;
        }

        $data['needs_accommodation'] = (bool) ($data['needs_accommodation'] ?? false);
        $data['user_id'] = $request->user()?->id;

        $email = $data['email'] ?? null;

        if ($email) {
            $event->rsvps()->updateOrCreate(['email' => $email], $data);
        } else {
            $event->rsvps()->create($data);
        }

        return back()->with('success', $data['status'] === Rsvp::STATUS_ATTENDING
            ? "Thank you! We've saved your RSVP."
            : "Thanks for letting us know — you'll be missed.");
    }

    /**
     * The host's view of who replied.
     */
    public function index(Event $event)
    {
        $this->authorize('update', $event);

        $event->load('rsvps');

        return Inertia::render('Events/Rsvps', [
            'event' => $event->only(['id', 'title', 'share_code', 'starts_at']),
            'rsvps' => $event->rsvps()->latest()->get(),
            'summary' => $event->rsvpSummary(),
            'settings' => self::settings($event),
        ]);
    }

    /**
     * RSVP settings live in template_data alongside the rest of the event's
     * content config. Collecting replies is on unless the host turns it off.
     *
     * @return array{enabled: bool, ask_meal: bool, ask_accommodation: bool, deadline: ?string}
     */
    public static function settings(Event $event): array
    {
        $data = $event->template_data ?? [];

        return [
            'enabled' => (bool) ($data['rsvp_enabled'] ?? true),
            'ask_meal' => (bool) ($data['rsvp_ask_meal'] ?? true),
            'ask_accommodation' => (bool) ($data['rsvp_ask_accommodation'] ?? true),
            'deadline' => $data['rsvp_deadline'] ?? null,
        ];
    }

    /**
     * Whether the form should accept replies right now.
     */
    public static function isOpen(Event $event): bool
    {
        $settings = self::settings($event);

        if (! $settings['enabled']) {
            return false;
        }

        if ($settings['deadline']) {
            try {
                return now()->startOfDay()->lte(\Illuminate\Support\Carbon::parse($settings['deadline'])->endOfDay());
            } catch (\Throwable) {
                return true;
            }
        }

        return true;
    }

    /**
     * Mirrors Public\EventController: strangers only reach public, published
     * events; the owner and admins can also reply while previewing.
     */
    protected function respondableEvent(string $shareCode): Event
    {
        $event = Event::where('share_code', $shareCode)->firstOrFail();

        $viewer = auth()->user();
        $canBypass = $viewer && ($viewer->id === $event->user_id || $viewer->is_admin);

        abort_unless($canBypass || ($event->is_public && $event->status === 'published'), 404);

        return $event;
    }
}
