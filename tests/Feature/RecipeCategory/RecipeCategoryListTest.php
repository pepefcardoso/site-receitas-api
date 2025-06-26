<?php

namespace Tests\Feature\RecipeCategory;

use App\Models\RecipeCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RecipeCategoryListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function test_endpoint_index_retorna_lista_de_categorias(): void
    {
        // Arrange
        RecipeCategory::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/recipe-categories');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);
    }

    #[Test]
    public function test_endpoint_index_retorna_array_vazio_se_nao_ha_categorias(): void
    {
        // Act
        $response = $this->getJson('/api/recipe-categories');

        // Assert
        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }
}
