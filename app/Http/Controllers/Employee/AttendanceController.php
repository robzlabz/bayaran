<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function status(): JsonResponse
    {
        $user = auth()->user();
        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $todayAttendance = Attendance::today()->where('employee_id', $employee->id)->first();

        return response()->json([
            'is_clocked_in' => $todayAttendance && !$todayAttendance->clock_out,
            'attendance' => $todayAttendance,
            'employee' => [
                'name' => $employee->name,
                'balance' => $employee->balance,
            ],
        ]);
    }

    public function clockIn(Request $request): JsonResponse
    {
        $user = auth()->user();
        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Check if already clocked in today
        $existing = Attendance::today()->where('employee_id', $employee->id)->first();
        if ($existing && !$existing->clock_out) {
            return response()->json(['error' => 'Sudah clock in hari ini'], 422);
        }

        if ($existing && $existing->clock_out) {
            // Already clocked out, can't clock in again today
            return response()->json(['error' => 'Sudah clock out hari ini'], 422);
        }

        $data = [
            'employee_id' => $employee->id,
            'clock_in' => now(),
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('attendances', 'public');
            $data['clock_in_photo'] = $path;
        }

        $attendance = Attendance::create($data);

        return response()->json([
            'message' => 'Clock in berhasil',
            'attendance' => $attendance,
            'clock_in_time' => $attendance->clock_in->format('H:i:s'),
        ]);
    }

    public function clockOut(Request $request): JsonResponse
    {
        $user = auth()->user();
        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $attendance = Attendance::today()->where('employee_id', $employee->id)->first();

        if (!$attendance) {
            return response()->json(['error' => 'Belum clock in hari ini'], 422);
        }

        if ($attendance->clock_out) {
            return response()->json(['error' => 'Sudah clock out'], 422);
        }

        $clockOut = now();
        $data = ['clock_out' => $clockOut];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('attendances', 'public');
            $data['clock_out_photo'] = $path;
        }

        // Calculate work hours
        $workHours = $clockOut->diffInMinutes($attendance->clock_in) / 60;
        $data['work_hours'] = round($workHours, 2);

        $attendance->update($data);

        return response()->json([
            'message' => 'Clock out berhasil',
            'attendance' => $attendance,
            'clock_in_time' => $attendance->clock_in->format('H:i:s'),
            'clock_out_time' => $clockOut->format('H:i:s'),
            'work_hours' => $data['work_hours'],
        ]);
    }

    public function history(): JsonResponse
    {
        $user = auth()->user();
        $employee = Employee::find($user->employee_id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->latest()
            ->limit(30)
            ->get()
            ->map(function ($a) {
                return [
                    'date' => $a->clock_in->format('d M Y'),
                    'clock_in' => $a->clock_in->format('H:i'),
                    'clock_out' => $a->clock_out?->format('H:i') ?? '-',
                    'hours' => $a->work_hours ?? '-',
                ];
            });

        return response()->json(['attendances' => $attendances]);
    }
}
