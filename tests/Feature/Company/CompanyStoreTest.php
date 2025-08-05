<?php

namespace Tests\Feature\Company;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyStoreTest extends TestCase
{
    use CompanyTestSetup, RefreshDatabase;

    public function test_admin_can_store_a_company(): void
    {
        $companyData = [
            'name' => 'New Tech Inc.',
            'cnpj' => '99.073.876/0001-28',
            'email' => 'contact@newtech.com',
            'phone' => '11987654321',
            'address' => '456 Innovation Ave',
            'website' => 'https://newtech.com',
        ];

        $response = $this->actingAsAdmin()->postJson('/api/companies', $companyData);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'New Tech Inc.']);

        $this->assertDatabaseHas('companies', ['name' => 'New Tech Inc.', 'user_id' => $this->adminUser->id]);
    }

    public function test_regular_user_cannot_store_a_company(): void
    {
        $companyData = \App\Models\Company::factory()->make()->toArray();
        $response = $this->actingAsRegularUser()->postJson('/api/companies', $companyData);
        $response->assertStatus(403);
    }

    public function test_store_company_validation_fails_for_missing_data(): void
    {
        $response = $this->actingAsAdmin()->postJson('/api/companies', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'cnpj', 'email', 'phone', 'address', 'website']);
    }
}
