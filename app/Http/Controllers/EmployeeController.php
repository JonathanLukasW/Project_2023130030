<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 
use Illuminate\Contracts\Encryption\DecryptException;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'position'])->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        return view('employees.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'email' => 'required|email|unique:employees,email|unique:users,email', 
            'phone_number' => 'required',
            'address' => 'required',
            'birth_date' => 'required|date',
            'hire_date' => 'required|date',
            'department_id' => 'required',
            'position_id' => 'required',
            'salary' => 'nullable|numeric', 
        ]);

        DB::transaction(function () use ($request) {
            
            $employee = Employee::create([
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'hire_date' => $request->hire_date,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'salary' => $request->salary ?? 0,
                'status' => 'active'
            ]);

            $user = User::create([
                'name' => $request->fullname,
                'email' => $request->email,
                'password' => Hash::make('password123'),
                'employee_id' => $employee->id,
            ]);

            $user->assignRole('Employee');
        });

        return redirect()->route('employees.index')
            ->with('success', 'Employee & User Account created successfully. Default Password: password123');
    }

    public function show($id)
    {
        try {
            $decryptedId = decrypt($id);
            $employee = Employee::with(['department', 'position'])->findOrFail($decryptedId);
            return view('employees.show', compact('employee'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = decrypt($id);
            $employee = Employee::findOrFail($decryptedId);
            $departments = Department::all();
            $positions = Position::all();
            return view('employees.edit', compact('employee', 'departments', 'positions'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $decryptedId = decrypt($id);
            $employee = Employee::findOrFail($decryptedId);

            $request->validate([
                'fullname' => 'required',
                'email' => 'required|email|unique:employees,email,'.$employee->id,
                'department_id' => 'required',
                'position_id' => 'required',
                'status' => 'required'
            ]);

            $employee->update($request->all());

            $user = User::where('employee_id', $employee->id)->first();
            if ($user && $user->email !== $request->email) {
                $user->update([
                    'email' => $request->email,
                    'name' => $request->fullname
                ]);
            }

            return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');

        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = decrypt($id);
            $employee = Employee::findOrFail($decryptedId);
 
            $user = User::where('employee_id', $employee->id)->first();
            if ($user) {
                $user->delete();
            }

            $employee->delete();
            return redirect()->route('employees.index')->with('success', 'Employee & User Account deleted successfully.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }
}