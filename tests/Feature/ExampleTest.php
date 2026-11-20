<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The root of the application sends visitors to the authenticated area (DEC-CORE-007).
     */
    public function test_the_application_redirects_the_root_to_the_home_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/inicio');
    }
}
