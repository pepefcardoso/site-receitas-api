<?php

namespace Tests\Feature\RecipeUnit;

use App\Models\RecipeUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RecipeUnitListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function test_endpoint_index_retorna_lista_de_unidades(): void
    {
        // Arrange
        RecipeUnit::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/recipe-units');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);
    }
}
