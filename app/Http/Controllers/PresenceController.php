<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use Illuminate\Http\Request;
use Carbon\Carbon;
// --- PERUBAHAN 1: Tambahkan 'Auth' ---
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PresenceController extends Controller
{
    public function index()
    {
        // --- PERUBAHAN 2: Ganti logic 'session' ke 'Auth::user()->can()' ---
        $query = Presence::with('employee');

        // Cek Izin (Permission) Spatie, bukan Session!
        // Izin 'presence_view_all' ini sudah kita set di web.php.
        if (Auth::user()->can('presence_view_all')) {
            // Kalau punya izin, dia bisa lihat semua (tidak perlu 'where')
        } else {
            // Kalau tidak punya izin, dia HANYA bisa lihat datanya sendiri
            // (Kita pakai Auth::user()->employee_id yang JAUH LEBIH AMAN daripada session)
            $query->where('employee_id', Auth::user()->employee_id);
        }

        $presences = $query->get();
        return view('presences.index', compact('presences'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('presences.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // --- PERUBAHAN 3: Ganti logic 'session' ke 'Auth::user()->can()' ---
        
        // Cek Izin Spatie. Jika 'presence_view_all' (admin/HR), dia bisa input manual
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
        
        } else {
            // --- PERUBAHAN 4: Ini adalah logic untuk KARYAWAN BIASA (non-admin) ---
            // Karyawan biasa HANYA bisa absen untuk diri sendiri (pakai GPS)

            $validated = $request->validate([
                 'latitude' => 'required',
                 'longitude' => 'required',
            ]);

            // (Kamu bisa tambahkan validasi jarak GPS di sini jika perlu)

            Presence::create([
                // Ambil ID dari Auth yang login, JANGAN dari session
                'employee_id' => Auth::user()->employee_id, 
                'check_in' => Carbon::now()->format('Y-m-d H:i:s'),
                'date' => Carbon::now()->format('Y-m-d'),
                'status' => 'present',
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
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
        // (Asumsi: Hanya yg punya 'presence_view_all' yg bisa 'update' - ini sudah diatur di web.php)
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