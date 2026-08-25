<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);
    }

    /**
     * Build a fake Google profile as Socialite would hand it back.
     */
    private function fakeGoogleUser(array $overrides = []): SocialiteUser
    {
        $user = new SocialiteUser();
        $user->map(array_merge([
            'id' => '1234567890',
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'avatar' => 'https://lh3.googleusercontent.com/a/avatar.png',
        ], $overrides));
        $user->token = 'fake-access-token';
        $user->refreshToken = 'fake-refresh-token';
        $user->user = array_merge(['email_verified' => true], $overrides['raw'] ?? []);

        return $user;
    }

    private function mockSocialiteReturning(SocialiteUser $user): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_redirect_sends_the_user_to_google(): void
    {
        $this->get('/auth/google')->assertRedirectContains('accounts.google.com');
    }

    public function test_a_new_google_user_is_created_and_signed_in(): void
    {
        $this->mockSocialiteReturning($this->fakeGoogleUser());

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'priya@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('1234567890', $user->google_id);
        $this->assertSame('Priya Sharma', $user->name);

        // email_verified_at is not mass-assignable, so this asserts it was
        // actually set rather than silently dropped.
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_an_existing_google_user_is_matched_not_duplicated(): void
    {
        $existing = User::factory()->create([
            'email' => 'priya@example.com',
        ]);
        $existing->forceFill(['google_id' => '1234567890'])->save();

        $this->mockSocialiteReturning($this->fakeGoogleUser());

        $this->get('/auth/google/callback')->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($existing->fresh());
        $this->assertSame(1, User::where('email', 'priya@example.com')->count());
    }

    public function test_a_password_account_can_be_linked_when_google_verified_the_email(): void
    {
        $existing = User::factory()->create(['email' => 'priya@example.com']);

        $this->mockSocialiteReturning($this->fakeGoogleUser());

        $this->get('/auth/google/callback')->assertRedirect(route('dashboard'));

        $this->assertSame('1234567890', $existing->fresh()->google_id);
        $this->assertSame(1, User::count());
    }

    public function test_an_unverified_google_email_cannot_take_over_an_existing_account(): void
    {
        $existing = User::factory()->create(['email' => 'priya@example.com']);

        $this->mockSocialiteReturning(
            $this->fakeGoogleUser(['raw' => ['email_verified' => false]])
        );

        $response = $this->get('/auth/google/callback');

        // The existing account must not have been hijacked or signed into.
        $this->assertNull($existing->fresh()->google_id);
        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertSame(1, User::count());
    }

    public function test_a_failed_callback_redirects_to_login_instead_of_erroring(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andThrow(new \RuntimeException('invalid state'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_google_sign_in_degrades_gracefully_when_not_configured(): void
    {
        config(['services.google.client_id' => null, 'services.google.client_secret' => null]);

        $response = $this->get('/auth/google');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_a_deactivated_account_cannot_sign_in_with_google(): void
    {
        $user = User::factory()->create(['email' => 'priya@example.com']);
        $user->forceFill(['google_id' => '1234567890', 'is_active' => false])->save();

        $this->mockSocialiteReturning($this->fakeGoogleUser());

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
