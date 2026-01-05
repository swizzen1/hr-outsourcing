<?php

namespace App\Providers;

use App\Models\Absence;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\Vacancy;
use App\Policies\AbsencePolicy;
use App\Policies\AttendancePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\LeaveRequestPolicy;
use App\Policies\PositionPolicy;
use App\Policies\VacancyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Position::class => PositionPolicy::class,
        Vacancy::class => VacancyPolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
        Absence::class => AbsencePolicy::class,
        Attendance::class => AttendancePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
