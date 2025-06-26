<?php
// tests/Feature/PostTopic/PostTopicShowTest.php

namespace Tests\Feature\PostTopic;

use App\Models\PostTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PostTopicShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_endpoint_show_retorna_topic_especifico(): void
    {
        $topic = PostTopic::factory()->create();

        $response = $this->getJson("/api/post-topics/{$topic->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $topic->id);
        $response->assertJsonPath('data.name', $topic->name);
    }

    #[Test]
    public function test_endpoint_show_retorna_404_para_topic_inexistente(): void
    {
        $response = $this->getJson('/api/post-topics/999');

        $response->assertStatus(404);
    }
}
