<?php

namespace Tests\Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CommentDestroyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_dono_do_comentario_pode_deleta_lo(): void
    {
        // Arrange
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->forCommentable($post)->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        // Act
        $response = $this->deleteJson("/api/comments/{$comment->id}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    #[Test]
    public function test_usuario_nao_pode_deletar_comentario_de_outro(): void
    {
        // Arrange
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $post = Post::factory()->create();
        $commentOfUserB = Comment::factory()->forCommentable($post)->create(['user_id' => $userB->id]);
        Sanctum::actingAs($userA);

        // Act
        $response = $this->deleteJson("/api/comments/{$commentOfUserB->id}");

        // Assert
        $response->assertStatus(403);
    }
}
