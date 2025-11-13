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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard_view',
            'task_view', 'task_create', 'task_edit', 'task_delete', 'task_mark_status',
            'employee_view', 'employee_manage', 
            'department_manage', 
            
            // --- PERUBAHAN 1: Ganti 'role_manage' jadi 'position_manage' ---
            'position_manage', // (Ini dari "bonus" kita kemarin)

            'presence_view_all', 'presence_create',
            'salary_view_all',
            'leave_manage', 'leave_confirm_reject',

            // --- PERUBAHAN 2: Tambahkan Izin Baru untuk halaman baru kita ---
            'permission_manage', // (Izin untuk mengelola halaman Izin)
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $hrManagerRole = Role::firstOrCreate(['name' => 'HR Manager']);
        // --- PERUBAHAN 3: Kasih SEMUA izin ke HR Manager (termasuk yg baru) ---
        $hrManagerRole->givePermissionTo(Permission::all());

        $developerRole = Role::firstOrCreate(['name' => 'Developer']);
        $developerRole->givePermissionTo(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage']);

        // --- PERUBAHAN 4 (Saran): Kita bikin 3 Role baru sesuai Seeder Jabatan ---
        $salesRole = Role::firstOrCreate(['name' => 'Sales']);
        $salesRole->givePermissionTo(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage']);

        $accountingRole = Role::firstOrCreate(['name' => 'Accounting']);
        $accountingRole->givePermissionTo(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage', 'salary_view_all', 'presence_view_all']);

        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
        $supervisorRole->givePermissionTo(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage', 'leave_confirm_reject']);


        // --- Menghubungkan User ke Role ---
        // (Pastikan ID User cocok dengan Seeder HR)
        $asep = User::find(1); // Asep (HR Manager)
        if ($asep) $asep->assignRole($hrManagerRole);
        
        $joko = User::find(2); // Joko (Developer)
        if ($joko) $joko->assignRole($developerRole);

        $denis = User::find(3); // Denis (Sales)
        if ($denis) $denis->assignRole($salesRole); // <-- Diperbarui

        $bobby = User::find(4); // Bobby (Accounting)
        if ($bobby) $bobby->assignRole($accountingRole); // <-- Diperbarui

        $udin = User::find(5); // Udin (Developer)
        if ($udin) $udin->assignRole($developerRole);
        
        // (Kita belum punya user 'Supervisor', tapi Role-nya sudah siap)
    }
}