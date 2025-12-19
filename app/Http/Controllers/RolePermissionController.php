<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name', 'asc')->get();
        $roles = Role::where('name', '!=', 'HR Manager')
                     ->with('permissions')
                     ->orderBy('name', 'asc')
                     ->get();

        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role->id] = $role->permissions->pluck('name')->all();
        }

        return view('permissions.index', compact('permissions', 'roles', 'rolePermissions'));
    }

    public function update(Request $request)
    {
        try {
            $roleIds = Role::where('name', '!=', 'HR Manager')->pluck('id');

            foreach ($roleIds as $roleId) {
                $role = Role::findById($roleId);
                
                $permissions = $request->input('permissions.' . $roleId, []);

                $role->syncPermissions($permissions);
            }
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->route('permissions.index')->with('success', 'Permissions updated successfully.');

        } catch (\Exception $e) {
            return redirect()->route('permissions.index')->with('error', 'Error updating permissions: ' . $e->getMessage());
        }
    }
}