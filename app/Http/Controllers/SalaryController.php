<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Employee;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::all();

        return view('salaries.index', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::all();

        return view('salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'salary' => 'required|numeric',
            'bonuses' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'pay_date' => 'required|date',
        ]);

        $netSalary = $request->input('salary') + $request->input('bonuses', 0) - $request->input('deductions', 0);
        $request->merge(['net_salary' => $netSalary]); 

        Salary::create($request->all());

        return redirect()->route('salaries.index')->with('success', 'Salary record created successfully.');
    }

    public function edit(Salary $salary)
    {
        $employees = Employee::all();

        return view('salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        $request->validate([
            'employee_id' => 'required',
            'salary' => 'required|numeric',
            'bonuses' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'pay_date' => 'required|date',
        ]);

        $netSalary = $request->input('salary') + $request->input('bonuses', 0) - $request->input('deductions', 0);
        $request->merge(['net_salary' => $netSalary]); 

        $salary->update($request->all());

        return redirect()->route('salaries.index')->with('success', 'Salary record updated successfully.');
    }

    public function show(Salary $salary){
        return view('salaries.show', compact('salary'));
    }
}
