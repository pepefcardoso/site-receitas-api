<?php

namespace Tests\Feature\RecipeDiet;

use App\Models\RecipeDiet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RecipeDietListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Limpa o cache para garantir um teste limpo
        Cache::flush();
    }

    #[Test]
    public function test_endpoint_index_retorna_lista_de_dietas(): void
    {
        // Arrange
        RecipeDiet::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/recipe-diets');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);
    }

    #[Test]
    public function test_endpoint_index_retorna_array_vazio_se_nao_ha_dietas(): void
    {
        // Act
        $response = $this->getJson('/api/recipe-diets');

        // Assert
        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }
}
