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
            // Kita ambil 'name'-nya saja dari permission yang dimiliki role
            $rolePermissions[$role->id] = $role->permissions->pluck('name')->all();
        }

        // 4. Kirim semua data ini ke view
        return view('permissions.index', compact('permissions', 'roles', 'rolePermissions'));
    }

    /**
     * Menyimpan perubahan Izin (Permission) dari form.
     */
    public function update(Request $request)
    {
        try {
            // Ambil semua ID Role yang dikirim dari form (kecuali HR Manager)
            $roleIds = Role::where('name', '!=', 'HR Manager')->pluck('id');

            foreach ($roleIds as $roleId) {
                // 1. Ambil data Role-nya
                $role = Role::findById($roleId);
                
                // 2. Ambil daftar izin (dari checkbox) yang dikirim dari form
                // Jika tidak ada izin yang dicentang untuk role ini, $permissions akan jadi array kosong []
                $permissions = $request->input('permissions.' . $roleId, []);

                $role->syncPermissions($permissions);
            }

            // (Opsional) Hapus cache Spatie biar perubahannya langsung terasa
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->route('permissions.index')->with('success', 'Permissions updated successfully.');

        } catch (\Exception $e) {
            return redirect()->route('permissions.index')->with('error', 'Error updating permissions: ' . $e->getMessage());
        }
    }
}