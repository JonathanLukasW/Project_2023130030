<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Employee;

class PermissionSeeder extends Seeder
{
   public function run(): void
    {
        // 1. Hapus Cache Permission Spatie (Wajib)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- DAFTAR SEMUA IZIN RESMI ---
        $permissions = [
            'dashboard_view',
            'task_view', 
            'task_manage', // Izin View All/Manage ada
            'task_create', 'task_edit', 'task_delete', 'task_mark_status',
            'employee_view', 'employee_manage', 
            'department_manage', 
            'position_manage', 
            'presence_view_all', 'presence_create',
            'salary_view_all',
            'leave_manage', 'leave_confirm_reject',
            'permission_manage', 
        ];

        // 2. Buat semua izin yang ada di array
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // 3. AMBIL SEMUA IZIN YANG ADA DI DATABASE
        $allPermissions = Permission::all();
        

        $hrManagerRole = Role::firstOrCreate(['name' => 'HR Manager']);
        // 4. HR Manager mendapatkan SEMUA Izin yang sudah terdaftar
        // Kita pakai syncPermissions untuk memastikan tidak ada yang terlewat
        $hrManagerRole->syncPermissions($allPermissions); 

        // 5. Sinkronisasi Role Karyawan Lain (Ini penting untuk membatasi mereka)
        
        $developerRole = Role::firstOrCreate(['name' => 'Developer']);
        $developerRole->syncPermissions(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage']);

        $salesRole = Role::firstOrCreate(['name' => 'Sales']);
        $salesRole->syncPermissions(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage']);

        $accountingRole = Role::firstOrCreate(['name' => 'Accounting']);
        $accountingRole->syncPermissions(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage', 'salary_view_all', 'presence_view_all']);

        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
        $supervisorRole->syncPermissions(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage', 'leave_confirm_reject']);

        $asep = User::find(1); 
        if ($asep) $asep->assignRole($hrManagerRole);
        
        $joko = User::find(2); 
        if ($joko) $joko->assignRole($developerRole);

        $denis = User::find(3); 
        if ($denis) $denis->assignRole($salesRole); 

        $bobby = User::find(4); 
        if ($bobby) $bobby->assignRole($accountingRole); 

        $udin = User::find(5); 
        if ($udin) $udin->assignRole($developerRole);
    }
}