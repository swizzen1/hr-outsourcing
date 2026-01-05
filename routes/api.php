<?php

use App\Http\Controllers\Api\CompanyVacancyController;
use App\Http\Controllers\Api\CompanyAbsenceController;
use App\Http\Controllers\Api\CompanyLeaveRequestApprovalController;
use App\Http\Controllers\Api\MeLeaveRequestController;
use App\Http\Controllers\Api\MeAttendanceController;
use App\Http\Controllers\Api\PublicVacancyController;
use App\Models\Company;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/public')->group(function () {
    Route::get('vacancies', [PublicVacancyController::class, 'index']);
    Route::get('vacancies/{id}', [PublicVacancyController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'company.access'])->get('/v1/companies/{company}/ping', function (Company $company) {
    return response()->json([
        'company_id' => $company->id,
        'status' => 'ok',
    ]);
});

Route::middleware(['auth:sanctum', 'company.access'])->post('/v1/companies/{company}/vacancies', [CompanyVacancyController::class, 'store']);

Route::middleware(['auth:sanctum', 'role:Employee|Company Admin|HR|Admin'])->group(function () {
    Route::post('/v1/me/attendance/check-in', [MeAttendanceController::class, 'checkIn']);
    Route::post('/v1/me/attendance/check-out', [MeAttendanceController::class, 'checkOut']);
    Route::post('/v1/me/leave-requests', [MeLeaveRequestController::class, 'store']);
    Route::get('/v1/me/leave-requests', [MeLeaveRequestController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'company.access'])->group(function () {
    Route::patch('/v1/companies/{company}/leave-requests/{leaveRequest}/approve', [CompanyLeaveRequestApprovalController::class, 'approve']);
    Route::patch('/v1/companies/{company}/leave-requests/{leaveRequest}/reject', [CompanyLeaveRequestApprovalController::class, 'reject']);
    Route::post('/v1/companies/{company}/absences', [CompanyAbsenceController::class, 'store']);
    Route::get('/v1/companies/{company}/absences', [CompanyAbsenceController::class, 'index']);
});
