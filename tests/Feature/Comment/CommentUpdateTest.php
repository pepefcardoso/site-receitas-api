<?php

namespace Tests\Feature\Comment;

use App\Enum\RolesEnum;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CommentUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_dono_do_comentario_pode_atualiza_lo(): void
    {
        // Arrange
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->forCommentable($post)->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        // Act
        $response = $this->putJson("/api/comments/{$comment->id}", ['content' => 'Conteúdo atualizado.']);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'content' => 'Conteúdo atualizado.']);
    }

    #[Test]
    public function test_admin_pode_atualizar_comentario_de_outro_usuario(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => RolesEnum::ADMIN]);
        $customer = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->forCommentable($post)->create(['user_id' => $customer->id]);
        Sanctum::actingAs($admin);

        // Act
        $response = $this->putJson("/api/comments/{$comment->id}", ['content' => 'Editado pelo admin.']);

        // Assert
        $response->assertStatus(200);
    }

    #[Test]
    public function test_usuario_nao_pode_atualizar_comentario_de_outro(): void
    {
        // Arrange
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $post = Post::factory()->create();
        $commentOfUserB = Comment::factory()->forCommentable($post)->create(['user_id' => $userB->id]);
        Sanctum::actingAs($userA);

        // Act
        $response = $this->putJson("/api/comments/{$commentOfUserB->id}", ['content' => 'Tentativa de invasão']);

        // Assert
        $response->assertStatus(403);
    }
}
