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

            // Akan memanggil resources/views/dashboard/hr_dashboard.blade.php
            return view('dashboard.index', compact('employee', 'department', 'salary', 'presence', 'tasks', 'isHRManager'));
            
        } else {
            // EMPLOYEE DASHBOARD (Personal Data)
            $user_role = $user->getRoleNames()->first() ?? 'Employee';
            
            $tasks_pending = Task::where('assigned_to', $user->employee_id)
                                 ->where('status', 'pending')
                                 ->count();
            
            // Akan memanggil resources/views/dashboard/employee_dashboard.blade.php
            return view('dashboard.index', compact('user_role', 'tasks_pending', 'isHRManager'));
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
            $query->where('employee_id', Auth::user()->employee_id);
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