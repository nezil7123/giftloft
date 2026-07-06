<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/people')->assertRedirect('/login');
    }

    public function test_user_can_find_someone_by_exact_email(): void
    {
        $me = User::factory()->create();
        $friend = User::factory()->create(['name' => 'Priya Friend', 'email' => 'priya@example.com']);

        $this->actingAs($me)
            ->get('/people?q=Priya@Example.com') // case-insensitive
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('People/Find')
                ->where('searched', true)
                ->where('match.id', $friend->id)
                ->where('match.name', 'Priya Friend')
                ->missing('match.email')
                ->missing('match.mobile'));
    }

    public function test_user_can_find_someone_by_phone_even_with_formatting(): void
    {
        $me = User::factory()->create();
        $friend = User::factory()->create(['mobile' => '9876543210']);

        $this->actingAs($me)
            ->get('/people?q='.urlencode('98765 432-10'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('match.id', $friend->id));
    }

    public function test_user_can_find_someone_by_username(): void
    {
        $me = User::factory()->create();
        $friend = User::factory()->create(['username' => 'sarah-james']);

        // Bare, @-prefixed, and differently-cased forms all resolve.
        foreach (['sarah-james', '@sarah-james', 'Sarah-James'] as $q) {
            $this->actingAs($me)
                ->get('/people?q='.urlencode($q))
                ->assertOk()
                ->assertInertia(fn ($page) => $page
                    ->where('match.id', $friend->id)
                    ->where('match.username', 'sarah-james'));
        }
    }

    public function test_partial_email_does_not_match(): void
    {
        $me = User::factory()->create();
        User::factory()->create(['email' => 'priya@example.com']);

        $this->actingAs($me)
            ->get('/people?q=priya')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('searched', true)
                ->where('match', null));
    }

    public function test_hidden_profiles_are_not_findable(): void
    {
        $me = User::factory()->create();
        User::factory()->create(['email' => 'ghost@example.com', 'profile_visible' => false]);

        $this->actingAs($me)
            ->get('/people?q=ghost@example.com')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('match', null));
    }

    public function test_hidden_profile_page_is_404(): void
    {
        $ghost = User::factory()->create(['profile_visible' => false]);

        $this->get("/u/{$ghost->id}")->assertNotFound();
    }

    public function test_empty_query_shows_blank_search_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/people')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('searched', false)
                ->where('match', null));
    }
}
