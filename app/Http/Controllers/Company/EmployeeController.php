<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = Employee::where('owner_id', auth()->id())->latest()->paginate(20);
        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:employees,phone', 'unique:users,phone'],
            'payment_type' => ['required', 'in:monthly,daily,hourly,per_delivery'],
            'salary_amount' => ['required_if:payment_type,monthly', 'nullable', 'numeric', 'min:0'],
            'daily_rate' => ['required_if:payment_type,daily', 'nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['required_if:payment_type,hourly', 'nullable', 'numeric', 'min:0'],
            'delivery_rate' => ['required_if:payment_type,per_delivery', 'nullable', 'numeric', 'min:0'],
        ]);

        $validated['owner_id'] = auth()->id();
        $validated['balance'] = 0;
        $validated['leave_quota'] = $validated['payment_type'] === 'monthly' ? 12 : 0;

        $employee = Employee::create($validated);

        $defaultPassword = 'bayaran' . substr($employee->phone, -4);
        User::create([
            'name' => $employee->name,
            'phone' => $employee->phone,
            'email' => $employee->phone . '@bayaran.app',
            'password' => Hash::make($defaultPassword),
            'password_default' => $defaultPassword,
            'role' => 'employee',
            'owner_id' => auth()->id(),
            'employee_id' => $employee->id,
            'account_type' => 'personal',
        ]);

        return redirect()->route('company.employees.index')
            ->with('success', "Karyawan <strong>{$employee->name}</strong> berhasil ditambahkan. Password default: <strong>{$defaultPassword}</strong>");
    }

    public function show(Employee $employee): View
    {
        $this->authorizeOwner($employee);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $this->authorizeOwner($employee);
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorizeOwner($employee);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:employees,phone,' . $employee->id],
            'payment_type' => ['required', 'in:monthly,daily,hourly,per_delivery'],
            'salary_amount' => ['required_if:payment_type,monthly', 'nullable', 'numeric', 'min:0'],
            'daily_rate' => ['required_if:payment_type,daily', 'nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['required_if:payment_type,hourly', 'nullable', 'numeric', 'min:0'],
            'delivery_rate' => ['required_if:payment_type,per_delivery', 'nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $employee->update($validated);

        if ($employee->user) {
            $employee->user->update([
                'name' => $employee->name,
                'phone' => $employee->phone,
            ]);
        }

        return redirect()->route('company.employees.index')
            ->with('success', "Karyawan <strong>{$employee->name}</strong> berhasil diperbarui.");
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorizeOwner($employee);
        $name = $employee->name;
        $employee->delete();

        return redirect()->route('company.employees.index')
            ->with('success', "Karyawan <strong>{$name}</strong> berhasil dihapus.");
    }

    private function authorizeOwner(Employee $employee): void
    {
        abort_if($employee->owner_id !== auth()->id(), 403);
    }
}
