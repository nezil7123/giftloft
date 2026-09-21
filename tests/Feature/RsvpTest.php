<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Rsvp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpTest extends TestCase
{
    use RefreshDatabase;

    private function event(array $attributes = [], array $templateData = []): Event
    {
        return Event::create(array_merge([
            'user_id' => User::factory()->create()->id,
            'title' => 'Sarah & James',
            'type' => 'wedding',
            'is_public' => true,
            'status' => 'published',
            'share_code' => 'rsvptest',
            'template_data' => $templateData,
        ], $attributes));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'status' => 'attending',
            'party_size' => 3,
            'meal_preference' => 'veg',
            'needs_accommodation' => true,
            'note' => 'No peanuts please',
        ], $overrides);
    }

    public function test_a_guest_can_rsvp_with_meal_and_accommodation_choices(): void
    {
        $event = $this->event();

        $this->post("/e/{$event->share_code}/rsvp", $this->payload())
            ->assertRedirect()
            ->assertSessionHas('success');

        $rsvp = Rsvp::first();
        $this->assertSame('Priya Sharma', $rsvp->name);
        $this->assertSame('attending', $rsvp->status);
        $this->assertSame(3, $rsvp->party_size);
        $this->assertSame('veg', $rsvp->meal_preference);
        $this->assertTrue($rsvp->needs_accommodation);
        $this->assertSame('No peanuts please', $rsvp->note);
    }

    public function test_replying_again_with_the_same_email_updates_rather_than_duplicates(): void
    {
        $event = $this->event();

        $this->post("/e/{$event->share_code}/rsvp", $this->payload());
        $this->post("/e/{$event->share_code}/rsvp", $this->payload([
            'party_size' => 1,
            'meal_preference' => 'non_veg',
        ]));

        $this->assertSame(1, Rsvp::count());
        $this->assertSame(1, Rsvp::first()->party_size);
        $this->assertSame('non_veg', Rsvp::first()->meal_preference);
    }

    public function test_declining_clears_the_attendee_only_answers(): void
    {
        $event = $this->event();

        $this->post("/e/{$event->share_code}/rsvp", $this->payload([
            'status' => 'not_attending',
            'party_size' => 4,
            'meal_preference' => 'veg',
            'needs_accommodation' => true,
        ]));

        $rsvp = Rsvp::first();
        $this->assertSame('not_attending', $rsvp->status);
        $this->assertSame(1, $rsvp->party_size);
        $this->assertNull($rsvp->meal_preference);
        $this->assertFalse($rsvp->needs_accommodation);
    }

    public function test_accommodation_is_ignored_when_the_host_is_not_asking(): void
    {
        $event = $this->event(templateData: ['rsvp_ask_accommodation' => false]);

        $this->post("/e/{$event->share_code}/rsvp", $this->payload(['needs_accommodation' => true]));

        $this->assertFalse(Rsvp::first()->needs_accommodation);
    }

    public function test_a_meal_choice_is_rejected_when_the_host_is_not_asking(): void
    {
        $event = $this->event(templateData: ['rsvp_ask_meal' => false]);

        $this->post("/e/{$event->share_code}/rsvp", $this->payload())
            ->assertSessionHasErrors('meal_preference');

        $this->assertSame(0, Rsvp::count());
    }

    public function test_rsvps_are_refused_when_the_host_switched_them_off(): void
    {
        $event = $this->event(templateData: ['rsvp_enabled' => false]);

        $this->post("/e/{$event->share_code}/rsvp", $this->payload())->assertForbidden();
        $this->assertSame(0, Rsvp::count());
    }

    public function test_rsvps_are_refused_after_the_deadline(): void
    {
        $event = $this->event(templateData: ['rsvp_deadline' => now()->subDay()->toDateString()]);

        $this->post("/e/{$event->share_code}/rsvp", $this->payload())->assertForbidden();
    }

    public function test_the_deadline_day_itself_still_accepts_replies(): void
    {
        $event = $this->event(templateData: ['rsvp_deadline' => now()->toDateString()]);

        $this->post("/e/{$event->share_code}/rsvp", $this->payload())->assertSessionHas('success');
        $this->assertSame(1, Rsvp::count());
    }

    public function test_a_draft_event_does_not_accept_rsvps_from_strangers(): void
    {
        $event = $this->event(['status' => 'draft']);

        $this->post("/e/{$event->share_code}/rsvp", $this->payload())->assertNotFound();
    }

    public function test_invalid_input_is_rejected(): void
    {
        $event = $this->event();

        $this->post("/e/{$event->share_code}/rsvp", $this->payload([
            'name' => '',
            'status' => 'maybe',
            'party_size' => 99,
        ]))->assertSessionHasErrors(['name', 'status', 'party_size']);
    }

    public function test_the_host_sees_a_summary_of_replies(): void
    {
        $event = $this->event();

        $this->post("/e/{$event->share_code}/rsvp", $this->payload([
            'email' => 'a@example.com', 'party_size' => 2, 'meal_preference' => 'veg', 'needs_accommodation' => true,
        ]));
        $this->post("/e/{$event->share_code}/rsvp", $this->payload([
            'email' => 'b@example.com', 'party_size' => 3, 'meal_preference' => 'non_veg', 'needs_accommodation' => false,
        ]));
        $this->post("/e/{$event->share_code}/rsvp", $this->payload([
            'email' => 'c@example.com', 'status' => 'not_attending',
        ]));

        $summary = $event->fresh()->rsvpSummary();

        $this->assertSame(3, $summary['responses']);
        $this->assertSame(2, $summary['attending']);
        $this->assertSame(1, $summary['not_attending']);
        $this->assertSame(5, $summary['head_count']);
        $this->assertSame(1, $summary['veg']);
        $this->assertSame(1, $summary['non_veg']);
        $this->assertSame(1, $summary['accommodation']);
    }

    public function test_only_the_owner_can_view_the_responses(): void
    {
        $event = $this->event();

        $this->actingAs(User::factory()->create())
            ->get("/events/{$event->id}/rsvps")
            ->assertForbidden();

        $this->actingAs($event->user)
            ->get("/events/{$event->id}/rsvps")
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Events/Rsvps')->has('summary'));
    }
}
