<?php

namespace Tests\Feature\Rating;

use App\Models\Post;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RatingUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_rating()
    {
        $post = Post::factory()->create();
        $rating = Rating::factory()->forRateable($post)->create();
        Sanctum::actingAs($rating->user);

        $response = $this->putJson("/api/ratings/{$rating->id}", ['rating' => 2]);

        $response->assertStatus(200)
            ->assertJsonPath('data.rating', 2);

        $this->assertDatabaseHas('ratings', ['id' => $rating->id, 'rating' => 2]);
    }

    public function test_non_owner_cannot_update_rating()
    {
        $post = Post::factory()->create();
        $rating = Rating::factory()->forRateable($post)->create();
        $other = User::factory()->create();
        Sanctum::actingAs($other);

        $response = $this->putJson("/api/ratings/{$rating->id}", ['rating' => 3]);
        $response->assertStatus(403);
    }
}
