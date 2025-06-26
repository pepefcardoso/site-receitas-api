<?php

namespace Tests\Feature\RecipeDiet;

use App\Models\RecipeDiet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RecipeDietShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_endpoint_show_retorna_dieta_especifica(): void
    {
        // Arrange
        $diet = RecipeDiet::factory()->create();

        // Act
        $response = $this->getJson("/api/recipe-diets/{$diet->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $diet->id)
            ->assertJsonPath('data.name', $diet->name);
    }

    #[Test]
    public function test_endpoint_show_retorna_404_para_dieta_inexistente(): void
    {
        // Act
        $response = $this->getJson('/api/recipe-diets/999');

        // Assert
        $response->assertStatus(404);
    }
}
