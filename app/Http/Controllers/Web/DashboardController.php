<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\LeaveRequest;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        $companies = Company::withCount(['users', 'vacancies'])->orderBy('name')->get();

        return view('dashboard.admin', [
            'companies' => $companies,
        ]);
    }

    public function company(Request $request)
    {
        $company = $request->user()->company;

        $pendingLeaves = LeaveRequest::query()
            ->where('company_id', $company->id)
            ->where('status', 'pending')
            ->count();

        $vacancies = Vacancy::query()
            ->where('company_id', $company->id)
            ->count();

        return view('dashboard.company', [
            'company' => $company,
            'pendingLeaves' => $pendingLeaves,
            'vacancies' => $vacancies,
        ]);
    }

    public function employee(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.employee', [
            'attendance' => $attendance,
            'leaveRequests' => $leaveRequests,
        ]);
    }
}
