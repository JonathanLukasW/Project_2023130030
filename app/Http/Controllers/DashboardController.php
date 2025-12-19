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

        $isHRManager = $user->hasRole('HR Manager');

        if ($isHRManager) {
            $employee = Employee::count();
            $department = Department::count();
            $salary = Salary::count();
            $presence = Presence::count();

            $tasks = Task::with('employee')->limit(5)->get();

            return view('dashboard.hr_dashboard', compact('employee', 'department', 'salary', 'presence', 'tasks', 'isHRManager'));
        } else {
            $user_role = $user->getRoleNames()->first() ?? 'Employee';
            $employeeId = $user->employee_id;

            if ($employeeId) {
                $tasks_pending = Task::where('assigned_to', $employeeId)
                    ->where('status', 'pending')
                    ->count();
            } else {
                $tasks_pending = 0;
            }
            return view('dashboard.employee_dashboard', compact('user_role', 'tasks_pending', 'isHRManager'));
        }
    }

    public function presence(Request $request)
    {
        $query = Presence::query();

        if ($request->has('employee_id') && $request->employee_id != 'all') {
            $query->where('employee_id', $request->employee_id);
        } elseif (!Auth::user()->hasRole('HR Manager')) {
            $query->where('employee_id', Auth::user()->employee_id);
        }
        if ($request->has('month') && $request->month != 'all') {
            $query->whereMonth('date', $request->month);
        }
        if ($request->has('year') && $request->year != 'all') {
            $query->whereYear('date', $request->year);
        } else {
            $query->whereYear('date', date('Y'));
        }

        $totalPresent = (clone $query)->where('status', 'present')->count();
        $totalAbsent  = (clone $query)->where('status', 'absent')->count();
        $totalLeave   = (clone $query)->where('status', 'leave')->count();

        return response()->json([
            'labels' => ['Hadir', 'Alfa', 'Cuti/Izin'],
            'data'   => [$totalPresent, $totalAbsent, $totalLeave]
        ]);
    }
}
