<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();

        return view('tasks.index', compact('tasks'));
    }

    public function create(){

        $employees = Employee::all();

        return view('tasks.create', compact('employees'));
    }

    public function store(Request $request){
        
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

    public function edit(Task $task){
        $employees = Employee::all();

        return view('tasks.edit', compact('task', 'employees'));
    }

    public function update(Request $request, task $task){
        
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

    public function show(Task $task) {
        return view('tasks.show', compact('task'));

    }
}
