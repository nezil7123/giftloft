<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventPhotoTest extends TestCase
{
    use RefreshDatabase;

    private function event(User $user, array $attrs = []): Event
    {
        return Event::create(array_merge([
            'user_id' => $user->id, 'title' => 'Wedding', 'type' => 'wedding',
            'is_public' => true, 'status' => 'published', 'share_code' => 'sc'.uniqid(),
        ], $attrs));
    }

    public function test_owner_can_upload_multiple_photos(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)
            ->post("/events/{$event->id}/photos", [
                'photos' => [
                    UploadedFile::fake()->image('one.jpg'),
                    UploadedFile::fake()->image('two.png'),
                ],
            ])
            ->assertRedirect();

        $photos = $event->fresh()->photos;
        $this->assertCount(2, $photos);
        foreach ($photos as $path) {
            $this->assertStringStartsWith("/storage/events/{$event->id}/", $path);
            Storage::disk('public')->assertExists(str_replace('/storage/', '', $path));
        }
    }

    public function test_non_owner_cannot_upload_photos(): void
    {
        Storage::fake('public');
        $event = $this->event(User::factory()->create());

        $this->actingAs(User::factory()->create())
            ->post("/events/{$event->id}/photos", ['photos' => [UploadedFile::fake()->image('x.jpg')]])
            ->assertForbidden();
    }

    public function test_upload_rejects_non_images(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)
            ->post("/events/{$event->id}/photos", ['photos' => [UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')]])
            ->assertSessionHasErrors('photos.0');

        $this->assertEmpty($event->fresh()->photos ?? []);
    }

    public function test_owner_can_delete_a_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)->post("/events/{$event->id}/photos", [
            'photos' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')],
        ]);

        $photos = $event->fresh()->photos;
        $target = $photos[0];

        $this->actingAs($user)
            ->delete("/events/{$event->id}/photos", ['path' => $target])
            ->assertRedirect();

        $remaining = $event->fresh()->photos;
        $this->assertNotContains($target, $remaining);
        $this->assertCount(1, $remaining);
        Storage::disk('public')->assertMissing(str_replace('/storage/', '', $target));
    }

    public function test_reorder_makes_chosen_photo_the_cover(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = $this->event($user);

        $this->actingAs($user)->post("/events/{$event->id}/photos", [
            'photos' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')],
        ]);

        $photos = $event->fresh()->photos;
        $second = $photos[1];

        $this->actingAs($user)
            ->put("/events/{$event->id}/photos/order", ['order' => [$second, $photos[0]]])
            ->assertRedirect();

        $this->assertSame($second, $event->fresh()->photos[0]);
    }
}
