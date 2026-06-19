<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_events(): void
    {
        $this->get('/events')->assertRedirect('/login');
    }

    public function test_index_lists_only_the_users_events(): void
    {
        $user = User::factory()->create();
        $mine = Event::create(['user_id' => $user->id, 'title' => 'Mine', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft']);
        $other = Event::create(['user_id' => User::factory()->create()->id, 'title' => 'Theirs', 'type' => 'birthday', 'is_public' => true, 'status' => 'draft']);

        $this->actingAs($user)
            ->get('/events')
            ->assertInertia(fn ($page) => $page
                ->component('Events/Index')
                ->has('events', 1)
                ->where('events.0.title', 'Mine'));
    }

    public function test_user_can_create_an_event_and_gets_a_share_code(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/events', [
            'title' => 'Sarah & James',
            'type' => 'wedding',
            'description' => 'Join us',
            'location' => 'Mumbai',
            'venue' => 'Taj',
            'is_public' => true,
            'status' => 'published',
        ]);

        $event = Event::first();
        $this->assertNotNull($event);
        $this->assertSame('Sarah & James', $event->title);
        $this->assertSame($user->id, $event->user_id);
        $this->assertNotEmpty($event->share_code);
        $response->assertRedirect("/events/{$event->id}");
    }

    public function test_event_creation_is_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/events', ['title' => '', 'type' => 'not-a-type', 'is_public' => true, 'status' => 'draft'])
            ->assertSessionHasErrors(['title', 'type']);
    }

    public function test_owner_can_view_show_page(): void
    {
        $user = User::factory()->create();
        $event = Event::create(['user_id' => $user->id, 'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft']);

        $this->actingAs($user)->get("/events/{$event->id}")->assertOk();
    }

    public function test_non_owner_cannot_view_show_page(): void
    {
        $event = Event::create(['user_id' => User::factory()->create()->id, 'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft']);

        $this->actingAs(User::factory()->create())
            ->get("/events/{$event->id}")
            ->assertForbidden();
    }

    public function test_owner_can_update_event(): void
    {
        $user = User::factory()->create();
        $event = Event::create(['user_id' => $user->id, 'title' => 'Old', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft']);

        $this->actingAs($user)->put("/events/{$event->id}", [
            'title' => 'New Title',
            'type' => 'birthday',
            'is_public' => false,
            'status' => 'published',
        ])->assertRedirect("/events/{$event->id}");

        $this->assertSame('New Title', $event->fresh()->title);
        $this->assertSame('birthday', $event->fresh()->type);
    }

    public function test_non_owner_cannot_update_or_delete(): void
    {
        $event = Event::create(['user_id' => User::factory()->create()->id, 'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft']);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->put("/events/{$event->id}", ['title' => 'Hax', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft'])->assertForbidden();
        $this->actingAs($intruder)->delete("/events/{$event->id}")->assertForbidden();
    }

    public function test_owner_can_delete_event(): void
    {
        $user = User::factory()->create();
        $event = Event::create(['user_id' => $user->id, 'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft']);

        $this->actingAs($user)->delete("/events/{$event->id}")->assertRedirect('/events');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_public_event_page_is_accessible_by_share_code(): void
    {
        $event = Event::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Public Wedding', 'type' => 'wedding',
            'is_public' => true, 'status' => 'published', 'share_code' => 'abc123xy',
        ]);

        $this->get('/e/abc123xy')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/EventShow')
                ->where('event.title', 'Public Wedding'));
    }

    public function test_draft_or_private_events_are_not_public(): void
    {
        $draft = Event::create(['user_id' => User::factory()->create()->id, 'title' => 'D', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft', 'share_code' => 'draftcode']);
        $private = Event::create(['user_id' => User::factory()->create()->id, 'title' => 'P', 'type' => 'wedding', 'is_public' => false, 'status' => 'published', 'share_code' => 'privcode1']);

        $this->get('/e/draftcode')->assertNotFound();
        $this->get('/e/privcode1')->assertNotFound();
    }
}
