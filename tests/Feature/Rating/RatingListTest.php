<?php

namespace Tests\Feature\Rating;

use App\Models\Post;
use App\Models\Recipe;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingListTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_list_post_ratings()
    {
        $post = Post::factory()->create();
        Rating::factory()->count(4)->forRateable($post)->create();

        $response = $this->getJson("/api/posts/{$post->id}/ratings");

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data')
            ->assertJsonStructure(['data' => [['id', 'rating', 'author']]]);
    }

    public function test_anyone_can_list_recipe_ratings()
    {
        $recipe = Recipe::factory()->create();
        Rating::factory()->count(2)->forRateable($recipe)->create();

        $response = $this->getJson("/api/recipes/{$recipe->id}/ratings");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
