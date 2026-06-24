<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\Employee;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = auth()->id();
        $employeeIds = Employee::where('owner_id', $ownerId)->pluck('id');

        $employeeId = $request->get('employee_id');

        $query = Payment::whereIn('employee_id', $employeeIds)->with(['employee', 'debts']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $payments = $query->latest('payment_date')->paginate(20);
        $employees = Employee::where('owner_id', $ownerId)->where('is_active', true)->get();

        $totalPaid = $query->sum('total_amount');
        $totalSalary = $query->sum('salary_amount');
        $totalDebtPaid = $query->sum('debt_amount');

        return view('payments.index', compact(
            'payments', 'employees', 'employeeId',
            'totalPaid', 'totalSalary', 'totalDebtPaid'
        ));
    }

    public function create(Request $request): View
    {
        $employees = Employee::where('owner_id', auth()->id())->where('is_active', true)
            ->with(['debts' => function ($q) { $q->unpaid(); }])
            ->get();
        $selectedEmployeeId = $request->get('employee_id');
        return view('payments.create', compact('employees', 'selectedEmployeeId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'total_amount' => ['required', 'numeric', 'min:1'],
            'salary_amount' => ['required', 'numeric', 'min:0'],
            'debt_amount' => ['required', 'numeric', 'min:0'],
            'debt_ids' => ['nullable', 'array'],
            'debt_ids.*' => ['exists:debts,id'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Validate split
        if (($validated['salary_amount'] + $validated['debt_amount']) != $validated['total_amount']) {
            return back()->withErrors(['debt_amount' => 'Jumlah gaji + hutang harus sama dengan total bayaran.'])->withInput();
        }

        $employee = Employee::findOrFail($validated['employee_id']);
        abort_if($employee->owner_id !== auth()->id(), 403);

        // Record payment
        $payment = Payment::create([
            'employee_id' => $employee->id,
            'total_amount' => $validated['total_amount'],
            'salary_amount' => $validated['salary_amount'],
            'debt_amount' => $validated['debt_amount'],
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Attach debts with amounts
        if ($validated['debt_amount'] > 0 && !empty($validated['debt_ids'])) {
            $remaining = $validated['debt_amount'];

            foreach ($validated['debt_ids'] as $debtId) {
                if ($remaining <= 0) break;

                $debt = Debt::findOrFail($debtId);
                $debtRemaining = $debt->remaining;
                $payAmount = min($debtRemaining, $remaining);

                // Link to payment
                $payment->debts()->attach($debtId, ['amount' => $payAmount]);

                // Update debt
                $debt->increment('paid_amount', $payAmount);
                $remaining -= $payAmount;

                if ($debt->refresh()->remaining <= 0) {
                    $debt->update(['is_paid' => true, 'paid_at' => now()]);
                }
            }
        }

        // Salary goes to employee balance
        if ($validated['salary_amount'] > 0) {
            $employee->increment('balance', $validated['salary_amount']);
        }

        return redirect()->route('company.payments.index')
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        abort_if($payment->employee->owner_id !== auth()->id(), 403);
        $payment->delete(); // cascade deletes pivot

        return redirect()->route('company.payments.index')
            ->with('success', 'Riwayat pembayaran dihapus.');
    }
}
