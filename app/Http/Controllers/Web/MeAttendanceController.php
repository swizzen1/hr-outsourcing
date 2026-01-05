<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Actions\Attendance\CheckIn;
use App\Actions\Attendance\CheckOut;
use App\Actions\Support\ActionException;
use App\Models\Attendance;
use Illuminate\Http\Request;

class MeAttendanceController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        return view('me.attendance.show', [
            'attendance' => $attendance,
        ]);
    }

    public function checkIn(Request $request, CheckIn $checkIn)
    {
        $user = $request->user();
        try {
            $checkIn->handle($user);
        } catch (ActionException $exception) {
            return back()->withErrors([$exception->getField() ?? 'attendance' => $exception->getMessage()]);
        }

        return back()->with('status', 'Checked in successfully.');
    }

    public function checkOut(Request $request, CheckOut $checkOut)
    {
        $user = $request->user();
        try {
            $checkOut->handle($user);
        } catch (ActionException $exception) {
            return back()->withErrors([$exception->getField() ?? 'attendance' => $exception->getMessage()]);
        }

        return back()->with('status', 'Checked out successfully.');
    }
}
