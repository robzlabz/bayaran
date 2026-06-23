<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Debt;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $ownerId = auth()->id();
        $employeeIds = Employee::where('owner_id', $ownerId)->pluck('id');

        $totalEmployees = Employee::where('owner_id', $ownerId)->count();
        $activeEmployees = Employee::where('owner_id', $ownerId)->where('is_active', true)->count();
        $totalBalance = Employee::where('owner_id', $ownerId)->sum('balance');
        $totalDebt = Debt::whereIn('employee_id', $employeeIds)->sum('amount');
        $unpaidDebt = Debt::whereIn('employee_id', $employeeIds)->where('is_paid', false)->sum('amount');
        $todayAttendance = Attendance::whereIn('employee_id', $employeeIds)->whereDate('clock_in', today())->count();

        $totalLeaves = Leave::whereIn('employee_id', $employeeIds)->year()->count();

        return view('reports.index', compact(
            'totalEmployees', 'activeEmployees', 'totalBalance',
            'totalDebt', 'unpaidDebt', 'todayAttendance', 'totalLeaves'
        ));
    }

    public function attendance(Request $request): View
    {
        $ownerId = auth()->id();
        $employeeIds = Employee::where('owner_id', $ownerId)->pluck('id');

        $month = $request->get('month', now()->format('m'));
        $year = $request->get('year', now()->format('Y'));
        $employeeId = $request->get('employee_id');

        $query = Attendance::whereIn('employee_id', $employeeIds)
            ->whereYear('clock_in', $year)
            ->whereMonth('clock_in', $month)
            ->with('employee');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->orderBy('clock_in')->get();
        $employees = Employee::where('owner_id', $ownerId)->where('is_active', true)->get();

        // Group by employee
        $grouped = $attendances->groupBy('employee_id');

        return view('reports.attendance', compact(
            'attendances', 'grouped', 'employees', 'month', 'year', 'employeeId',
            'employeeIds', 'ownerId'
        ));
    }

    public function attendancePdf(Request $request)
    {
        $ownerId = auth()->id();
        $employeeIds = Employee::where('owner_id', $ownerId)->pluck('id');

        $month = $request->get('month', now()->format('m'));
        $year = $request->get('year', now()->format('Y'));
        $employeeId = $request->get('employee_id');

        $query = Attendance::whereIn('employee_id', $employeeIds)
            ->whereYear('clock_in', $year)
            ->whereMonth('clock_in', $month)
            ->with('employee');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->orderBy('clock_in')->get();
        $grouped = $attendances->groupBy('employee_id');

        $companyName = auth()->user()->company_name ?? auth()->user()->name;
        $period = \Carbon\Carbon::create($year, $month)->locale('id')->isoFormat('MMMM YYYY');

        $pdf = Pdf::loadView('reports.pdf.attendance', compact(
            'attendances', 'grouped', 'month', 'year', 'employeeId',
            'employeeIds', 'ownerId', 'companyName', 'period'
        ));

        return $pdf->download("rekap-absensi-{$month}-{$year}.pdf");
    }

    public function debts(Request $request): View
    {
        $ownerId = auth()->id();
        $employeeIds = Employee::where('owner_id', $ownerId)->pluck('id');

        $month = $request->get('month', now()->format('m'));
        $year = $request->get('year', now()->format('Y'));
        $employeeId = $request->get('employee_id');
        $status = $request->get('status', '');

        $query = Debt::whereIn('employee_id', $employeeIds)
            ->whereYear('debt_date', $year)
            ->whereMonth('debt_date', $month)
            ->with('employee');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($status === 'unpaid') {
            $query->where('is_paid', false);
        } elseif ($status === 'paid') {
            $query->where('is_paid', true);
        }

        $debts = $query->latest('debt_date')->get();
        $employees = Employee::where('owner_id', $ownerId)->where('is_active', true)->get();

        $totalDebt = $debts->sum('amount');
        $totalUnpaid = $debts->where('is_paid', false)->sum('amount');

        return view('reports.debts', compact(
            'debts', 'employees', 'month', 'year', 'employeeId', 'status',
            'totalDebt', 'totalUnpaid'
        ));
    }

    public function debtsPdf(Request $request)
    {
        $ownerId = auth()->id();
        $employeeIds = Employee::where('owner_id', $ownerId)->pluck('id');

        $month = $request->get('month', now()->format('m'));
        $year = $request->get('year', now()->format('Y'));
        $employeeId = $request->get('employee_id');

        $query = Debt::whereIn('employee_id', $employeeIds)
            ->whereYear('debt_date', $year)
            ->whereMonth('debt_date', $month)
            ->with('employee');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $debts = $query->latest('debt_date')->get();
        $companyName = auth()->user()->company_name ?? auth()->user()->name;
        $period = \Carbon\Carbon::create($year, $month)->locale('id')->isoFormat('MMMM YYYY');
        $totalDebt = $debts->sum('amount');

        $pdf = Pdf::loadView('reports.pdf.debts', compact(
            'debts', 'month', 'year', 'companyName', 'period', 'totalDebt'
        ));

        return $pdf->download("rekap-hutang-{$month}-{$year}.pdf");
    }
}
