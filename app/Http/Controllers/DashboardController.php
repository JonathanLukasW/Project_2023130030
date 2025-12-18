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
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Memastikan method role tersedia
        $isHRManager = $user->hasRole('HR Manager');

        if ($isHRManager) {
            // HR MANAGER DASHBOARD (All Data)
            $employee = Employee::count();
            $department = Department::count();
            $salary = Salary::count();
            $presence = Presence::count();
            
            $tasks = Task::with('employee')->limit(5)->get(); 

            // PERBAIKAN DI SINI: Arahkan ke 'dashboard.hr_dashboard'
            return view('dashboard.hr_dashboard', compact('employee', 'department', 'salary', 'presence', 'tasks', 'isHRManager'));
            
        } else {
            // EMPLOYEE DASHBOARD (Personal Data)
            $user_role = $user->getRoleNames()->first() ?? 'Employee';
            
            // Cek apakah user terhubung ke data employee
            $employeeId = $user->employee_id;

            if ($employeeId) {
                $tasks_pending = Task::where('assigned_to', $employeeId)
                                     ->where('status', 'pending')
                                     ->count();
            } else {
                $tasks_pending = 0;
            }
            
            // PERBAIKAN DI SINI: Arahkan ke 'dashboard.employee_dashboard'
            return view('dashboard.employee_dashboard', compact('user_role', 'tasks_pending', 'isHRManager'));
        }
    }

    /**
     * AJAX endpoint untuk Chart.js. Sekarang mendukung filter Personal.
     */
    public function presence(Request $request)
    {
        $targetYear = 2025;
        $query = Presence::where('status', 'present')->whereYear('date', $targetYear);
        
        if ($request->get('employee')) {
            // Pastikan user punya employee_id sebelum query
            if (Auth::user()->employee_id) {
                $query->where('employee_id', Auth::user()->employee_id);
            } else {
                // Jika user tidak punya data employee (misal admin murni), kembalikan 0
                return response()->json(array_fill(0, 12, 0));
            }
        }

        $rawMonthlyData = $query
            ->select(DB::raw('MONTH(date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy(DB::raw('MONTH(date)'), 'ASC')
            ->pluck('total', 'month')
            ->toArray();

        $dataForChart = array_fill(0, 12, 0); 
        
        foreach ($rawMonthlyData as $month => $total) {
            $monthIndex = $month - 1; 
            
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $dataForChart[$monthIndex] = $total; 
            }
        }

        return response()->json($dataForChart);
    }
}