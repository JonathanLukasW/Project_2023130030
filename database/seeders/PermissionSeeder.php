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
            'department_manage', 'position_manage',
            'presence_view_all', 'presence_create',
            'salary_view_all',
            'leave_manage', 'leave_confirm_reject',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $hrManagerRole = Role::firstOrCreate(['name' => 'HR Manager']);
        $hrManagerRole->givePermissionTo(Permission::all());

        $developerRole = Role::firstOrCreate(['name' => 'Developer']);
        $developerRole->givePermissionTo(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage']);

        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);
        $employeeRole->givePermissionTo(['dashboard_view', 'task_view', 'task_mark_status', 'presence_create', 'leave_manage']);

        $asep = User::find(1);
        if ($asep) $asep->assignRole($hrManagerRole);
        
        $joko = User::find(2);
        if ($joko) $joko->assignRole($developerRole);

        $denis = User::find(3);
        if ($denis) $denis->assignRole($employeeRole); 

        $bobby = User::find(4);
        if ($bobby) $bobby->assignRole($employeeRole); 

        $udin = User::find(5);
        if ($udin) $udin->assignRole($developerRole);
    }
}