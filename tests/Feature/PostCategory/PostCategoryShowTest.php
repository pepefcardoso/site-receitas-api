<?php

namespace Tests\Feature\PostCategory;

use App\Models\PostCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PostCategoryShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_endpoint_show_retorna_categoria_especifica(): void
    {
        // Arrange
        $category = PostCategory::factory()->create();

        // Act
        $response = $this->getJson("/api/post-categories/{$category->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $category->id);
        $response->assertJsonPath('data.name', $category->name);
    }

    #[Test]
    public function test_endpoint_show_retorna_404_para_categoria_inexistente(): void
    {
        // Act
        $response = $this->getJson('/api/post-categories/999');

        // Assert
        $response->assertStatus(404);
    }
}
