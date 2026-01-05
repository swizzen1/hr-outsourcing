<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AbsenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-01-05 09:00:00'));
    }

    public function test_company_admin_can_create_absence_for_same_company(): void
    {
        $companyAdmin = $this->makeUser('Company Admin');
        $employee = $this->makeUser('Employee', $companyAdmin->company->slug, $companyAdmin->company_id);

        Sanctum::actingAs($companyAdmin);

        $response = $this->postJson("/api/v1/companies/{$companyAdmin->company_id}/absences", [
            'user_id' => $employee->id,
            'date' => Carbon::now()->toDateString(),
            'reason' => 'Sick',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['user_id' => $employee->id]);
    }

    public function test_company_admin_cannot_create_absence_for_other_company(): void
    {
        $companyAdmin = $this->makeUser('Company Admin');
        $otherCompany = Company::create(['name' => 'Other Co', 'slug' => 'other-co']);
        $otherEmployee = $this->makeUser('Employee', $otherCompany->slug, $otherCompany->id);

        Sanctum::actingAs($companyAdmin);

        $response = $this->postJson("/api/v1/companies/{$companyAdmin->company_id}/absences", [
            'user_id' => $otherEmployee->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response->assertStatus(422);
    }

    public function test_employee_cannot_create_absence(): void
    {
        $employee = $this->makeUser('Employee');

        Sanctum::actingAs($employee);

        $response = $this->postJson("/api/v1/companies/{$employee->company_id}/absences", [
            'user_id' => $employee->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_hr_and_admin_can_create_for_any_company(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);
        $employee = $this->makeUser('Employee', $company->slug, $company->id);

        $hr = $this->makeUser('HR', 'global-hr', null);
        Sanctum::actingAs($hr);
        $this->postJson("/api/v1/companies/{$company->id}/absences", [
            'user_id' => $employee->id,
            'date' => Carbon::now()->toDateString(),
        ])->assertStatus(201);

        $admin = $this->makeUser('Admin', 'global-admin', null);
        Sanctum::actingAs($admin);
        $this->postJson("/api/v1/companies/{$company->id}/absences", [
            'user_id' => $employee->id,
            'date' => Carbon::now()->addDay()->toDateString(),
        ])->assertStatus(201);
    }

    public function test_duplicate_same_user_date_returns_422(): void
    {
        $companyAdmin = $this->makeUser('Company Admin');
        $employee = $this->makeUser('Employee', $companyAdmin->company->slug, $companyAdmin->company_id);

        Absence::create([
            'company_id' => $companyAdmin->company_id,
            'user_id' => $employee->id,
            'date' => Carbon::now()->toDateString(),
            'created_by' => $companyAdmin->id,
        ]);

        Sanctum::actingAs($companyAdmin);

        $this->postJson("/api/v1/companies/{$companyAdmin->company_id}/absences", [
            'user_id' => $employee->id,
            'date' => Carbon::now()->toDateString(),
        ])->assertStatus(422);
    }

    public function test_list_returns_only_company_absences(): void
    {
        $companyAdmin = $this->makeUser('Company Admin');
        $employee = $this->makeUser('Employee', $companyAdmin->company->slug, $companyAdmin->company_id);

        Absence::create([
            'company_id' => $companyAdmin->company_id,
            'user_id' => $employee->id,
            'date' => Carbon::now()->toDateString(),
            'created_by' => $companyAdmin->id,
        ]);

        $otherCompany = Company::create(['name' => 'Other Co', 'slug' => 'other-co']);
        $otherEmployee = $this->makeUser('Employee', $otherCompany->slug, $otherCompany->id);
        Absence::create([
            'company_id' => $otherCompany->id,
            'user_id' => $otherEmployee->id,
            'date' => Carbon::now()->toDateString(),
            'created_by' => $companyAdmin->id,
        ]);

        Sanctum::actingAs($companyAdmin);

        $response = $this->getJson("/api/v1/companies/{$companyAdmin->company_id}/absences");
        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'meta' => ['current_page', 'per_page', 'total'],
        ]);

        $companyIds = collect($response->json('data'))->pluck('user_id')->all();
        $this->assertContains($employee->id, $companyIds);
        $this->assertNotContains($otherEmployee->id, $companyIds);
    }

    private function makeUser(string $role, ?string $companySlug = null, ?int $companyId = null): User
    {
        if ($companyId === null && $role !== 'HR' && $role !== 'Admin') {
            $slug = $companySlug ?? 'acme-corp';
            $company = Company::firstOrCreate(['slug' => $slug], ['name' => 'Acme Corp']);
        } elseif ($companyId !== null) {
            $company = Company::findOrFail($companyId);
        } else {
            $company = null;
        }

        $user = User::create([
            'name' => $role.' User',
            'email' => uniqid($role, true).'@example.com',
            'password' => Hash::make('password'),
            'company_id' => $company?->id,
        ]);

        Role::firstOrCreate(['name' => $role, 'guard_name' => 'sanctum']);
        $user->assignRole($role);

        return $user;
    }
}
