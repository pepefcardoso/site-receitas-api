<?php

namespace Tests\Feature\Company;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyListTest extends TestCase
{
    use CompanyTestSetup, RefreshDatabase;

    public function test_authenticated_user_can_list_companies(): void
    {
        Company::factory(5)->create();

        $response = $this->actingAsRegularUser()->getJson('/api/companies');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'cnpj', 'email', 'phone', 'address', 'website', 'created_at'],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_list_companies_is_paginated(): void
    {
        Company::factory(25)->create();

        $response = $this->actingAsRegularUser()->getJson('/api/companies?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 25)
            ->assertJsonPath('meta.per_page', 10);
    }

    public function test_list_companies_can_be_filtered_by_name(): void
    {
        Company::factory()->create(['name' => 'My Awesome Company']);
        Company::factory(5)->create();

        $response = $this->actingAsRegularUser()->getJson('/api/companies?search=Awesome Company');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'My Awesome Company');
    }

    public function test_unauthenticated_user_cannot_list_companies(): void
    {
        $response = $this->getJson('/api/companies');
        $response->assertStatus(401);
    }
}
