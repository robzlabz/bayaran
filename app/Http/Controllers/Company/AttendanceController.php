<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $employeeId = $request->get('employee_id');
        $date = $request->get('date', today()->format('Y-m-d'));

        $employeeIds = Employee::where('owner_id', auth()->id())->pluck('id');

        $query = Attendance::whereIn('employee_id', $employeeIds)->with('employee');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $query->whereDate('clock_in', $date);

        $attendances = $query->latest('clock_in')->get();
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();

        $todayCount = Attendance::whereIn('employee_id', $employeeIds)
            ->whereDate('clock_in', today())
            ->count();

        $activeEmployees = Employee::where('owner_id', auth()->id())->where('is_active', true)->count();

        return view('attendances.index', compact(
            'attendances', 'employees', 'employeeId', 'date', 'todayCount', 'activeEmployees'
        ));
    }

    public function create(Request $request): View
    {
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();
        $selectedEmployeeId = $request->get('employee_id');
        return view('attendances.create', compact('employees', 'selectedEmployeeId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'clock_in' => ['required', 'date'],
            'clock_out' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Custom validation: same-day clock_out before clock_in is invalid
        if ($validated['clock_out']) {
            $clockIn = now()->parse($validated['clock_in']);
            $clockOut = now()->parse($validated['clock_out']);

            if ($clockIn->isSameDay($clockOut) && $clockOut->lt($clockIn)) {
                return back()->withErrors([
                    'clock_out' => 'Jam pulang tidak boleh sebelum jam masuk pada hari yang sama.',
                ])->withInput();
            }
        }

        $employee = Employee::findOrFail($validated['employee_id']);
        abort_if($employee->owner_id !== auth()->id(), 403);

        $data = [
            'employee_id' => $employee->id,
            'clock_in' => $validated['clock_in'],
            'clock_out' => $validated['clock_out'] ?? null,
            'is_manual_entry' => true,
            'is_clock_in_manual' => true,
            'is_clock_out_manual' => $validated['clock_out'] ? true : false,
            'notes' => $validated['notes'] ?? 'Input manual oleh admin',
        ];

        if ($data['clock_out']) {
            $clockIn = now()->parse($data['clock_in']);
            $clockOut = now()->parse($data['clock_out']);
            $data['work_hours'] = round(abs($clockIn->floatDiffInMinutes($clockOut)) / 60, 2);
        }

        Attendance::create($data);

        return redirect()->route('company.attendances.index')
            ->with('success', 'Absensi manual berhasil dicatat.');
    }

    public function edit(Attendance $attendance): View
    {
        $this->authorizeOwner($attendance);
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();
        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $this->authorizeOwner($attendance);

        $validated = $request->validate([
            'clock_in' => ['required', 'date'],
            'clock_out' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Custom validation: same-day clock_out before clock_in is invalid
        if ($validated['clock_out']) {
            $clockIn = now()->parse($validated['clock_in']);
            $clockOut = now()->parse($validated['clock_out']);

            if ($clockIn->isSameDay($clockOut) && $clockOut->lt($clockIn)) {
                return back()->withErrors([
                    'clock_out' => 'Jam pulang tidak boleh sebelum jam masuk pada hari yang sama.',
                ])->withInput();
            }
        }

        $data = [
            'clock_in' => $validated['clock_in'],
            'clock_out' => $validated['clock_out'] ?? null,
            'is_clock_in_manual' => true,
            'notes' => $validated['notes'] ?? $attendance->notes,
        ];

        if ($data['clock_out']) {
            $data['is_clock_out_manual'] = true;
            $clockIn = now()->parse($data['clock_in']);
            $clockOut = now()->parse($data['clock_out']);
            $data['work_hours'] = round(abs($clockIn->floatDiffInMinutes($clockOut)) / 60, 2);
        } else {
            $data['work_hours'] = null;
        }

        $attendance->update($data);

        return redirect()->route('company.attendances.index', ['date' => $attendance->clock_in->format('Y-m-d')])
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $this->authorizeOwner($attendance);
        $attendance->delete();

        return redirect()->route('company.attendances.index')
            ->with('success', 'Absensi berhasil dihapus.');
    }

    private function authorizeOwner(Attendance $attendance): void
    {
        abort_if($attendance->employee->owner_id !== auth()->id(), 403);
    }
}
