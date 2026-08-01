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

    public function test_the_legacy_host_shows_the_shared_login_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Googleでログイン');
    }
}
