<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController; 

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/reload-captcha', [AuthController::class, 'reloadCaptcha'])->name('reloadCaptcha');


Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/presence', [DashboardController::class, 'presence'])->name('dashboard.presence');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/change-password', [ProfileController::class, 'editPassword'])->name('password.edit');
    Route::post('/change-password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::middleware(['role:HR Manager'])->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('positions', PositionController::class);
     
        Route::get('/manage-permissions', [RolePermissionController::class, 'index'])->name('permissions.index');
        Route::post('/manage-permissions/update', [RolePermissionController::class, 'update'])->name('permissions.update');

        Route::get('/export/presences', [ExportController::class, 'presences'])->name('export.presences');
        Route::get('/export/employees', [ExportController::class, 'employees'])->name('export.employees');
    });

    Route::get('presences', [PresenceController::class, 'index'])->name('presences.index');
    Route::get('presences/create', [PresenceController::class, 'create'])->name('presences.create')->middleware(['permission:presence_create']);
    Route::post('presences', [PresenceController::class, 'store'])->name('presences.store')->middleware(['permission:presence_create']);

    Route::middleware(['permission:presence_view_all'])->group(function () {
        Route::get('presences/{presence}/edit', [PresenceController::class, 'edit'])->name('presences.edit');
        Route::put('presences/{presence}', [PresenceController::class, 'update'])->name('presences.update');
        Route::delete('presences/{presence}', [PresenceController::class, 'destroy'])->name('presences.destroy');
    });

    Route::get('tasks/events', [TaskController::class, 'calendarEvents'])->name('tasks.events');
    Route::resource('tasks', TaskController::class);

    Route::get('leave-requests/confirm/{id}', [LeaveRequestController::class, 'confirm'])->name('leave-requests.confirm');
    Route::get('leave-requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::resource('leave-requests', LeaveRequestController::class);

    Route::get('salaries/generate', [SalaryController::class, 'generateView'])->name('salaries.generateView');
    Route::post('salaries/generate', [SalaryController::class, 'generateProcess'])->name('salaries.generate');
    Route::get('salaries/slip/{salary}', [SalaryController::class, 'slip'])->name('salaries.slip');
    Route::resource('salaries', SalaryController::class);

});