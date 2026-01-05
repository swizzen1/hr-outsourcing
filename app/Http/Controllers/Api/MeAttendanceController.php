<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\Attendance\CheckIn;
use App\Actions\Attendance\CheckOut;
use App\Actions\Support\ActionException;
use App\Http\Resources\AttendanceResource;
use Illuminate\Http\Request;

class MeAttendanceController extends Controller
{
    public function checkIn(Request $request, CheckIn $checkIn)
    {
        $user = $request->user();

        try {
            $result = $checkIn->handle($user);
        } catch (ActionException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $resource = new AttendanceResource($result['attendance']);
        $response = $resource->response();

        if ($result['created']) {
            $response->setStatusCode(201);
        }

        return $response;
    }

    public function checkOut(Request $request, CheckOut $checkOut)
    {
        $user = $request->user();

        try {
            $attendance = $checkOut->handle($user);
        } catch (ActionException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return new AttendanceResource($attendance);
    }
}
