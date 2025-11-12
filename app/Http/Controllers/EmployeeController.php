<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;

class EmployeeController extends Controller
{
    public function index(){
        $employees = Employee::with(['department', 'position'])->get();
        return view('employees.index', compact('employees'));
    }

    public function create() {
        $departments = Department::all();
        $positions = Position::all();
        return view('employees.create', compact('departments', 'positions'));
    }

    public function show($id){
        $employee = Employee::with(['department', 'position'])->findOrFail($id);
        return view('employees.show', compact('employee'));
    }

    public function edit($id){
        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        $positions = Position::all();
        return view('employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function store(Request $request){
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|required',
            'birth_date' => 'required|date',
            'hire_date' => 'required|date',
            'department_id' => 'required',
            'position_id' => 'required',
            'status' => 'required|string',
            'salary' => 'required|numeric'
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')->with('success', 'Employee created successfully');

    }

     public function update(Request $request, $id){
            $request->validate([
                'fullname' => 'required|string|max:255',
                'email' => 'required|email',
                'phone_number' => 'required|string|max:20',
                'address' => 'nullable|required',
                'birth_date' => 'required|date',
                'hire_date' => 'required|date',
                'department_id' => 'required',
                'position_id' => 'required',
                'status' => 'required|string',
                'salary' => 'required|numeric'
            ]);

            $employee = Employee::findOrFail($id);
            $employee->update($request->all());

            return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
        }

        public function destroy(int $id){
            $employee = Employee::findOrFail($id);
            $employee->delete();

            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
        }  
}