<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ExportController;

// Helper Route untuk Captcha
Route::get('captcha-image', function () {
    return captcha_img('flat');
})->name('captcha.image');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['permission:dashboard_view']);
    Route::get('/dashboard/presence', [DashboardController::class, 'presence']);
    
    // --- TASK CALENDAR DATA ---
    Route::get('/tasks/events', [TaskController::class, 'getEvents'])->name('tasks.events');

    // --- EXPORT DATA ---
    Route::get('/export/employees', [ExportController::class, 'exportEmployees'])->name('export.employees')->middleware(['permission:employee_manage']);
    Route::get('/export/presences', [ExportController::class, 'exportPresences'])->name('export.presences')->middleware(['permission:presence_view_all']);
    
    // --- TASKS MANAGEMENT ---
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{encodedId}', [TaskController::class, 'show'])->name('tasks.show');
    
    Route::middleware('permission:task_create')->group(function () {
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });
    
    Route::middleware('permission:task_edit')->group(function () {
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    });
    
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')->middleware(['permission:task_delete']);
    
    // Task Status Actions
    Route::get('/tasks/done/{id}', [TaskController::class, 'done'])->name('tasks.done')->middleware(['permission:task_mark_status']);
    Route::get('/tasks/pending/{id}', [TaskController::class, 'pending'])->name('tasks.pending')->middleware(['permission:task_mark_status']);

    // --- HR MASTER DATA (Employees, Departments, Positions) ---
    Route::resource('employees', EmployeeController::class)->middleware(['permission:employee_manage']);
    Route::resource('departments', DepartmentController::class)->middleware(['permission:department_manage']);
    Route::resource('positions', PositionController::class)->middleware(['permission:position_manage']);

    // --- PRESENCE MANAGEMENT ---
    Route::get('presences/create', [PresenceController::class, 'create'])->name('presences.create')->middleware(['permission:presence_create']);
    Route::post('presences', [PresenceController::class, 'store'])->name('presences.store')->middleware(['permission:presence_create']);
    
    Route::get('presences', [PresenceController::class, 'index'])->name('presences.index'); 

    Route::middleware(['permission:presence_view_all'])->group(function () {
        Route::get('presences/{presence}/edit', [PresenceController::class, 'edit'])->name('presences.edit');
        Route::put('presences/{presence}', [PresenceController::class, 'update'])->name('presences.update');
        Route::delete('presences/{presence}', [PresenceController::class, 'destroy'])->name('presences.destroy');
    });

    // --- SALARIES (PAYROLL AUTOMATION) ---
    // 1. Generate & Form (Hanya Admin/HR)
    Route::get('/payroll/generate', [SalaryController::class, 'generateForm'])->name('salaries.generate_form')->middleware(['permission:salary_view_all']);
    Route::post('/payroll/generate', [SalaryController::class, 'generate'])->name('salaries.generate')->middleware(['permission:salary_view_all']);

    // 2. Resource (Hanya Index, Show, Destroy - Create/Edit Manual DIHAPUS)
    Route::resource('salaries', SalaryController::class)
        ->only(['destroy']) // Destroy butuh permission salary_view_all
        ->middleware(['permission:salary_view_all']);

    // 3. Index & Show (Custom logic di controller untuk cek hak akses karyawan biasa)
    Route::get('salaries', [SalaryController::class, 'index'])->name('salaries.index');
    Route::get('salaries/{salary}', [SalaryController::class, 'show'])->name('salaries.show');

    // --- LEAVE REQUESTS ---
    Route::resource('leave-requests', LeaveRequestController::class)->middleware(['permission:leave_manage']);
    Route::get('/leave-requests/confirm/{id}', [LeaveRequestController::class, 'confirm'])->name('leave-requests.confirm')->middleware(['permission:leave_confirm_reject']);
    Route::get('/leave-requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject')->middleware(['permission:leave_confirm_reject']);

    // --- PERMISSION MANAGEMENT ---
    Route::controller(RolePermissionController::class)->middleware(['permission:permission_manage'])->group(function () {
        Route::get('/manage-permissions', 'index')->name('permissions.index');
        Route::post('/manage-permissions', 'update')->name('permissions.update');
    });

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';