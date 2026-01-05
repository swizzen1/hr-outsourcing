<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveRequestsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-01-05 09:00:00'));
    }

    public function test_employee_can_create_pending_leave_request(): void
    {
        $user = $this->makeUser('Employee');

        Sanctum::actingAs($user);

        $payload = [
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'reason' => 'Medical appointment',
            'type' => 'sick',
        ];

        $response = $this->postJson('/api/v1/me/leave-requests', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['status' => 'pending']);

        $leaveRequest = LeaveRequest::first();
        $this->assertSame($user->id, $leaveRequest->user_id);
        $this->assertSame($user->company_id, $leaveRequest->company_id);
    }

    public function test_employee_cannot_create_with_past_start_date(): void
    {
        $user = $this->makeUser('Employee');

        Sanctum::actingAs($user);

        $payload = [
            'start_date' => Carbon::now()->subDay()->toDateString(),
            'end_date' => Carbon::now()->addDay()->toDateString(),
        ];

        $this->postJson('/api/v1/me/leave-requests', $payload)
            ->assertStatus(422);
    }

    public function test_employee_list_returns_only_own_requests(): void
    {
        $user = $this->makeUser('Employee');
        $otherUser = $this->makeUser('Employee', 'other-co');

        LeaveRequest::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        LeaveRequest::create([
            'company_id' => $otherUser->company_id,
            'user_id' => $otherUser->id,
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/me/leave-requests');
        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('user_id')->unique()->all();
        $this->assertSame([$user->id], $ids);
    }

    public function test_company_admin_can_approve_pending_request_in_own_company(): void
    {
        $companyAdmin = $this->makeUser('Company Admin');
        $employee = $this->makeUser('Employee', $companyAdmin->company->slug, $companyAdmin->company_id);

        $leaveRequest = LeaveRequest::create([
            'company_id' => $employee->company_id,
            'user_id' => $employee->id,
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        Sanctum::actingAs($companyAdmin);

        $response = $this->patchJson("/api/v1/companies/{$employee->company_id}/leave-requests/{$leaveRequest->id}/approve");

        $response->assertOk();
        $response->assertJsonFragment(['status' => 'approved']);
    }

    public function test_company_admin_cannot_approve_other_company(): void
    {
        $companyAdmin = $this->makeUser('Company Admin');
        $otherCompany = Company::create(['name' => 'Other Co', 'slug' => 'other-co']);
        $otherUser = $this->makeUser('Employee', $otherCompany->slug, $otherCompany->id);

        $leaveRequest = LeaveRequest::create([
            'company_id' => $otherUser->company_id,
            'user_id' => $otherUser->id,
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        Sanctum::actingAs($companyAdmin);

        $response = $this->patchJson("/api/v1/companies/{$otherCompany->id}/leave-requests/{$leaveRequest->id}/approve");
        $this->assertTrue(in_array($response->status(), [403, 404], true));
    }

    public function test_hr_and_admin_can_approve_across_companies(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);
        $employee = $this->makeUser('Employee', $company->slug, $company->id);

        $leaveRequest = LeaveRequest::create([
            'company_id' => $employee->company_id,
            'user_id' => $employee->id,
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        $hr = $this->makeUser('HR', 'global-hr', null);
        Sanctum::actingAs($hr);
        $this->patchJson("/api/v1/companies/{$company->id}/leave-requests/{$leaveRequest->id}/approve")
            ->assertOk();

        $leaveRequest->refresh();
        $leaveRequest->status = 'pending';
        $leaveRequest->decided_by = null;
        $leaveRequest->decided_at = null;
        $leaveRequest->save();

        $admin = $this->makeUser('Admin', 'global-admin', null);
        Sanctum::actingAs($admin);
        $this->patchJson("/api/v1/companies/{$company->id}/leave-requests/{$leaveRequest->id}/approve")
            ->assertOk();
    }

    public function test_employee_cannot_approve_or_reject(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);
        $employee = $this->makeUser('Employee', $company->slug, $company->id);

        $leaveRequest = LeaveRequest::create([
            'company_id' => $employee->company_id,
            'user_id' => $employee->id,
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        Sanctum::actingAs($employee);

        $this->patchJson("/api/v1/companies/{$company->id}/leave-requests/{$leaveRequest->id}/approve")
            ->assertStatus(403);
        $this->patchJson("/api/v1/companies/{$company->id}/leave-requests/{$leaveRequest->id}/reject")
            ->assertStatus(403);
    }

    public function test_approve_or_reject_non_pending_returns_422(): void
    {
        $companyAdmin = $this->makeUser('Company Admin');
        $employee = $this->makeUser('Employee', $companyAdmin->company->slug, $companyAdmin->company_id);

        $approved = LeaveRequest::create([
            'company_id' => $employee->company_id,
            'user_id' => $employee->id,
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'approved',
        ]);

        $rejected = LeaveRequest::create([
            'company_id' => $employee->company_id,
            'user_id' => $employee->id,
            'start_date' => Carbon::now()->addDay()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'rejected',
        ]);

        Sanctum::actingAs($companyAdmin);

        $this->patchJson("/api/v1/companies/{$employee->company_id}/leave-requests/{$approved->id}/approve")
            ->assertStatus(422);
        $this->patchJson("/api/v1/companies/{$employee->company_id}/leave-requests/{$rejected->id}/reject")
            ->assertStatus(422);
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
