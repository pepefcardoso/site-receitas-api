<?php

namespace Tests\Feature\Company;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyShowTest extends TestCase
{
    use CompanyTestSetup, RefreshDatabase;

    public function test_authenticated_user_can_show_a_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAsRegularUser()->getJson("/api/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                ],
            ]);
    }

    public function test_show_returns_404_for_non_existent_company(): void
    {
        $response = $this->actingAsRegularUser()->getJson('/api/companies/999');
        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_show_a_company(): void
    {
        $company = Company::factory()->create();
        $response = $this->getJson("/api/companies/{$company->id}");
        $response->assertStatus(401);
    }
}
