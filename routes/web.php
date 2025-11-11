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

// Route kustom untuk memuat gambar Captcha
Route::get('captcha-image', function() {
    return captcha_img('flat'); 
})->name('captcha.image');

Route::get('/', function () {
    return redirect()->route('login');
});

// Grup Middleware 'auth' Tunggal untuk semua halaman yang memerlukan login
Route::middleware('auth')->group(function () {

    // DAFTAR SEMUA ROLE AKTIF (SESUAI SEEDER)
    $allActiveRoles = 'HR Manager,Developer,Supervisor,Accounting,Sales';
    $basicRoles = 'Developer,Supervisor,Accounting,Sales';
    $hrAccessRoles = 'HR Manager'; 

    // 1. Dashboard 
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['role:' . $allActiveRoles]);
    Route::get('/dashboard/presence', [DashboardController::class, 'presence']);

    // 2. Modul CRUD (Menggunakan Spatie Permission)

    // Task Module: Menggunakan Permission
    Route::resource('tasks', TaskController::class)
        ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Pembatasan Akses CRUD Tasks menggunakan middleware 'permission' Spatie
    // Note: Anda harus menjalankan 'php artisan migrate:fresh --seed' agar permissions terdaftar
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

    // Employee Module (HR Manager Only)
    Route::resource('employees', EmployeeController::class)->middleware(['role:HR Manager']);

    // Department Module (HR Manager Only)
    Route::resource('departments', DepartmentController::class)->middleware(['role:HR Manager']);

    // Role Module (HR Manager Only)
    Route::resource('roles', RoleController::class)->middleware(['role:HR Manager']);

    // Presences Module 
    Route::resource('presences', PresenceController::class)->middleware(['role:HR Manager,Accounting,Supervisor']);

    // Salaries Module 
    Route::resource('salaries', SalaryController::class)->middleware(['role:HR Manager,Accounting']);

    // Leave Requests Module 
    Route::resource('leave-requests', LeaveRequestController::class)->middleware(['role:' . $allActiveRoles]);
    
    // Custom Actions Leave Requests (HR Manager/Supervisor Only)
    Route::get('/leave-requests/confirm/{id}', [LeaveRequestController::class, 'confirm'])->name('leave-requests.confirm')->middleware(['role:' . $hrAccessRoles]);
    Route::get('/leave-requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject')->middleware(['role:' . $hrAccessRoles]);

    // 3. Profile Bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';