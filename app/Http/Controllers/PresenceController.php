<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator; 

class PresenceController extends Controller
{
    public function index()
    {
        if (session('position') == 'HR Manager') {
            $presences = Presence::with('employee')->get();
        } else {
            $presences = Presence::with('employee')->where('employee_id', session('employee_id'))->get();
        }
        return view('presences.index', compact('presences'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('presences.create', compact('employees'));
    }

    public function store(Request $request)
    {
        if (session('position') == 'HR Manager') {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'check_in' => 'nullable|date_format:Y-m-d H:i:s', 
                'check_out' => 'nullable|date_format:Y-m-d H:i:s', 
                'date' => 'required|date_format:Y-m-d',
                'status' => 'required|in:present,absent,leave',
            ]);

            if ($validated['status'] !== 'present') {
                $validated['check_in'] = null;
                $validated['check_out'] = null;
            }
            
            Presence::create($validated);
        } else {
            Presence::create([
                'employee_id' => session('employee_id'),
                'check_in' => Carbon::now()->format('Y-m-d H:i:s'),
                'date' => Carbon::now()->format('Y-m-d'),
                'status' => 'present',
            ]);
        }
        
        return redirect()->route('presences.index')->with('success', 'Presence recorded successfully.');
    }

    public function edit(Presence $presence)
    {
        $employees = Employee::all();
        return view('presences.edit', compact('presence', 'employees'));
    }

    public function update(Request $request, Presence $presence)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'check_in' => 'nullable|date_format:Y-m-d H:i:s', 
            'check_out' => 'nullable|date_format:Y-m-d H:i:s', 
            'date' => 'required|date_format:Y-m-d',
            'status' => 'required|in:present,absent,leave',
        ]);
        if ($validated['status'] !== 'present') {
            $validated['check_in'] = null;
            $validated['check_out'] = null;
        }

        $presence->update($validated);

        return redirect()->route('presences.index')->with('success', 'Presence updated successfully.');
    }

    public function destroy(Presence $presence){
        $presence->delete();

        return redirect()->route('presences.index')->with('success', 'Presence deleted successfully.');
    }
}