<?php

namespace Tests\Feature\Rating;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RatingStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_rating()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/posts/{$post->id}/ratings", ['rating' => 5]);

        $response->assertStatus(201)
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'rateable_id' => $post->id,
            'rateable_type' => get_class($post),
            'rating' => 5,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_rating()
    {
        $post = Post::factory()->create();
        $response = $this->postJson("/api/posts/{$post->id}/ratings", ['rating' => 4]);
        $response->assertStatus(401);
    }
}
