<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    private function publicEvent(User $user, array $attrs = []): Event
    {
        return Event::create(array_merge([
            'user_id' => $user->id, 'title' => 'Our Wedding', 'type' => 'wedding',
            'status' => 'published', 'is_public' => true, 'share_code' => 'code-'.uniqid(),
        ], $attrs));
    }

    private function publicWishlist(User $user, array $attrs = []): Wishlist
    {
        return Wishlist::create(array_merge([
            'user_id' => $user->id, 'name' => 'Our Registry', 'slug' => 'registry-'.uniqid(),
            'visibility' => 'public', 'active' => true,
        ], $attrs));
    }

    public function test_guest_can_view_a_public_profile(): void
    {
        $host = User::factory()->create(['name' => 'Sarah Host']);
        $event = $this->publicEvent($host);
        $this->publicWishlist($host);

        $this->get("/u/{$host->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Profile')
                ->where('profile.name', 'Sarah Host')
                ->has('events', 1)
                ->where('events.0.share_code', $event->share_code)
                ->has('wishlists', 1));
    }

    public function test_private_and_draft_content_is_hidden(): void
    {
        $host = User::factory()->create();
        $this->publicEvent($host, ['title' => 'Draft Party', 'status' => 'draft']);
        $this->publicEvent($host, ['title' => 'Secret Party', 'is_public' => false]);
        $this->publicWishlist($host, ['visibility' => 'private']);
        $this->publicWishlist($host, ['active' => false]);

        $this->get("/u/{$host->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('events', 0)
                ->has('wishlists', 0));
    }

    public function test_profile_never_exposes_email(): void
    {
        $host = User::factory()->create(['email' => 'private@example.com']);
        $this->publicEvent($host);

        $this->get("/u/{$host->id}")
            ->assertOk()
            ->assertDontSee('private@example.com')
            ->assertInertia(fn ($page) => $page->missing('profile.email'));
    }

    public function test_profile_is_reachable_by_username(): void
    {
        $host = User::factory()->create(['name' => 'Sarah Host', 'username' => 'sarah-james']);
        $this->publicEvent($host);

        $this->get('/u/sarah-james')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('profile.name', 'Sarah Host')
                ->where('profile.username', 'sarah-james')
                ->has('events', 1));
    }

    public function test_user_can_set_a_username_in_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name, 'email' => $user->email, 'username' => 'My-Handle_9',
        ])->assertSessionHasNoErrors();

        $this->assertSame('my-handle_9', $user->fresh()->username); // stored lowercase
    }

    public function test_invalid_reserved_and_taken_usernames_are_rejected(): void
    {
        $user = User::factory()->create();
        User::factory()->create(['username' => 'taken']);

        foreach (['has spaces', 'a', 'bad--dash', 'admin', 'taken'] as $bad) {
            $this->actingAs($user)->patch('/profile', [
                'name' => $user->name, 'email' => $user->email, 'username' => $bad,
            ])->assertSessionHasErrors('username');
        }
    }

    public function test_unknown_user_is_404(): void
    {
        $this->get('/u/999999')->assertNotFound();
        $this->get('/u/no-such-person')->assertNotFound();
    }

    public function test_logged_in_user_can_view_another_users_profile(): void
    {
        $host = User::factory()->create();
        $visitor = User::factory()->create();
        $this->publicEvent($host);

        $this->actingAs($visitor)->get("/u/{$host->id}")->assertOk();
    }
}
