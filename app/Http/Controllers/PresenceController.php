<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Presence::with('employee');

        // Filter untuk Admin
        if ($request->filled('employee_id') && Auth::user()->can('presence_view_all')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        // Batasan Akses Karyawan Biasa
        if (!Auth::user()->can('presence_view_all')) {
            $query->where('employee_id', Auth::user()->employee_id);
        }

        $presences = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        $employees = Employee::orderBy('fullname')->get();

        return view('presences.index', compact('presences', 'employees'));
    }

    public function create()
    {
        $employees = Employee::all();
        
        // Cek status absen hari ini untuk Karyawan
        $isCheckIn = true; // Default
        $todayPresence = null;

        if (!Auth::user()->can('presence_view_all')) {
            $todayPresence = Presence::where('employee_id', Auth::user()->employee_id)
                ->where('date', Carbon::now()->format('Y-m-d'))
                ->first();

            if ($todayPresence && $todayPresence->check_in && is_null($todayPresence->check_out)) {
                $isCheckIn = false; // Berarti saatnya Check Out
            }
        }

        return view('presences.create', compact('employees', 'isCheckIn', 'todayPresence'));
    }

    public function store(Request $request)
    {
        // === LOGIC ADMIN (Manual) ===
        if (Auth::user()->can('presence_view_all')) {
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
            return redirect()->route('presences.index')->with('success', 'Data presensi berhasil disimpan (Manual).');
        } 
        
        // === LOGIC KARYAWAN (Check-in / Check-out) ===
        else {
            $employeeId = Auth::user()->employee_id;
            $today = Carbon::now()->format('Y-m-d');

            // Cek apakah ini Check-in atau Check-out
            $type = $request->input('type'); // 'in' atau 'out'

            if ($type == 'out') {
                // --- PROSES CHECK OUT ---
                $presence = Presence::where('employee_id', $employeeId)
                    ->where('date', $today)
                    ->first();
                
                if ($presence) {
                    $presence->update([
                        'check_out' => Carbon::now(),
                    ]);
                    return redirect()->route('presences.index')->with('success', 'Berhasil Check-out! Hati-hati di jalan.');
                } else {
                    return redirect()->back()->with('error', 'Data check-in tidak ditemukan hari ini.');
                }

            } else {
                // --- PROSES CHECK IN ---
                
                // 1. Cek dulu jangan sampai double check-in
                $exists = Presence::where('employee_id', $employeeId)->where('date', $today)->exists();
                if ($exists) {
                    return redirect()->route('presences.index')->with('error', 'Anda sudah absen hari ini.');
                }

                // 2. Validasi Input
                $request->validate([
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'photo' => 'required', // Foto Wajib
                ]);

                // 3. Proses Simpan Foto
                $photoPath = null;
                if ($request->filled('photo')) {
                    $image = $request->photo;  // data:image/jpeg;base64,...
                    
                    // Decode Base64 sederhana
                    if (strpos($image, 'base64,') !== false) {
                        $image = explode('base64,', $image)[1];
                    }
                    $image = str_replace(' ', '+', $image);
                    $imageData = base64_decode($image);

                    // Buat nama file unik
                    $fileName = 'presence_' . $employeeId . '_' . time() . '.jpeg';
                    
                    // Simpan ke storage/app/public/presences
                    Storage::disk('public')->put('presences/' . $fileName, $imageData);
                    
                    $photoPath = 'presences/' . $fileName;
                }

                // 4. Simpan ke Database
                Presence::create([
                    'employee_id' => $employeeId,
                    'date' => $today,
                    'check_in' => Carbon::now(),
                    'check_out' => null, // Masih kosong
                    'status' => 'present',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'photo' => $photoPath
                ]);

                return redirect()->route('presences.index')->with('success', 'Berhasil Check-in! Selamat bekerja.');
            }
        }
    }

    // ... method edit, update, destroy biarkan sama seperti sebelumnya ...
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