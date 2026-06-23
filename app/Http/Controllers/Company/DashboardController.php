<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalEmployees = Employee::where('owner_id', $userId)->count();
        $totalDebts = DB::table('debts')->whereIn('employee_id', function ($q) use ($userId) {
            $q->select('id')->from('employees')->where('owner_id', $userId);
        })->where('is_paid', false)->sum('amount');
        $totalBalance = Employee::where('owner_id', $userId)->sum('balance');
        $todayAttendance = 0; // placeholder for now

        return view('dashboards.company.index', compact(
            'totalEmployees', 'totalDebts', 'totalBalance', 'todayAttendance'
        ));
    }
}
