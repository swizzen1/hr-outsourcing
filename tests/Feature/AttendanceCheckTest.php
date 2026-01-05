<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-01-05 09:00:00'));
    }

    public function test_check_in_creates_record(): void
    {
        $user = $this->makeUser('Employee');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/attendance/check-in');

        $response->assertStatus(201);

        $attendance = Attendance::first();
        $this->assertNotNull($attendance);
        $this->assertSame($user->id, $attendance->user_id);
        $this->assertSame($user->company_id, $attendance->company_id);
        $this->assertSame(Carbon::now()->toDateString(), $attendance->date->toDateString());
        $this->assertNotNull($attendance->check_in_at);
    }

    public function test_double_check_in_returns_422(): void
    {
        $user = $this->makeUser('Employee');

        Sanctum::actingAs($user);
        $this->postJson('/api/v1/me/attendance/check-in')->assertStatus(201);

        $response = $this->postJson('/api/v1/me/attendance/check-in');
        $response->assertStatus(422);
        $this->assertSame('Already checked in today', $response->json('message'));
    }

    public function test_check_out_without_check_in_returns_422(): void
    {
        $user = $this->makeUser('Employee');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/attendance/check-out');
        $response->assertStatus(422);
        $this->assertSame('Must check in before checking out', $response->json('message'));
    }

    public function test_check_out_after_check_in_sets_check_out_at(): void
    {
        $user = $this->makeUser('Employee');

        Sanctum::actingAs($user);
        $this->postJson('/api/v1/me/attendance/check-in')->assertStatus(201);

        $response = $this->postJson('/api/v1/me/attendance/check-out');
        $response->assertOk();

        $attendance = Attendance::first();
        $this->assertNotNull($attendance->check_out_at);
    }

    public function test_double_check_out_returns_422(): void
    {
        $user = $this->makeUser('Employee');

        Sanctum::actingAs($user);
        $this->postJson('/api/v1/me/attendance/check-in')->assertStatus(201);
        $this->postJson('/api/v1/me/attendance/check-out')->assertOk();

        $response = $this->postJson('/api/v1/me/attendance/check-out');
        $response->assertStatus(422);
        $this->assertSame('Already checked out today', $response->json('message'));
    }

    public function test_attendance_uses_authenticated_user_company(): void
    {
        $user = $this->makeUser('Employee');
        $otherCompany = Company::create(['name' => 'Other Co', 'slug' => 'other-co']);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/me/attendance/check-in', [
            'user_id' => 999,
            'company_id' => $otherCompany->id,
        ]);

        $response->assertStatus(201);

        $attendance = Attendance::first();
        $this->assertSame($user->id, $attendance->user_id);
        $this->assertSame($user->company_id, $attendance->company_id);
    }

    private function makeUser(string $role): User
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'user'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
        ]);

        Role::firstOrCreate(['name' => $role, 'guard_name' => 'sanctum']);
        $user->assignRole($role);

        return $user;
    }
}
