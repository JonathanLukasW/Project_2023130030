<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Salary;
use App\Models\Presence;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = Employee::count();
        $department = Department::count();
        $salary = Salary::count();
        $presence = Presence::count();
        
        // Mengambil tugas untuk ditampilkan di tabel ringkasan
        $tasks = Task::with('employee')->limit(5)->get(); 

        return view('dashboard.index', compact('employee', 'department', 'salary', 'presence', 'tasks'));
    }

    /**
     * MENGEMBALIKAN FUNGSI PRESENCE (AJAX endpoint untuk Chart.js).
     */
    public function presence()
    {
        $targetYear = 2025; 
        
        // Query untuk mengambil total kehadiran per bulan
        $rawMonthlyData = DB::select("
            SELECT MONTH(date) as month, COUNT(*) as total
            FROM presences
            WHERE status = 'present' AND YEAR(date) = ?
            GROUP BY MONTH(date)
            ORDER BY MONTH(date) ASC
        ", [$targetYear]);

        $dataForChart = array_fill(0, 12, 0); 
        
        foreach ($rawMonthlyData as $item) {
            $monthIndex = $item->month - 1; 
            
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $dataForChart[$monthIndex] = $item->total; 
            }
        }

        return response()->json($dataForChart);
    }
}