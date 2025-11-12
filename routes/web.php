<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController; // Sudah benar
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\LeaveRequestController;

Route::get('captcha-image', function () {
    return captcha_img('flat');
})->name('captcha.image');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['permission:dashboard_view']);
    Route::get('/dashboard/presence', [DashboardController::class, 'presence']);

    Route::resource('tasks', TaskController::class)
        ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::middleware('permission:task_create')->group(function () {
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });
    Route::middleware('permission:task_edit')->group(function () {
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    });
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')->middleware(['permission:task_delete']);

    Route::get('/tasks/done/{id}', [TaskController::class, 'done'])->name('tasks.done')->middleware(['permission:task_mark_status']);
    Route::get('/tasks/pending/{id}', [TaskController::class, 'pending'])->name('tasks.pending')->middleware(['permission:task_mark_status']);

    Route::resource('employees', EmployeeController::class)->middleware(['permission:employee_manage']);

    Route::resource('departments', DepartmentController::class)->middleware(['permission:department_manage']);

    Route::resource('positions', PositionController::class)->middleware(['permission:position_manage']);

    Route::get('presences/create', [PresenceController::class, 'create'])->name('presences.create')->middleware(['permission:presence_create']);
    Route::post('presences', [PresenceController::class, 'store'])->name('presences.store')->middleware(['permission:presence_create']);

    // 2. Izin 'presence_view_all' (HANYA HR/Admin) untuk sisanya
    Route::get('presences', [PresenceController::class, 'index'])->name('presences.index')->middleware(['permission:presence_view_all']);
    Route::get('presences/{presence}/edit', [PresenceController::class, 'edit'])->name('presences.edit')->middleware(['permission:presence_view_all']);
    Route::put('presences/{presence}', [PresenceController::class, 'update'])->name('presences.update')->middleware(['permission:presence_view_all']);
    Route::delete('presences/{presence}', [PresenceController::class, 'destroy'])->name('presences.destroy')->middleware(['permission:presence_view_all']);

    Route::get('salaries', [SalaryController::class, 'index'])->name('salaries.index')->middleware(['permission:leave_manage']);
    Route::get('salaries/{salary}', [SalaryController::class, 'show'])->name('salaries.show')->middleware(['permission:leave_manage']);

    Route::get('salaries/create', [SalaryController::class, 'create'])->name('salaries.create')->middleware(['permission:salary_view_all']);
    Route::post('salaries', [SalaryController::class, 'store'])->name('salaries.store')->middleware(['permission:salary_view_all']);
    Route::get('salaries/{salary}/edit', [SalaryController::class, 'edit'])->name('salaries.edit')->middleware(['permission:salary_view_all']);
    Route::put('salaries/{salary}', [SalaryController::class, 'update'])->name('salaries.update')->middleware(['permission:salary_view_all']);
    Route::delete('salaries/{salary}', [SalaryController::class, 'destroy'])->name('salaries.destroy')->middleware(['permission:salary_view_all']);

    Route::resource('leave-requests', LeaveRequestController::class)->middleware(['permission:leave_manage']);

    Route::get('/leave-requests/confirm/{id}', [LeaveRequestController::class, 'confirm'])->name('leave-requests.confirm')->middleware(['permission:leave_confirm_reject']);
    Route::get('/leave-requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject')->middleware(['permission:leave_confirm_reject']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
