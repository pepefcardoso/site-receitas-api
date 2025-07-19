<?php

namespace Tests\Feature\Rating;

use App\Models\Post;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_view_single_rating()
    {
        $post = Post::factory()->create();
        $rating = Rating::factory()->forRateable($post)->create();

        $response = $this->getJson("/api/ratings/{$rating->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $rating->id)
            ->assertJsonStructure(['data' => ['id', 'rating', 'author']]);
    }
}
