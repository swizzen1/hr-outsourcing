<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_access_other_company(): void
    {
        $this->seed();

        $employee = User::role('Employee')->whereNotNull('company_id')->firstOrFail();
        $otherCompany = Company::where('id', '!=', $employee->company_id)->firstOrFail();

        Sanctum::actingAs($employee);

        $this->getJson("/api/v1/companies/{$otherCompany->id}/ping")
            ->assertStatus(403);
    }

    public function test_hr_and_admin_can_access_all_companies(): void
    {
        $this->seed();

        $company = Company::firstOrFail();

        $hr = User::role('HR')->firstOrFail();
        Sanctum::actingAs($hr);
        $this->getJson("/api/v1/companies/{$company->id}/ping")
            ->assertOk();

        $admin = User::role('Admin')->firstOrFail();
        Sanctum::actingAs($admin);
        $this->getJson("/api/v1/companies/{$company->id}/ping")
            ->assertOk();
    }
}
