<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Salary;
use App\Models\Presence;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = Employee::count();
        $department = Department::count();
        $salary = Salary::count();
        $presence = Presence::count();
        $tasks = Task::all();

        return view('dashboard.index', compact('employee', 'department', 'salary', 'presence', 'tasks'));
    }

    public function presence()
    {
        $data = Presence::where('status', 'present')
            ->selectRaw('MONTH(date) as month, YEAR(date) as year, COUNT(*) as total_present')
            ->groupBy('year', 'month')
            ->orderBy('month', 'asc')
            ->get();

        $temp = [];
        $i = 0;
        foreach ($data as $item) {
            $item[$i] = $item->total_present;
            $i++;
        }

        return response()->json($temp);
    }
}
