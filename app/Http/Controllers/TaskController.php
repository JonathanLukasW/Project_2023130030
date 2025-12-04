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
        
        // Jika user TIDAK punya izin 'task_manage' (Karyawan biasa),
        // maka data tugas DIBATASI HANYA yang ditugaskan ke dia.
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
    
    // --- FUNGSI BARU: Untuk Mengambil Data Events JSON Kalender ---
    public function getEvents(Request $request)
    {
        $events = $this->getCalendarEvents();
        
        return response()->json($events);
    }
    
    // --- FUNGSI PRIVAT PEMBANTU (Helper) ---
    private function getCalendarEvents()
    {
        $query = Task::query();

        // Terapkan Logic Pembatasan Akses
        if (!Auth::user()->can('task_manage')) { 
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
                'url' => route('tasks.show', $task->id),
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
    // --- AKHIR FUNGSI BARU ---

    public function create()
    {
        if (!Auth::user()->can('task_create')) {
            abort(403, 'Unauthorized action.');
        }

        $employees = Employee::all();
        return view('tasks.create', compact('employees'));
    }
    // ... (Fungsi store, edit, update, show, destroy, done, pending TIDAK DIUBAH)
}