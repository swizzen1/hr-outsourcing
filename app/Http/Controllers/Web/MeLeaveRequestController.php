<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Actions\LeaveRequests\CreateLeaveRequest;
use App\Actions\Support\ActionException;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class MeLeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $leaveRequests = LeaveRequest::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('me.leave_requests.index', [
            'leaveRequests' => $leaveRequests,
        ]);
    }

    public function create()
    {
        return view('me.leave_requests.create');
    }

    public function store(StoreLeaveRequestRequest $request, CreateLeaveRequest $createLeaveRequest)
    {
        $user = $request->user();

        try {
            $createLeaveRequest->handle($user, $request->validated());
        } catch (ActionException $exception) {
            return back()->withErrors([$exception->getField() ?? 'leave_request' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('me.leave_requests.index')
            ->with('status', 'Leave request submitted.');
    }
}
