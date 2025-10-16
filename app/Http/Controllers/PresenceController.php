<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Employee;

class PresenceController extends Controller
{
    public function index()
    {
        $presences = Presence::all();

        return view('presences.index', compact('presences'));
    }

    public function create(){
        $employees = Employee::all();

        return view('presences.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'date' => 'required|date',
            'status' => 'required|string',
        ]);

        Presence::create($validated);

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
            'employee_id' => 'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'date' => 'required|date',
            'status' => 'required|string',
        ]);

        $presence->update($validated);

        return redirect()->route('presences.index')->with('success', 'Presence updated successfully.');
    }
}
