<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that the homepage redirects to the SIMS login screen.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // 1. Root URL redirects to login
        $response = $this->get('/');
        $response->assertRedirect('/login');

        // 2. Login screen returns 200 OK
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
    }
}