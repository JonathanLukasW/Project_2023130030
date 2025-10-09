<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;

class PresenceController extends Controller
{
    public function index()
    {
        $presences = Presence::all();

        return view('tasks.index', compact('tasks'));
    }
}
