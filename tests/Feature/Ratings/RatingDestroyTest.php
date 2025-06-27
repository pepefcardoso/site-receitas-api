<?php

namespace Tests\Feature\Rating;

use App\Models\Post;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RatingDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_delete_rating()
    {
        $post = Post::factory()->create();
        $rating = Rating::factory()->forRateable($post)->create();

        Sanctum::actingAs($rating->user);

        $response = $this->deleteJson("/api/ratings/{$rating->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('ratings', ['id' => $rating->id]);
    }

    public function test_non_owner_cannot_delete_rating()
    {
        $post = Post::factory()->create();
        $rating = Rating::factory()->forRateable($post)->create();
        $other = User::factory()->create();
        Sanctum::actingAs($other);

        $response = $this->deleteJson("/api/ratings/{$rating->id}");
        $response->assertStatus(403);
    }
}
