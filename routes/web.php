<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CompanyAbsenceController as WebCompanyAbsenceController;
use App\Http\Controllers\Web\CompanyLeaveRequestController as WebCompanyLeaveRequestController;
use App\Http\Controllers\Web\CompanyVacancyController as WebCompanyVacancyController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MeAttendanceController as WebMeAttendanceController;
use App\Http\Controllers\Web\MeLeaveRequestController as WebMeLeaveRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole(['Admin', 'HR'])) {
        return redirect()->route('dashboard.admin');
    }

    if ($user->hasRole('Company Admin')) {
        return redirect()->route('dashboard.company');
    }

    return redirect()->route('dashboard.employee');
})->middleware('auth')->name('dashboard.redirect');

Route::middleware(['auth', 'role:Admin|HR'])->group(function () {
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
});

Route::middleware(['auth', 'role:Company Admin'])->group(function () {
    Route::get('/company/dashboard', [DashboardController::class, 'company'])->name('dashboard.company');
});

Route::middleware(['auth', 'role:Employee'])->group(function () {
    Route::get('/me/dashboard', [DashboardController::class, 'employee'])->name('dashboard.employee');

    Route::get('/me/attendance', [WebMeAttendanceController::class, 'show'])->name('me.attendance.show');
    Route::post('/me/attendance/check-in', [WebMeAttendanceController::class, 'checkIn'])->name('me.attendance.checkin');
    Route::post('/me/attendance/check-out', [WebMeAttendanceController::class, 'checkOut'])->name('me.attendance.checkout');

    Route::get('/me/leave-requests', [WebMeLeaveRequestController::class, 'index'])->name('me.leave_requests.index');
    Route::get('/me/leave-requests/create', [WebMeLeaveRequestController::class, 'create'])->name('me.leave_requests.create');
    Route::post('/me/leave-requests', [WebMeLeaveRequestController::class, 'store'])->name('me.leave_requests.store');
});

Route::middleware(['auth', 'role:Admin|HR|Company Admin', 'company.access'])->group(function () {
    Route::get('/companies/{company}/vacancies', [WebCompanyVacancyController::class, 'index'])->name('company.vacancies.index');
    Route::get('/companies/{company}/vacancies/create', [WebCompanyVacancyController::class, 'create'])->name('company.vacancies.create');
    Route::post('/companies/{company}/vacancies', [WebCompanyVacancyController::class, 'store'])->name('company.vacancies.store');

    Route::get('/companies/{company}/leave-requests', [WebCompanyLeaveRequestController::class, 'index'])->name('company.leave_requests.index');
    Route::patch('/companies/{company}/leave-requests/{leaveRequest}/approve', [WebCompanyLeaveRequestController::class, 'approve'])->name('company.leave_requests.approve');
    Route::patch('/companies/{company}/leave-requests/{leaveRequest}/reject', [WebCompanyLeaveRequestController::class, 'reject'])->name('company.leave_requests.reject');

    Route::get('/companies/{company}/absences', [WebCompanyAbsenceController::class, 'index'])->name('company.absences.index');
    Route::get('/companies/{company}/absences/create', [WebCompanyAbsenceController::class, 'create'])->name('company.absences.create');
    Route::post('/companies/{company}/absences', [WebCompanyAbsenceController::class, 'store'])->name('company.absences.store');
});
