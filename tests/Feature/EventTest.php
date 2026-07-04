<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_user_can_create_an_event_with_an_uploaded_cover_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/events', [
            'title' => 'Sarah & James',
            'type' => 'wedding',
            'is_public' => true,
            'status' => 'draft',
            'cover_photo' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $event = Event::first();
        $response->assertRedirect("/events/{$event->id}");
        $this->assertNotNull($event->cover_photo_url);
        $this->assertStringStartsWith("/storage/events/{$event->id}/", $event->cover_photo_url);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $event->cover_photo_url));
    }

    public function test_cover_photo_must_be_an_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post('/events', [
            'title' => 'Sarah & James',
            'type' => 'wedding',
            'is_public' => true,
            'status' => 'draft',
            'cover_photo' => UploadedFile::fake()->create('notes.pdf', 100),
        ])->assertSessionHasErrors(['cover_photo']);
    }

    public function test_owner_can_replace_and_remove_cover_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = Event::create(['user_id' => $user->id, 'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft']);

        $this->actingAs($user)->put("/events/{$event->id}", [
            'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft',
            'cover_photo' => UploadedFile::fake()->image('cover.jpg'),
        ])->assertRedirect("/events/{$event->id}");

        $event->refresh();
        $this->assertNotNull($event->cover_photo_url);
        $firstPath = str_replace('/storage/', '', $event->cover_photo_url);
        Storage::disk('public')->assertExists($firstPath);

        // Removing clears the URL and deletes the file.
        $this->actingAs($user)->put("/events/{$event->id}", [
            'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft',
            'remove_cover_photo' => true,
        ])->assertRedirect("/events/{$event->id}");

        $this->assertNull($event->fresh()->cover_photo_url);
        Storage::disk('public')->assertMissing($firstPath);
    }

    public function test_user_can_create_an_event_with_venue_details(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/events', [
            'title' => 'Sarah & James',
            'type' => 'wedding',
            'venue' => 'The Taj Mahal Palace',
            'location' => 'Mumbai',
            'is_public' => true,
            'status' => 'draft',
            'venue_note' => 'A sea-facing heritage ballroom',
            'venue_map_url' => 'https://maps.google.com/?q=Taj+Mahal+Palace',
            'travel' => 'Nearest station is Churchgate',
            'stay' => 'Try the Trident nearby',
            'venue_photo' => UploadedFile::fake()->image('venue.jpg'),
        ]);

        $event = Event::first();
        $response->assertRedirect("/events/{$event->id}");
        $this->assertSame('A sea-facing heritage ballroom', $event->template_data['venue_note']);
        $this->assertSame('https://maps.google.com/?q=Taj+Mahal+Palace', $event->template_data['venue_map_url']);
        $this->assertSame('Nearest station is Churchgate', $event->template_data['travel']);
        $this->assertSame('Try the Trident nearby', $event->template_data['stay']);
        $this->assertNotNull($event->template_data['venue_photo_url']);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $event->template_data['venue_photo_url']));
    }

    public function test_updating_an_event_does_not_wipe_out_design_editor_content(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id, 'title' => 'X', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft',
            'template_data' => ['hosts' => 'Sarah & James', 'schedule' => [['time' => '4 PM', 'title' => 'Ceremony']]],
        ]);

        // A basic profile edit that doesn't touch venue fields at all.
        $this->actingAs($user)->put("/events/{$event->id}", [
            'title' => 'Updated Title', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft',
        ])->assertRedirect("/events/{$event->id}");

        $fresh = $event->fresh();
        $this->assertSame('Updated Title', $fresh->title);
        $this->assertSame('Sarah & James', $fresh->template_data['hosts']);
        $this->assertCount(1, $fresh->template_data['schedule']);

        // Now update just the venue note — schedule/hosts must still survive.
        $this->actingAs($user)->put("/events/{$event->id}", [
            'title' => 'Updated Title', 'type' => 'wedding', 'is_public' => true, 'status' => 'draft',
            'venue_note' => 'Updated venue note',
        ]);

        $fresh = $event->fresh();
        $this->assertSame('Updated venue note', $fresh->template_data['venue_note']);
        $this->assertSame('Sarah & James', $fresh->template_data['hosts']);
        $this->assertCount(1, $fresh->template_data['schedule']);
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
