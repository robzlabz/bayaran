<?php

use App\Http\Controllers\Company\DebtController;
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
    });

// ─── Employee ────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');
    });

// ─── Profile ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
