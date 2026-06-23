<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOwners = User::where('role', 'owner')->count();
        $totalEmployees = User::where('role', 'employee')->count();
        $totalCompanies = User::where('account_type', 'company')->count();
        $users = User::orderBy('created_at', 'desc')->get();

        return view('dashboards.admin.index', compact('totalOwners', 'totalEmployees', 'totalCompanies', 'users'));
    }
}
