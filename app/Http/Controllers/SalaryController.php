<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\DecryptException;

class SalaryController extends Controller
{
    /**
     * Menampilkan Daftar Gaji (History & Bulan Ini)
     */
    public function index()
    {
        // Start Query
        $query = Salary::with('employee');

        // LOGIC HAK AKSES:
        // Jika bukan 'salary_view_all' (bukan HR/Admin), 
        // maka HANYA tampilkan data milik user yang login.
        if (!Auth::user()->can('salary_view_all')) {
            $query->where('employee_id', Auth::user()->employee_id);
        }

        // Urutkan dari gaji terbaru
        $salaries = $query->orderBy('pay_date', 'desc')->get();

        return view('salaries.index', compact('salaries'));
    }

    /**
     * Tampilan Form Generate (Hanya Admin)
     */
    public function generateView()
    {
        return view('salaries.generate');
    }

    /**
     * Proses Hitung Gaji Masal (Snapshot)
     */
    public function generateProcess(Request $request)
    {
        $request->validate([
            'month' => 'required', // Format YYYY-MM
        ]);

        // Parsing Input (2025-12)
        $dateParts = explode('-', $request->month);
        $year = $dateParts[0];
        $month = $dateParts[1];
        
        // Set Tanggal Gajian (Misal tgl 28 bulan tersebut)
        $payDate = Carbon::create($year, $month, 28); 

        // 1. Cek apakah sudah pernah generate bulan ini? Biar gak dobel.
        $exists = Salary::whereYear('pay_date', $year)
                        ->whereMonth('pay_date', $month)
                        ->exists();
                        
        if ($exists) {
            return back()->with('error', 'Gaji untuk periode ' . $payDate->format('F Y') . ' sudah pernah digenerate.');
        }

        DB::transaction(function () use ($month, $year, $payDate) {
            $employees = Employee::with('position')->where('status', 'active')->get();

            foreach ($employees as $employee) {
                $baseSalary = ($employee->salary > 0) ? $employee->salary : ($employee->position->base_salary ?? 0);

                $bonusTasks = Task::where('assigned_to', $employee->id)
                    ->whereMonth('completed_at', $month)
                    ->whereYear('completed_at', $year)
                    ->where('status', 'completed')
                    ->whereColumn('completed_at', '<=', 'due_date') 
                    ->count();
                
                $bonusAmount = $bonusTasks * 50000; 

                $lateTasks = Task::where('assigned_to', $employee->id)
                    ->whereMonth('completed_at', $month)
                    ->whereYear('completed_at', $year)
                    ->where('status', 'completed')
                    ->whereColumn('completed_at', '>', 'due_date')
                    ->count();
                
                $alfas = \App\Models\Presence::where('employee_id', $employee->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('status', 'absent')
                    ->count();

                $deductionAmount = ($lateTasks * 50000) + ($alfas * 100000);

                $netSalary = $baseSalary + $bonusAmount - $deductionAmount;

                if($netSalary < 0) $netSalary = 0;

                Salary::create([
                    'employee_id' => $employee->id,
                    'salary'      => $baseSalary,
                    'bonuses'     => $bonusAmount,
                    'deductions'  => $deductionAmount,
                    'net_salary'  => $netSalary,
                    'pay_date'    => $payDate,
                ]);
            }
        });

        return redirect()->route('salaries.index')->with('success', 'Gaji periode ' . $payDate->format('F Y') . ' berhasil digenerate!');
    }

    public function slip($id)
    {
        try {
            $decryptedId = decrypt($id); 
            $salary = Salary::with(['employee.position', 'employee.department'])->findOrFail($decryptedId);

            if (!Auth::user()->can('salary_view_all') && Auth::user()->employee_id != $salary->employee_id) {
                abort(403, 'Unauthorized action.');
            }

            return view('salaries.slip', compact('salary'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function show($id)
    {
        return $this->slip($id);
    }

    public function destroy($id)
    {
        try {
            $decryptedId = decrypt($id);
            $salary = Salary::findOrFail($decryptedId);
            $salary->delete();
            
            return redirect()->route('salaries.index')->with('success', 'Data gaji berhasil dihapus.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }
}