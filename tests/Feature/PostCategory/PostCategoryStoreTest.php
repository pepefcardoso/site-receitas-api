<?php

namespace Tests\Feature\PostCategory;

use App\Enum\RolesEnum;
use App\Models\PostCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Str;

class PostCategoryStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_admin_pode_criar_nova_categoria(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => RolesEnum::ADMIN]);
        Sanctum::actingAs($admin);

        $name = 'Nova Categoria';
        $categoryData = ['name' => $name];

        // Act
        $response = $this->postJson('/api/post-categories', $categoryData);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('post_categories', [
            'name' => $name,
            'normalized_name' => Str::upper($name)
        ]);
        $response->assertJsonPath('data.name', $name);
    }

    #[Test]
    public function test_cliente_nao_pode_criar_categoria(): void
    {
        // Arrange
        $customer = User::factory()->create(['role' => RolesEnum::CUSTOMER]);
        Sanctum::actingAs($customer);

        // Act
        $response = $this->postJson('/api/post-categories', ['name' => 'Tentativa Falha']);

        // Assert
        $response->assertStatus(403);
    }

    #[Test]
    public function test_criar_categoria_com_nome_duplicado_retorna_erro_validacao(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => RolesEnum::ADMIN]);
        $existingCategory = PostCategory::factory()->create();
        Sanctum::actingAs($admin);

        // Act
        $response = $this->postJson('/api/post-categories', ['name' => $existingCategory->name]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function test_nao_autenticado_nao_pode_criar_categoria(): void
    {
        // Arrange: Nenhum usuário autenticado

        // Act
        $response = $this->postJson('/api/post-categories', ['name' => 'Tentativa sem Auth']);

        // Assert
        $response->assertStatus(401);
    }
}
