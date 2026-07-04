<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_with_stats_and_no_events(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Dashboard')
                ->where('stats.events', 0)
                ->where('stats.orders', 0)
                ->has('recentEvents', 0));
    }

    public function test_dashboard_lists_recent_events(): void
    {
        $user = User::factory()->create();
        Event::create(['user_id' => $user->id, 'title' => 'Our Wedding', 'type' => 'wedding', 'status' => 'draft']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('stats.events', 1)
                ->has('recentEvents', 1)
                ->where('recentEvents.0.title', 'Our Wedding'));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
