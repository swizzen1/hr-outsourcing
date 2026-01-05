<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\LeaveRequests\CreateLeaveRequest;
use App\Actions\Support\ActionException;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class MeLeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $paginator = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'data' => LeaveRequestResource::collection($paginator->getCollection())->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(StoreLeaveRequestRequest $request, CreateLeaveRequest $createLeaveRequest)
    {
        $user = $request->user();

        try {
            $leaveRequest = $createLeaveRequest->handle($user, $request->validated());
        } catch (ActionException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return (new LeaveRequestResource($leaveRequest))
            ->response()
            ->setStatusCode(201);
    }
}
