<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventDesignTest extends TestCase
{
    use RefreshDatabase;

    private function event(User $user, array $attrs = []): Event
    {
        return Event::create(array_merge([
            'user_id' => $user->id, 'title' => 'Wedding', 'type' => 'wedding',
            'is_public' => true, 'status' => 'published', 'share_code' => 'sc'.uniqid(),
        ], $attrs));
    }

    public function test_new_event_has_default_templates(): void
    {
        $event = $this->event(User::factory()->create());
        $this->assertSame('classic', $event->template);
        $this->assertSame('elegant', $event->invitation_template);
    }

    public function test_owner_can_open_design_editor(): void
    {
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)
            ->get("/events/{$event->id}/design")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Events/Design')
                ->has('websiteTemplates', 12)
                ->has('invitationTemplates', 11));
    }

    public function test_non_owner_cannot_open_design_editor(): void
    {
        $event = $this->event(User::factory()->create());
        $this->actingAs(User::factory()->create())
            ->get("/events/{$event->id}/design")
            ->assertForbidden();
    }

    public function test_owner_can_save_template_and_content(): void
    {
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'festive',
            'invitation_template' => 'bold',
            'template_data' => [
                'hosts' => 'Sarah & James',
                'tagline' => 'We are getting married!',
                'schedule' => [['time' => '4 PM', 'title' => 'Ceremony', 'detail' => 'Garden']],
                'faqs' => [['question' => 'Parking?', 'answer' => 'Yes, on-site.']],
            ],
        ])->assertRedirect("/events/{$event->id}");

        $event->refresh();
        $this->assertSame('festive', $event->template);
        $this->assertSame('bold', $event->invitation_template);
        $this->assertSame('Sarah & James', $event->template_data['hosts']);
        $this->assertCount(1, $event->template_data['schedule']);
    }

    public function test_owner_can_upload_a_venue_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'classic',
            'invitation_template' => 'elegant',
            'template_data' => ['venue_note' => 'A rooftop garden'],
            'venue_photo' => UploadedFile::fake()->image('venue.jpg'),
        ])->assertRedirect("/events/{$event->id}");

        $event->refresh();
        $this->assertNotNull($event->template_data['venue_photo_url']);
        $this->assertStringStartsWith("/storage/events/{$event->id}/", $event->template_data['venue_photo_url']);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $event->template_data['venue_photo_url']));
    }

    public function test_venue_photo_persists_across_unrelated_saves(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'classic', 'invitation_template' => 'elegant',
            'venue_photo' => UploadedFile::fake()->image('venue.jpg'),
        ]);
        $storedUrl = $event->fresh()->template_data['venue_photo_url'];

        // Saving again without touching the photo should keep it intact.
        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'classic', 'invitation_template' => 'elegant',
            'template_data' => ['hosts' => 'Sarah & James'],
        ]);

        $this->assertSame($storedUrl, $event->fresh()->template_data['venue_photo_url']);
        $this->assertSame('Sarah & James', $event->fresh()->template_data['hosts']);
    }

    public function test_owner_can_remove_venue_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'classic', 'invitation_template' => 'elegant',
            'venue_photo' => UploadedFile::fake()->image('venue.jpg'),
        ]);
        $path = str_replace('/storage/', '', $event->fresh()->template_data['venue_photo_url']);
        Storage::disk('public')->assertExists($path);

        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'classic', 'invitation_template' => 'elegant',
            'remove_venue_photo' => true,
        ]);

        $this->assertNull($event->fresh()->template_data['venue_photo_url']);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_venue_photo_must_be_an_image(): void
    {
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'classic', 'invitation_template' => 'elegant',
            'venue_photo' => UploadedFile::fake()->create('notes.pdf', 100),
        ])->assertSessionHasErrors('venue_photo');
    }

    public function test_venue_map_url_is_saved_and_validated(): void
    {
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'classic', 'invitation_template' => 'elegant',
            'template_data' => ['venue_map_url' => 'https://maps.google.com/?q=Taj+Mahal'],
        ])->assertRedirect("/events/{$event->id}");

        $this->assertSame('https://maps.google.com/?q=Taj+Mahal', $event->fresh()->template_data['venue_map_url']);

        $this->actingAs($user)->put("/events/{$event->id}/design", [
            'template' => 'classic', 'invitation_template' => 'elegant',
            'template_data' => ['venue_map_url' => 'not-a-url'],
        ])->assertSessionHasErrors('template_data.venue_map_url');
    }

    public function test_invalid_template_key_is_rejected(): void
    {
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)
            ->put("/events/{$event->id}/design", ['template' => 'nope', 'invitation_template' => 'elegant'])
            ->assertSessionHasErrors('template');
    }

    public function test_public_event_uses_selected_template(): void
    {
        $event = $this->event(User::factory()->create(), [
            'share_code' => 'tpl-demo', 'template' => 'modern',
            'template_data' => ['hosts' => 'A & B'],
        ]);

        $this->get('/e/tpl-demo')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/EventShow')
                ->where('event.template', 'modern')
                ->where('event.template_data.hosts', 'A & B'));
    }

    public function test_public_invitation_page_renders(): void
    {
        $event = $this->event(User::factory()->create(), ['share_code' => 'inv-demo', 'invitation_template' => 'floral']);

        $this->get('/e/inv-demo/invitation')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/EventInvitation')
                ->where('event.invitation_template', 'floral'));
    }
}
