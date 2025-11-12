<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Employee;
// --- PERUBAHAN 1: Tambahkan 'Auth' ---
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function index()
    {
        // --- PERUBAHAN 2: Ganti logic 'session' ke 'Auth::user()->can()' ---
        $query = Salary::with('employee'); // Eager load employee

        // Cek Izin Spatie (Izin 'salary_view_all' dari seeder-mu)
        if (Auth::user()->can('salary_view_all')) {
            // Admin/HR (yang punya izin) bisa lihat semua gaji
        } else {
            // Karyawan biasa hanya bisa lihat datanya sendiri
            $query->where('employee_id', Auth::user()->employee_id);
        }
        
        // Urutkan berdasarkan tanggal bayar terbaru
        $salaries = $query->orderBy('pay_date', 'desc')->get();
        return view('salaries.index', compact('salaries'));
    }

    public function create()
    {
        // (Aman, fungsi ini sudah dilindungi 'salary_view_all' di web.php)
        $employees = Employee::all();
        return view('salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // (Aman, fungsi ini sudah dilindungi 'salary_view_all' di web.php)
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
        // (Aman, fungsi ini sudah dilindungi 'salary_view_all' di web.php)
        $employees = Employee::all();
        return view('salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        // (Aman, fungsi ini sudah dilindungi 'salary_view_all' di web.php)
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

    public function show(Salary $salary)
    {

        if (!Auth::user()->can('salary_view_all') && $salary->employee_id !== Auth::user()->employee_id) {
            abort(403, 'Unauthorized action. You can only view your own salary slips.');
        }

        return view('salaries.show', compact('salary'));
    }

    public function destroy(Salary $salary)
    {
        // (Aman, fungsi ini sudah dilindungi 'salary_view_all' di web.php)
        $salary->delete();

        return redirect()->route('salaries.index')->with('success', 'Salary record deleted successfully.');
    }  
}