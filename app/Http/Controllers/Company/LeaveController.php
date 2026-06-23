<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = auth()->id();
        $employeeIds = Employee::where('owner_id', $ownerId)->pluck('id');

        $employeeId = $request->get('employee_id');
        $year = $request->get('year', now()->year);

        $query = Leave::whereIn('employee_id', $employeeIds)
            ->whereYear('start_date', $year)
            ->with('employee');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $leaves = $query->latest('start_date')->paginate(20);
        $employees = Employee::where('owner_id', $ownerId)->where('is_active', true)->get();

        $totalLeaves = $leaves->total();
        $sickCount = $leaves->where('type', 'sick')->count();
        $permissionCount = $leaves->where('type', 'permission')->count();
        $annualCount = $leaves->where('type', 'annual_leave')->count();

        return view('leaves.index', compact(
            'leaves', 'employees', 'employeeId', 'year',
            'totalLeaves', 'sickCount', 'permissionCount', 'annualCount'
        ));
    }

    public function create(): View
    {
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();
        return view('leaves.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'type' => ['required', 'in:sick,permission,annual_leave'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        abort_if($employee->owner_id !== auth()->id(), 403);

        $validated['status'] = 'approved';
        $validated['approved_by'] = auth()->id();

        Leave::create($validated);

        return redirect()->route('company.leaves.index')
            ->with('success', 'Izin berhasil dicatat.');
    }

    public function destroy(Leave $leave): RedirectResponse
    {
        abort_if($leave->employee->owner_id !== auth()->id(), 403);
        $leave->delete();

        return redirect()->route('company.leaves.index')
            ->with('success', 'Izin berhasil dihapus.');
    }
}
