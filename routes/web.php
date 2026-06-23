<?php

use App\Http\Controllers\Company\AttendanceController as CompanyAttendanceController;
use App\Http\Controllers\Company\DebtController;
use App\Http\Controllers\Company\LeaveController;
use App\Http\Controllers\Company\ReportController;
use App\Http\Controllers\Company\TransactionController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ─── Employee Auth (phone + password) ────────────────────
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Employee\Auth\LoginController::class, 'create'])->name('login');
    Route::post('/login', [App\Http\Controllers\Employee\Auth\LoginController::class, 'store']);
    Route::post('/logout', [App\Http\Controllers\Employee\Auth\LoginController::class, 'destroy'])->name('logout');
});

// ─── Login redirect based on role ───────────────────────
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return redirect(match (auth()->user()->role) {
        'super_admin' => route('admin.dashboard'),
        'owner' => route('company.dashboard'),
        'employee' => route('employee.dashboard'),
        default => route('login'),
    });
})->name('dashboard');

// ─── Super Admin ─────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    });

// ─── Company / Owner ─────────────────────────────────────
Route::middleware(['auth', 'role:owner,super_admin'])
    ->prefix('company')
    ->name('company.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Company\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('employees', App\Http\Controllers\Company\EmployeeController::class);
        Route::resource('debts', App\Http\Controllers\Company\DebtController::class)->except(['show']);
        Route::patch('debts/{debt}/pay', [App\Http\Controllers\Company\DebtController::class, 'pay'])->name('debts.pay');

        Route::get('transactions', [App\Http\Controllers\Company\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/topup', [App\Http\Controllers\Company\TransactionController::class, 'createTopup'])->name('transactions.topup');
        Route::post('transactions/topup', [App\Http\Controllers\Company\TransactionController::class, 'storeTopup'])->name('transactions.topup.store');
        Route::get('transports/create', [App\Http\Controllers\Company\TransactionController::class, 'createTransport'])->name('transports.create');
        Route::post('transports', [App\Http\Controllers\Company\TransactionController::class, 'storeTransport'])->name('transports.store');

        Route::get('attendances', [App\Http\Controllers\Company\AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/create', [App\Http\Controllers\Company\AttendanceController::class, 'create'])->name('attendances.create');
        Route::post('attendances', [App\Http\Controllers\Company\AttendanceController::class, 'store'])->name('attendances.store');
        Route::get('attendances/{attendance}/edit', [App\Http\Controllers\Company\AttendanceController::class, 'edit'])->name('attendances.edit');
        Route::put('attendances/{attendance}', [App\Http\Controllers\Company\AttendanceController::class, 'update'])->name('attendances.update');
        Route::delete('attendances/{attendance}', [App\Http\Controllers\Company\AttendanceController::class, 'destroy'])->name('attendances.destroy');

        Route::resource('leaves', App\Http\Controllers\Company\LeaveController::class)->except(['edit', 'update', 'show']);

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Company\ReportController::class, 'index'])->name('index');
            Route::get('/attendance', [App\Http\Controllers\Company\ReportController::class, 'attendance'])->name('attendance');
            Route::get('/attendance/pdf', [App\Http\Controllers\Company\ReportController::class, 'attendancePdf'])->name('attendance.pdf');
            Route::get('/debts', [App\Http\Controllers\Company\ReportController::class, 'debts'])->name('debts');
            Route::get('/debts/pdf', [App\Http\Controllers\Company\ReportController::class, 'debtsPdf'])->name('debts.pdf');
        });
    });

// ─── Employee ────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/attendance/status', [App\Http\Controllers\Employee\AttendanceController::class, 'status'])->name('attendance.status');
        Route::post('/attendance/clock-in', [App\Http\Controllers\Employee\AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/attendance/clock-out', [App\Http\Controllers\Employee\AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
        Route::get('/attendance/history', [App\Http\Controllers\Employee\AttendanceController::class, 'history'])->name('attendance.history');
    });

// ─── Profile ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
