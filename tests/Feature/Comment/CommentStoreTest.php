<?php

namespace Tests\Feature\Comment;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CommentStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_usuario_autenticado_pode_criar_comentario_em_um_post(): void
    {
        // Arrange
        $user = User::factory()->create();
        $post = Post::factory()->create();
        Sanctum::actingAs($user);
        $commentData = ['content' => 'Ótimo post, muito informativo!'];

        // Act
        $response = $this->postJson("/api/posts/{$post->id}/comments", $commentData);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => get_class($post),
            'content' => 'Ótimo post, muito informativo!',
        ]);
        $response->assertJsonPath('data.content', 'Ótimo post, muito informativo!');
    }

    #[Test]
    public function test_usuario_nao_autenticado_nao_pode_comentar(): void
    {
        // Arrange
        $post = Post::factory()->create();
        $commentData = ['content' => 'Tentativa sem login'];

        // Act
        $response = $this->postJson("/api/posts/{$post->id}/comments", $commentData);

        // Assert
        $response->assertStatus(401);
    }
}
