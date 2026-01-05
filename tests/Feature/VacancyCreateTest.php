<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VacancyCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-01-05 10:00:00'));
        $this->seed();
    }

    public function test_employee_cannot_create(): void
    {
        $employee = User::role('Employee')->firstOrFail();
        $company = Company::findOrFail($employee->company_id);

        Sanctum::actingAs($employee);

        $this->postJson("/api/v1/companies/{$company->id}/vacancies", $this->validPayload())
            ->assertStatus(403);
    }

    public function test_company_admin_can_create_within_own_company(): void
    {
        $companyAdmin = User::role('Company Admin')->firstOrFail();
        $company = Company::findOrFail($companyAdmin->company_id);

        Sanctum::actingAs($companyAdmin);

        $response = $this->postJson("/api/v1/companies/{$company->id}/vacancies", $this->validPayload());

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'title', 'description', 'location', 'employment_type', 'published_at', 'expiration_date']]);
    }

    public function test_company_admin_cannot_create_for_other_company(): void
    {
        $companyAdmin = User::role('Company Admin')->firstOrFail();
        $otherCompany = Company::where('id', '!=', $companyAdmin->company_id)->firstOrFail();

        Sanctum::actingAs($companyAdmin);

        $this->postJson("/api/v1/companies/{$otherCompany->id}/vacancies", $this->validPayload())
            ->assertStatus(403);
    }

    public function test_hr_and_admin_can_create_for_any_company(): void
    {
        $company = Company::firstOrFail();

        $hr = User::role('HR')->firstOrFail();
        Sanctum::actingAs($hr);
        $this->postJson("/api/v1/companies/{$company->id}/vacancies", $this->validPayload())
            ->assertStatus(201);

        $admin = User::role('Admin')->firstOrFail();
        Sanctum::actingAs($admin);
        $this->postJson("/api/v1/companies/{$company->id}/vacancies", $this->validPayload())
            ->assertStatus(201);
    }

    public function test_draft_sets_published_at_null_and_not_public(): void
    {
        $companyAdmin = User::role('Company Admin')->firstOrFail();
        $company = Company::findOrFail($companyAdmin->company_id);

        Sanctum::actingAs($companyAdmin);

        $payload = $this->validPayload(['status' => 'draft', 'published_at' => Carbon::now()->subDay()->toISOString()]);

        $response = $this->postJson("/api/v1/companies/{$company->id}/vacancies", $payload);

        $response->assertStatus(201);
        $this->assertNull($response->json('data.published_at'));

        $vacancyId = $response->json('data.id');
        $this->getJson('/api/v1/public/vacancies')
            ->assertOk()
            ->assertJsonMissing(['id' => $vacancyId]);
    }

    public function test_published_without_published_at_sets_now_and_is_public(): void
    {
        $companyAdmin = User::role('Company Admin')->firstOrFail();
        $company = Company::findOrFail($companyAdmin->company_id);

        Sanctum::actingAs($companyAdmin);

        $payload = $this->validPayload(['status' => 'published', 'published_at' => null]);

        $response = $this->postJson("/api/v1/companies/{$company->id}/vacancies", $payload);

        $response->assertStatus(201);
        $this->assertSame(Carbon::now()->toISOString(), $response->json('data.published_at'));

        $vacancyId = $response->json('data.id');
        $this->getJson('/api/v1/public/vacancies')
            ->assertOk()
            ->assertJsonFragment(['id' => $vacancyId]);
    }

    public function test_published_with_future_published_at_returns_422(): void
    {
        $companyAdmin = User::role('Company Admin')->firstOrFail();
        $company = Company::findOrFail($companyAdmin->company_id);

        Sanctum::actingAs($companyAdmin);

        $payload = $this->validPayload([
            'status' => 'published',
            'published_at' => Carbon::now()->addDay()->toISOString(),
        ]);

        $this->postJson("/api/v1/companies/{$company->id}/vacancies", $payload)
            ->assertStatus(422);
    }

    public function test_expiration_date_in_past_returns_422(): void
    {
        $companyAdmin = User::role('Company Admin')->firstOrFail();
        $company = Company::findOrFail($companyAdmin->company_id);

        Sanctum::actingAs($companyAdmin);

        $payload = $this->validPayload([
            'expiration_date' => Carbon::now()->subDay()->toDateString(),
        ]);

        $this->postJson("/api/v1/companies/{$company->id}/vacancies", $payload)
            ->assertStatus(422);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'QA Engineer',
            'description' => 'Test product quality end-to-end.',
            'location' => 'Remote',
            'employment_type' => 'contract',
            'status' => 'published',
            'published_at' => Carbon::now()->subMinute()->toISOString(),
            'expiration_date' => Carbon::now()->addDays(10)->toDateString(),
        ], $overrides);
    }
}
