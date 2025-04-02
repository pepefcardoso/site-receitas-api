<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_example()
    {
        $response = $this->get('/api/posts');

        $response->assertJsonCount(10, 'data');

        Log::info('Response data:', $response->json());

        $response->assertStatus(200);
    }
}
