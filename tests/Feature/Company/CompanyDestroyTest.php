<?php

namespace Tests\Feature\Company;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyDestroyTest extends TestCase
{
    use CompanyTestSetup, RefreshDatabase;

    public function test_admin_can_destroy_a_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAsAdmin()->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_user_who_created_the_company_can_destroy_it(): void
    {
        $company = Company::factory()->create(['user_id' => $this->regularUser->id]);

        $response = $this->actingAsRegularUser()->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_unauthorized_user_cannot_destroy_a_company(): void
    {
        $anotherUser = \App\Models\User::factory()->create();
        $company = Company::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAsRegularUser()->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(403);
    }
}
