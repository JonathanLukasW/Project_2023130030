<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\LeaveRequestController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['role:HR,Developer']);
    Route::get('/dashboard/presence', [DashboardController::class, 'presence']);

    Route::resource('/tasks', TaskController::class)->middleware(['role:HR,Developer']);

    Route::resource('/employees', EmployeeController::class)->middleware(['role:HR']);


    Route::resource('/departments', DepartmentController::class)->middleware(['role:HR']);


    Route::resource('/roles', RoleController::class)->middleware(['role:HR']);


    Route::resource('/presences', PresenceController::class)->middleware(['role:HR,Developer']);


    Route::resource('/salaries', SalaryController::class)->middleware(['role:HR,Developer']);


    Route::resource('/leave-requests', LeaveRequestController::class)->middleware(['role:HR,Developer']);
    Route::get('/Leave-requests/confirm/{id}', [LeaveRequestController::class, 'confirm'])->name('leave-requests.confirm')->middleware(['role:HR']);
    Route::get('/Leave-requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject')->middleware(['role:HR']);
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
