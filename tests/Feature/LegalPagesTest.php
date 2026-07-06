<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_is_public(): void
    {
        $this->get('/privacy')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Public/Legal/Privacy'));
    }

    public function test_terms_and_conditions_are_public(): void
    {
        $this->get('/terms')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Public/Legal/Terms'));
    }
}
