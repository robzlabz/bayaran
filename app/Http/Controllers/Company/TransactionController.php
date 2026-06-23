<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $employeeIds = Employee::where('owner_id', auth()->id())->pluck('id');
        $employeeId = $request->get('employee_id');

        $query = Transaction::whereIn('employee_id', $employeeIds)->with('employee');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $transactions = $query->latest()->paginate(20);
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();

        return view('transactions.index', compact('transactions', 'employees', 'employeeId'));
    }

    public function createTopup(): View
    {
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();
        return view('transactions.topup', compact('employees'));
    }

    public function storeTopup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        abort_if($employee->owner_id !== auth()->id(), 403);

        $balanceBefore = $employee->balance;
        $balanceAfter = $balanceBefore + $validated['amount'];

        Transaction::create([
            'employee_id' => $employee->id,
            'type' => 'topup',
            'amount' => $validated['amount'],
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $validated['description'] ?? 'Top up saldo',
            'transaction_date' => now(),
        ]);

        $employee->increment('balance', $validated['amount']);

        return redirect()->route('company.transactions.index')
            ->with('success', 'Saldo <strong>' . $employee->name . '</strong> berhasil ditambah Rp ' . number_format($validated['amount'], 0, ',', '.'));
    }

    public function createTransport(): View
    {
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)->get();
        return view('transports.create', compact('employees'));
    }

    public function storeTransport(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        abort_if($employee->owner_id !== auth()->id(), 403);

        $amount = -abs($validated['amount']);
        $balanceBefore = $employee->balance;
        $balanceAfter = $balanceBefore + $amount;

        Transaction::create([
            'employee_id' => $employee->id,
            'type' => 'transport',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $validated['description'] ?? 'Ongkos kirim',
            'transaction_date' => now(),
        ]);

        $employee->decrement('balance', abs($amount));

        return redirect()->route('company.transactions.index')
            ->with('success', 'Ongkos <strong>' . $employee->name . '</strong> Rp ' . number_format(abs($amount), 0, ',', '.') . ' berhasil dicatat.');
    }
}
