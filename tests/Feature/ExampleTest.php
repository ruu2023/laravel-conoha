<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->withHeader('X-App-Subdomain', 'memo')->get('/');

        $response->assertStatus(200);
    }

    public function test_the_legacy_host_redirects_to_the_canonical_domain(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('https://memo.ruu-dev.com/');
    }
}
