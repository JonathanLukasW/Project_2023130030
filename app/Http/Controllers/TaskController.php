<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\DecryptException;

class TaskController extends Controller
{
    public function index()
    {
        $query = Task::with('employee');

        if (!Auth::user()->can('task_manage')) {
            if (Auth::user()->employee_id) {
                $query->where('assigned_to', Auth::user()->employee_id);
            } else {

                $query->where('id', 0); 
            }
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('fullname')->get();
        return view('tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:employees,id',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,completed,canceled',
        ]);

        Task::create($request->all());

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show($id)
    {
        try {
            $decryptedId = decrypt($id);
            $task = Task::with('employee')->findOrFail($decryptedId);
            return view('tasks.show', compact('task'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = decrypt($id);
            $task = Task::findOrFail($decryptedId);
            $employees = Employee::where('status', 'active')->get();
            return view('tasks.edit', compact('task', 'employees'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $decryptedId = decrypt($id);
            $task = Task::findOrFail($decryptedId);

            $request->validate([
                'title' => 'required|string|max:255',
                'assigned_to' => 'required|exists:employees,id',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
                'status' => 'required|in:pending,completed,canceled',
            ]);
            if ($request->status == 'completed' && $task->status != 'completed') {
                $task->completed_at = now();
            } 
            elseif ($request->status != 'completed') {
                $task->completed_at = null;
            }

            $task->update([
                'title' => $request->title,
                'assigned_to' => $request->assigned_to,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'completed_at' => $task->completed_at
            ]);

            return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = decrypt($id);
            $task = Task::findOrFail($decryptedId);
            $task->delete();
            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function calendarEvents(Request $request)
    {
        $events = $this->getCalendarEvents(false);
        return response()->json($events);
    }

    private function getCalendarEvents(bool $isPersonal = false)
    {
        $query = Task::query();

        if ($isPersonal || !Auth::user()->can('task_manage')) { 
            if (Auth::check() && Auth::user()->employee_id) {
                 $query->where('assigned_to', Auth::user()->employee_id);
            } else {
                 return [];
            }
        }
        
        if (request()->has('employee_id') && request()->employee_id != 'all') {
            $query->where('assigned_to', request()->employee_id);
        }

        if (request()->has('start') && request()->has('end')) {
            $query->whereBetween('due_date', [request()->start, request()->end]);
        }
        
        $tasks = $query->get();
        $events = [];

        foreach ($tasks as $task) {
            $colorClass = match ($task->status) {
                'pending' => '#ffc107',  
                'completed' => '#198754', 
                default => '#6c757d', 
            };

            $events[] = [
                'id' => $task->id,
                'title' => $task->title, 
                'start' => $task->due_date,
                'url' => route('tasks.show', encrypt($task->id)), 
                'backgroundColor' => $colorClass,
                'borderColor' => $colorClass,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'employee' => $task->employee->fullname ?? '-',
                    'description' => $task->description ?? 'Tidak ada deskripsi',
                    'status' => ucfirst($task->status),
                    'due_date_fmt' => \Carbon\Carbon::parse($task->due_date)->format('d F Y'),
                    'detail_url' => route('tasks.show', encrypt($task->id))
                ]
            ];
        }
        
        return $events;
    }
}