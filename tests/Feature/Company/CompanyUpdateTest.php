<?php

namespace Tests\Feature\Company;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyUpdateTest extends TestCase
{
    use CompanyTestSetup, RefreshDatabase;

    public function test_admin_can_update_any_company(): void
    {
        $company = Company::factory()->create(['user_id' => $this->regularUser->id]);
        $updateData = ['name' => 'Updated by Admin'];

        $response = $this->actingAsAdmin()->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated by Admin']);

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'Updated by Admin']);
    }

    public function test_user_who_created_the_company_can_update_it(): void
    {
        $company = Company::factory()->create(['user_id' => $this->regularUser->id]);
        $updateData = ['name' => 'Updated by Owner'];

        $response = $this->actingAsRegularUser()->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated by Owner']);
    }

    public function test_unauthorized_user_cannot_update_a_company(): void
    {
        $anotherUser = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $anotherUser->id]);
        $updateData = ['name' => 'This Should Not Work'];

        $response = $this->actingAsRegularUser()->putJson("/api/companies/{$company->id}", $updateData);

        $response->assertStatus(403);
    }
}
