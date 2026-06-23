<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboards.company.index');
    }
}
