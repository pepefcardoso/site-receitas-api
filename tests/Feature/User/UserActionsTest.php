<?php

namespace Tests\Feature\User;

use App\Enum\RolesEnum;
use App\Models\Post;
use App\Models\Recipe;
use App\Models\User;
use App\Notifications\DeletedUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserActionsTest extends TestCase
{
    use RefreshDatabase;

    // --- Testes para destroy ---

    #[Test]
    public function test_admin_pode_deletar_outro_usuario(): void
    {
        // Arrange
        Notification::fake();
        $admin = User::factory()->create(['role' => RolesEnum::ADMIN]);
        $customer = User::factory()->create();
        Sanctum::actingAs($admin);

        // Act
        $response = $this->deleteJson("/api/users/{$customer->id}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $customer->id]);

        // CORREÇÃO: Use assertSentOnDemand para verificar notificações "On-Demand"
        Notification::assertSentOnDemand(DeletedUser::class);
    }

    #[Test]
    public function test_usuario_pode_deletar_a_propria_conta(): void
    {
        // Arrange
        Notification::fake();
        $customer = User::factory()->create();
        Sanctum::actingAs($customer);

        // Act
        $response = $this->deleteJson("/api/users/{$customer->id}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $customer->id]);

        // CORREÇÃO: Use assertSentOnDemand aqui também para consistência
        Notification::assertSentOnDemand(DeletedUser::class);
    }

    #[Test]
    public function test_usuario_nao_pode_deletar_outra_conta(): void
    {
        // Arrange
        $customerA = User::factory()->create();
        $customerB = User::factory()->create();
        Sanctum::actingAs($customerA);

        // Act
        $response = $this->deleteJson("/api/users/{$customerB->id}");

        // Assert
        $response->assertStatus(403);
    }

    // --- Teste para authUser ---

    #[Test]
    public function test_usuario_autenticado_pode_buscar_seus_proprios_dados(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/users/me');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $user->id);
        $response->assertJsonPath('data.email', $user->email);
    }

    // --- Testes para updateRole ---

    #[Test]
    public function test_admin_pode_atualizar_a_role_de_um_usuario(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => RolesEnum::ADMIN]);
        $customer = User::factory()->create(['role' => RolesEnum::CUSTOMER]);
        Sanctum::actingAs($admin);

        $updateData = [
            'user_id' => $customer->id,
            'role' => RolesEnum::INTERNAL->value,
        ];

        $response = $this->patchJson("/api/users/{$customer->id}/role", $updateData);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'role' => RolesEnum::INTERNAL->value
        ]);
        $response->assertJsonPath('data.role', RolesEnum::INTERNAL->name);
    }

    #[Test]
    public function test_cliente_nao_pode_atualizar_a_role_de_um_usuario(): void
    {
        // Arrange
        $customerA = User::factory()->create();
        $customerB = User::factory()->create();
        Sanctum::actingAs($customerA);

        // Act
        $response = $this->patchJson("/api/users/{$customerB->id}/role", [
            'user_id' => $customerB->id,
            'role' => RolesEnum::ADMIN->value,
        ]);

        // Assert
        $response->assertStatus(403);
    }

    // --- Testes para toggleFavorite ---

    #[Test]
    public function test_usuario_pode_favoritar_e_desfavoritar_um_post(): void
    {
        // Arrange
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Sanctum::actingAs($user);

        // Act & Assert 1: Favorite
        $this->postJson('/api/users/favorites/posts', ['post_id' => $post->id])
            ->assertStatus(200);
        $this->assertDatabaseHas('rl_user_favorite_posts', ['user_id' => $user->id, 'post_id' => $post->id]);

        // Act & Assert 2: Unfavorite
        $this->postJson('/api/users/favorites/posts', ['post_id' => $post->id])
            ->assertStatus(200);
        $this->assertDatabaseMissing('rl_user_favorite_posts', ['user_id' => $user->id, 'post_id' => $post->id]);
    }

    #[Test]
    public function test_usuario_pode_favoritar_e_desfavoritar_uma_receita(): void
    {
        // Arrange
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        Sanctum::actingAs($user);

        // Act & Assert 1: Favorite
        $this->postJson('/api/users/favorites/recipes', ['recipe_id' => $recipe->id])
            ->assertStatus(200);
        $this->assertDatabaseHas('rl_user_favorite_recipes', ['user_id' => $user->id, 'recipe_id' => $recipe->id]);

        // Act & Assert 2: Unfavorite
        $this->postJson('/api/users/favorites/recipes', ['recipe_id' => $recipe->id])
            ->assertStatus(200);
        $this->assertDatabaseMissing('rl_user_favorite_recipes', ['user_id' => $user->id, 'recipe_id' => $recipe->id]);
    }
}
