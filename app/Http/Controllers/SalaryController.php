<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Employee;
use App\Models\Task;
use App\Models\Presence;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalaryController extends Controller
{
    // Menampilkan daftar gaji
    public function index()
    {
        $query = Salary::with('employee');

        // LOGIC PENGAMANAN DATA:
        if (Auth::user()->can('salary_view_all')) {
            // Jika HR/Admin: Lihat SEMUA data gaji
        } else {
            // Jika Karyawan Biasa: Filter HANYA data gaji miliknya sendiri
            $query->where('employee_id', Auth::user()->employee_id);
        }

        $salaries = $query->orderBy('pay_date', 'desc')->get();
        return view('salaries.index', compact('salaries'));
    }

    // --- FITUR GENERATE OTOMATIS (INTI SISTEM) ---
    public function generateForm()
    {
        if (!Auth::user()->can('salary_view_all')) abort(403);
        return view('salaries.generate');
    }

    public function generate(Request $request)
    {
        if (!Auth::user()->can('salary_view_all')) abort(403);

        $request->validate(['month' => 'required|date_format:Y-m']); // Contoh: 2025-10

        $month = Carbon::createFromFormat('Y-m', $request->month);
        // Ambil karyawan yang aktif saja (optional, tergantung kebutuhan)
        $employees = Employee::with('position')->where('status', 'active')->get();

        // KONFIGURASI NOMINAL (Sebaiknya dipindah ke table settings nanti)
        $bonusPerFastTask = 50000;
        $penaltyPerLateTask = 50000;
        $penaltyPerAbsent = 100000;

        $count = 0;

        foreach ($employees as $emp) {
            // 1. Ambil Gaji Pokok (Prioritas: Position > Employee > 0)
            $baseSalary = $emp->position->base_salary ?? $emp->salary ?? 0;

            // 2. Hitung Denda Absensi (Hanya status 'absent' di bulan terpilih)
            $absentCount = Presence::where('employee_id', $emp->id)
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->where('status', 'absent')
                ->count();

            $deductionAbsent = $absentCount * $penaltyPerAbsent;

            // 3. Hitung Bonus/Denda Task (Berdasarkan due_date di bulan terpilih)
            $tasks = Task::where('assigned_to', $emp->id)
                ->whereMonth('due_date', $month->month)
                ->whereYear('due_date', $month->year)
                ->where('status', 'completed')
                ->get();

            $taskBonus = 0;
            $taskDeduction = 0;

            foreach ($tasks as $task) {
                if ($task->completed_at && $task->due_date) {
                    $due = Carbon::parse($task->due_date);
                    $done = Carbon::parse($task->completed_at);

                    if ($done->lt($due)) {
                        $taskBonus += $bonusPerFastTask; // Bonus Cepat
                    } elseif ($done->gt($due)) {
                        $taskDeduction += $penaltyPerLateTask; // Denda Telat
                    }
                }
            }

            // 4. Kalkulasi Akhir
            $totalBonus = $taskBonus;
            $totalDeduction = $deductionAbsent + $taskDeduction;
            $netSalary = $baseSalary + $totalBonus - $totalDeduction;

            // 5. Simpan / Update Otomatis
            Salary::updateOrCreate(
                [
                    'employee_id' => $emp->id,
                    'pay_date' => $month->copy()->endOfMonth()->format('Y-m-d'),
                ],
                [
                    'salary' => $baseSalary,
                    'bonuses' => $totalBonus,
                    'deductions' => $totalDeduction,
                    'net_salary' => max(0, $netSalary) // Mencegah minus
                ]
            );
            $count++;
        }

        return redirect()->route('salaries.index')->with('success', "Sukses! Gaji untuk $count karyawan periode " . $month->format('F Y') . " telah dihitung ulang.");
    }

    // Detail Slip Gaji
    public function show(Salary $salary)
    {
        if (!Auth::user()->can('salary_view_all') && $salary->employee_id !== Auth::user()->employee_id) {
            abort(403, 'Unauthorized action. You can only view your own salary slips.');
        }
        return view('salaries.show', compact('salary'));
    }

    // Hapus data gaji (jika perlu regenerate bersih)
    public function destroy(Salary $salary)
    {
        if (!Auth::user()->can('salary_view_all')) abort(403);

        $salary->delete();
        return redirect()->route('salaries.index')->with('success', 'Data gaji berhasil dihapus.');
    }
}
