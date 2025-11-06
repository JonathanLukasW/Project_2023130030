<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $userRole = session('role');
        $employeeId = session('employee_id');
        $baseQuery = Task::with('employee');
        
        if (!($userRole === 'HR Manager')) {
            $baseQuery->where('assigned_to', $employeeId);
        }

        $tasks = $baseQuery->get();
        $tasks = $tasks->sortBy(function ($task) {
            return $task->status === 'pending' ? 1 : 2;
        })->values(); 
        return view('tasks.index', compact('tasks'));
    }
    
    public function create()
    {

        $employees = Employee::all();

        return view('tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|string'
        ]);

        Task::create($validated);
        return redirect()->route('tasks.index')->with('success', 'Task created succesfully');
    }

    public function edit(Task $task)
    {
        $employees = Employee::all();

        return view('tasks.edit', compact('task', 'employees'));
    }

    public function update(Request $request, task $task)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|string'
        ]);

        $task->update($validated);
        return redirect()->route('tasks.index')->with('success', 'Task updated succesfully');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function done(int $id)
    {
        $task = Task::find($id);
        $task->update(['status' => 'completed']);

        return redirect()->route('tasks.index')->with('success', 'Task marked as done successfully');
    }
    
    public function pending(int $id)
    {
        $task = Task::find($id);
        $task->update(['status' => 'pending']);

        return redirect()->route('tasks.index')->with('success', 'Task marked as pending successfully');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted succesfully');
    }
}