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
        $baseQuery = Task::with('employee');
        if (!Auth::user()->can('task_view_all')) {
            $baseQuery->where('assigned_to', Auth::user()->employee_id);
        }

        $tasks = $baseQuery->get();

        $tasks = $tasks->sortBy(fn ($task) => $task->status === 'pending' ? 1 : 2)->values();
        
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        if (!Auth::user()->can('task_create')) {
            abort(403, 'Unauthorized action.');
        }

        $employees = Employee::all();
        return view('tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('task_create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:employees,id',
            'due_date' => 'required|date',
            'status' => 'required|string'
        ]);

        Task::create($validated);
        return redirect()->route('tasks.index')->with('success', 'Task created succesfully');
    }

    public function edit(Task $task)
    {
        // Otorisasi dicek di Route (task_edit)
        if (!Auth::user()->can('task_edit')) {
            abort(403, 'Unauthorized action.');
        }
        
        $employees = Employee::all();
        return view('tasks.edit', compact('task', 'employees'));
    }

    public function update(Request $request, task $task)
    {
        if (!Auth::user()->can('task_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:employees,id',
            'due_date' => 'required|date',
            'status' => 'required|string'
        ]);

        $task->update($validated);
        return redirect()->route('tasks.index')->with('success', 'Task updated succesfully');
    }

    public function show(Task $task)
    {
        if (!Auth::user()->can('task_view_all') && $task->assigned_to !== Auth::user()->employee_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('tasks.show', compact('task'));
    }

    public function done(int $id)
    {
        if (!Auth::user()->can('task_mark_status')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::findOrFail($id);
        $task->update(['status' => 'completed']);
        return redirect()->route('tasks.index')->with('success', 'Task marked as done successfully');
    }
    
    public function pending(int $id)
    {
        if (!Auth::user()->can('task_mark_status')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::findOrFail($id);
        $task->update(['status' => 'pending']);
        return redirect()->route('tasks.index')->with('success', 'Task marked as pending successfully');
    }

    public function destroy(Task $task)
    {
        if (!Auth::user()->can('task_delete')) {
            abort(403, 'Unauthorized action.');
        }
        
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted succesfully');
    }
}