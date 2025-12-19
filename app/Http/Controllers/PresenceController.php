<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Presence::with('employee');

        if ($request->filled('employee_id') && Auth::user()->can('presence_view_all')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }
        if (!Auth::user()->can('presence_view_all')) {
            $query->where('employee_id', Auth::user()->employee_id);
        }

        $presences = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        $employees = Employee::orderBy('fullname')->get();

        return view('presences.index', compact('presences', 'employees'));
    }

    public function create()
    {
        $employees = Employee::orderBy('fullname')->get();
        
        $isCheckIn = true; 
        $todayPresence = null;

        if (!Auth::user()->can('presence_view_all')) {
            $todayPresence = Presence::where('employee_id', Auth::user()->employee_id)
                ->where('date', Carbon::now()->format('Y-m-d'))
                ->first();

            if ($todayPresence && $todayPresence->check_in && is_null($todayPresence->check_out)) {
                $isCheckIn = false; 
            }
        }

        return view('presences.create', compact('employees', 'isCheckIn', 'todayPresence'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('presence_view_all')) {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date_format:Y-m-d',
                'status' => 'required|in:present,absent,leave',
            ]);

            $checkIn = null;
            $checkOut = null;

            if ($validated['status'] == 'present') {

                $currentReviewTime = now()->format('H:i:s');
                $checkIn = Carbon::parse($validated['date'] . ' ' . $currentReviewTime);
            }

            Presence::create([
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
                'status' => $validated['status'],
                'check_in' => $checkIn,
                'check_out' => null,
            ]);
            
            return redirect()->route('presences.index')->with('success', 'Data presensi berhasil disimpan (Manual).');
        } 

        else {
            $employeeId = Auth::user()->employee_id;
            $today = Carbon::now()->format('Y-m-d');
            $type = $request->input('type'); 

            if ($type == 'out') {
                $presence = Presence::where('employee_id', $employeeId)->where('date', $today)->first();
                if ($presence) {
                    $presence->update(['check_out' => Carbon::now()]);
                    return redirect()->route('presences.index')->with('success', 'Berhasil Check-out!');
                }
                return redirect()->back()->with('error', 'Data check-in tidak ditemukan.');
            } else {
                $exists = Presence::where('employee_id', $employeeId)->where('date', $today)->exists();
                if ($exists) {
                    return redirect()->route('presences.index')->with('error', 'Anda sudah absen hari ini.');
                }

                $request->validate([
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'photo' => 'required',
                ]);

                $photoPath = null;
                if ($request->filled('photo')) {
                    $image = $request->photo;
                    if (strpos($image, 'base64,') !== false) {
                        $image = explode('base64,', $image)[1];
                    }
                    $image = str_replace(' ', '+', $image);
                    $imageData = base64_decode($image);
                    $fileName = 'presence_' . $employeeId . '_' . time() . '.jpeg';
                    Storage::disk('public')->put('presences/' . $fileName, $imageData);
                    $photoPath = 'presences/' . $fileName;
                }

                Presence::create([
                    'employee_id' => $employeeId,
                    'date' => $today,
                    'check_in' => Carbon::now(),
                    'status' => 'present',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'photo' => $photoPath
                ]);

                return redirect()->route('presences.index')->with('success', 'Berhasil Check-in!');
            }
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = decrypt($id);
            $presence = Presence::findOrFail($decryptedId);
            $employees = Employee::orderBy('fullname')->get();
            return view('presences.edit', compact('presence', 'employees'));
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function update(Request $request, $id)
    {
       try {
            $decryptedId = decrypt($id);
            $presence = Presence::findOrFail($decryptedId);

            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'status' => 'required|in:present,absent,leave',
                'check_in' => 'nullable|date',
                'check_out' => 'nullable|date',
            ]);

            if ($validated['status'] != 'present') {
                $validated['check_in'] = null;
                $validated['check_out'] = null;
            }

            $presence->update($validated);
            
            return redirect()->route('presences.index')->with('success', 'Data absensi berhasil diperbarui.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = decrypt($id);
            $presence = Presence::findOrFail($decryptedId);
            $presence->delete();
            return redirect()->route('presences.index')->with('success', 'Presence deleted successfully.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }
}