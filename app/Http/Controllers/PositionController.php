<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;

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
            'base_salary' => 'required|numeric|min:0',
        ]);

        Position::create($request->all());

        return redirect()->route('positions.index')->with('success', 'Position created successfully.');
    }

    public function edit($id)
    {
        try {
            $decryptedId = decrypt($id);
            $position = Position::findOrFail($decryptedId);
            return view('positions.edit', compact('position'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $decryptedId = decrypt($id);
            $position = Position::findOrFail($decryptedId);

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'base_salary' => 'required|numeric|min:0',
            ]);

            $position->update($request->all());

            return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = decrypt($id);
            $position = Position::findOrFail($decryptedId);
            $position->delete();
            return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }
}