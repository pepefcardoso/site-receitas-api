<?php
// tests/Feature/PostTopic/PostTopicListTest.php

namespace Tests\Feature\PostTopic;

use App\Models\PostTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PostTopicListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_endpoint_index_retorna_lista_de_topics(): void
    {
        PostTopic::factory()->count(3)->create();

        $response = $this->getJson('/api/post-topics');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => ['*' => ['id', 'name']]
        ]);
    }

    #[Test]
    public function test_endpoint_index_retorna_array_vazio_se_nao_ha_topics(): void
    {
        $response = $this->getJson('/api/post-topics');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }
}
