<?php

namespace Tests\Feature\NewsletterCustomer;

use App\Models\NewsletterCustomer;
use App\Notifications\CreateNewsletterCustomerNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NewsletterCustomerStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_qualquer_usuario_pode_se_inscrever_na_newsletter(): void
    {
        // Arrange
        Notification::fake();
        $email = 'novo.inscrito@email.com';

        // Act
        $response = $this->postJson('/api/newsletter', ['email' => $email]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('newsletter_customers', ['email' => $email]);
        Notification::assertSentOnDemand(CreateNewsletterCustomerNotification::class);
    }

    #[Test]
    public function test_inscricao_com_email_duplicado_retorna_erro_de_validacao(): void
    {
        // Arrange
        $existingCustomer = NewsletterCustomer::factory()->create();

        // Act
        $response = $this->postJson('/api/newsletter', ['email' => $existingCustomer->email]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function test_inscricao_com_email_invalido_falha(): void
    {
        // Act
        $response = $this->postJson('/api/newsletter', ['email' => 'email-invalido']);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
