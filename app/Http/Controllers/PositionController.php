<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position; 

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::all(); 
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        return view('positions.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_salary' => 'required|numeric', // <-- Validasi baru
        ]);

        Position::create($request->all());

        return redirect()->route('positions.index')->with('success', 'Position created successfully.');
    }

    public function edit(Position $position){
        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_salary' => 'required|numeric', // <-- Validasi baru
        ]);

        $position->update($request->all());

        return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position){
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
    }
}