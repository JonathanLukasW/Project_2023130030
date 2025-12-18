<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // <-- PENTING: Tambahkan Log

class TaskController extends Controller
{
    public function index()
    {
        $baseQuery = Task::with('employee');
        
        if (!Auth::user()->can('task_manage')) { 
            if (Auth::check() && Auth::user()->employee_id) {
                 $baseQuery->where('assigned_to', Auth::user()->employee_id);
            } else {
                 $baseQuery->whereRaw('1 = 0'); 
            }
        }
        
        $tasks = $baseQuery->get();
        $tasks = $tasks->sortBy(fn ($task) => $task->status === 'pending' ? 1 : 2)->values();
        
        return view('tasks.index', compact('tasks'));
    }

    public function getEvents(Request $request)
    {
        $isPersonalRequest = $request->has('personal') && Auth::check() && !Auth::user()->can('task_manage');
        $events = $this->getCalendarEvents($isPersonalRequest);
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
        
        $tasks = $query->get();
        $events = [];

        foreach ($tasks as $task) {
            $events[] = [
                'date' => $task->due_date, 
                'title' => $task->title,
                'url' => route('tasks.show', encodeId($task->id)), 
                'color' => $this->getEventColor($task->status), 
            ];
        }
        return $events;
    }
    
    private function getEventColor($status)
    {
        return match ($status) {
            'pending' => '#ffc107', 
            'completed' => '#198754', 
            default => '#6c757d', 
        };
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
            'due_date' => 'required|date_format:Y-m-d\TH:i', 
            'status' => 'required|string'
        ]);

        $validated['due_date'] = Carbon::createFromFormat('Y-m-d\TH:i', $validated['due_date'])->format('Y-m-d H:i:s');
        
        $task = Task::create($validated);

        // Jika status langsung completed saat create, set completed_at
        if ($task->status == 'completed') {
            $task->update(['completed_at' => now()]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        if (!Auth::user()->can('task_edit')) {
            abort(403, 'Unauthorized action.');
        }
        
        $employees = Employee::all();
        return view('tasks.edit', compact('task', 'employees'));
    }

    public function update(Request $request, Task $task)
    {
        if (!Auth::user()->can('task_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:employees,id',
            'due_date' => 'required|date_format:Y-m-d\TH:i',
            'status' => 'required|string'
        ]);
        
        $validated['due_date'] = Carbon::createFromFormat('Y-m-d\TH:i', $validated['due_date'])->format('Y-m-d H:i:s');

        // Logic Update Status Manual
        if ($validated['status'] == 'completed' && $task->status != 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] == 'pending') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function show(string $encodedId)
    {
        $taskId = decodeId($encodedId);
        if (!$taskId) {
            abort(404, 'Task not found.');
        }
        $task = Task::findOrFail($taskId);
        
        if (!Auth::user()->can('task_manage') && $task->assigned_to !== Auth::user()->employee_id) {
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
        
        // UPDATE BARU: Simpan waktu selesai
        $task->update([
            'status' => 'completed',
            'completed_at' => now() 
        ]);
        
        return redirect()->route('tasks.index')->with('success', 'Task marked as done successfully');
    }
    
    public function pending(int $id)
    {
        if (!Auth::user()->can('task_mark_status')) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::findOrFail($id);
        
        // Kalau balik ke pending, completed_at dihapus
        $task->update([
            'status' => 'pending',
            'completed_at' => null
        ]);
        
        return redirect()->route('tasks.index')->with('success', 'Task marked as pending successfully');
    }

    public function destroy(Task $task)
    {
        if (!Auth::user()->can('task_delete')) {
            abort(403, 'Unauthorized action.');
        }
        
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}