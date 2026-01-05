<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Actions\LeaveRequests\ApproveLeaveRequest;
use App\Actions\LeaveRequests\RejectLeaveRequest;
use App\Actions\Support\ActionException;
use App\Models\Company;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyLeaveRequestController extends Controller
{
    public function index(Request $request, Company $company)
    {
        Gate::authorize('view', $company);

        $leaveRequests = LeaveRequest::query()
            ->where('company_id', $company->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('company.leave_requests.index', [
            'company' => $company,
            'leaveRequests' => $leaveRequests,
        ]);
    }

    public function approve(Request $request, Company $company, LeaveRequest $leaveRequest, ApproveLeaveRequest $approveLeaveRequest)
    {
        if ($leaveRequest->company_id !== $company->id) {
            abort(404);
        }

        Gate::authorize('view', $company);
        Gate::authorize('approve', $leaveRequest);

        try {
            $approveLeaveRequest->handle($request->user(), $leaveRequest);
        } catch (ActionException $exception) {
            return back()->withErrors([$exception->getField() ?? 'status' => $exception->getMessage()]);
        }

        return back()->with('status', 'Leave request approved.');
    }

    public function reject(Request $request, Company $company, LeaveRequest $leaveRequest, RejectLeaveRequest $rejectLeaveRequest)
    {
        if ($leaveRequest->company_id !== $company->id) {
            abort(404);
        }

        Gate::authorize('view', $company);
        Gate::authorize('reject', $leaveRequest);

        try {
            $rejectLeaveRequest->handle($request->user(), $leaveRequest);
        } catch (ActionException $exception) {
            return back()->withErrors([$exception->getField() ?? 'status' => $exception->getMessage()]);
        }

        return back()->with('status', 'Leave request rejected.');
    }
}
