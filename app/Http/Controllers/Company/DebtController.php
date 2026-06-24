<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DebtController extends Controller
{
    public function index(): View
    {
        $employeeIds = Employee::where('owner_id', auth()->id())->pluck('id');

        $debts = Debt::whereIn('employee_id', $employeeIds)
            ->with('employee')
            ->latest()
            ->paginate(20);

        $totalUnpaid = Debt::whereIn('employee_id', $employeeIds)->unpaid()->sum('amount');
        $totalDebt = Debt::whereIn('employee_id', $employeeIds)->sum('amount');

        return view('debts.index', compact('debts', 'totalUnpaid', 'totalDebt'));
    }

    public function create(Request $request): View
    {
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();
        $selectedEmployeeId = $request->get('employee_id');
        return view('debts.create', compact('employees', 'selectedEmployeeId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'debt_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Ensure employee belongs to this owner
        $employee = Employee::findOrFail($validated['employee_id']);
        abort_if($employee->owner_id !== auth()->id(), 403);

        Debt::create($validated);

        return redirect()->route('company.debts.index')
            ->with('success', 'Hutang berhasil dicatat.');
    }

    public function edit(Debt $debt): View
    {
        $this->authorizeOwner($debt);
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();
        return view('debts.edit', compact('debt', 'employees'));
    }

    public function update(Request $request, Debt $debt): RedirectResponse
    {
        $this->authorizeOwner($debt);

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'debt_date' => ['required', 'date'],
            'is_paid' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        abort_if($employee->owner_id !== auth()->id(), 403);

        if ($request->boolean('is_paid') && !$debt->is_paid) {
            $validated['paid_at'] = now();
        } elseif (!$request->boolean('is_paid')) {
            $validated['paid_at'] = null;
        }

        $debt->update($validated);

        return redirect()->route('company.debts.index')
            ->with('success', 'Hutang berhasil diperbarui.');
    }

    public function destroy(Debt $debt): RedirectResponse
    {
        $this->authorizeOwner($debt);
        $debt->delete();

        return redirect()->route('company.debts.index')
            ->with('success', 'Hutang berhasil dihapus.');
    }

    public function pay(Debt $debt): RedirectResponse
    {
        $this->authorizeOwner($debt);
        $debt->update(['is_paid' => true, 'paid_at' => now()]);

        return redirect()->route('company.debts.index')
            ->with('success', 'Hutang ditandai lunas.');
    }

    private function authorizeOwner(Debt $debt): void
    {
        abort_if($debt->employee->owner_id !== auth()->id(), 403);
    }
}
