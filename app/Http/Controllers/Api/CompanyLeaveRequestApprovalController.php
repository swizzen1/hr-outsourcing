<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\LeaveRequests\ApproveLeaveRequest;
use App\Actions\LeaveRequests\RejectLeaveRequest;
use App\Actions\Support\ActionException;
use App\Http\Resources\LeaveRequestResource;
use App\Models\Company;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Gate;

class CompanyLeaveRequestApprovalController extends Controller
{
    public function approve(Company $company, LeaveRequest $leaveRequest, ApproveLeaveRequest $approveLeaveRequest)
    {
        if ($leaveRequest->company_id !== $company->id) {
            abort(404);
        }

        Gate::authorize('approve', $leaveRequest);

        try {
            $leaveRequest = $approveLeaveRequest->handle(request()->user(), $leaveRequest);
        } catch (ActionException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return new LeaveRequestResource($leaveRequest);
    }

    public function reject(Company $company, LeaveRequest $leaveRequest, RejectLeaveRequest $rejectLeaveRequest)
    {
        if ($leaveRequest->company_id !== $company->id) {
            abort(404);
        }

        Gate::authorize('reject', $leaveRequest);

        try {
            $leaveRequest = $rejectLeaveRequest->handle(request()->user(), $leaveRequest);
        } catch (ActionException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return new LeaveRequestResource($leaveRequest);
    }
}
