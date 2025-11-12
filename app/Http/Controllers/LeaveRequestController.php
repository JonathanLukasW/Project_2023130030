<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Employee;
// --- PERUBAHAN 1: Tambahkan 'Auth' ---
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        // --- PERUBAHAN 2: Ganti logic 'session' ke 'Auth::user()->can()' ---
        $query = LeaveRequest::with('employee');

        // Cek pakai Izin (Permission) Spatie, bukan Session!
        // Kita gunakan 'leave_confirm_reject' sebagai penanda "Admin" di modul ini
        // (Berdasarkan Seeder-mu, hanya 'HR Manager' yang punya izin ini)
        if (Auth::user()->can('leave_confirm_reject')) {
            // HR Manager (atau siapa pun yg bisa confirm/reject) bisa lihat semua.
        } else {
            // Karyawan biasa hanya bisa lihat datanya sendiri.
            $query->where('employee_id', Auth::user()->employee_id);
        }
        
        $leaveRequests = $query->orderBy('start_date', 'desc')->get();
        return view('leave-requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('leave-requests.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // --- PERUBAHAN 3: Ganti logic 'session' ke 'Auth::user()->can()' ---
        
        // Cek pakai Izin (Permission) Spatie
        if (Auth::user()->can('leave_confirm_reject')) {
            // Admin/HR bisa membuat cuti untuk karyawan lain
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'leave_type' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
            
            $data = $validated;
            $data['status'] = 'pending'; // Set status default

            LeaveRequest::create($data);

        } else {
            // Karyawan biasa membuat cuti untuk diri sendiri
            $validated = $request->validate([
                'leave_type' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            LeaveRequest::create([
                'employee_id' => Auth::user()->employee_id, // Ambil dari Auth
                'leave_type' => $validated['leave_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('leave-requests.index')->with('success', 'Leave request created successfully.');
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        // --- PERUBAHAN 4: TAMBAHAN KEAMANAN ---
        // Cek otorisasi:
        // 1. User adalah admin (bisa confirm/reject)
        // ATAU
        // 2. Ini adalah request miliknya DAN statusnya masih 'pending'
        if (!Auth::user()->can('leave_confirm_reject') && 
            ($leaveRequest->employee_id !== Auth::user()->employee_id || $leaveRequest->status !== 'pending')) 
        {
            abort(403, 'Unauthorized action. You can only edit your own pending requests.');
        }
        
        $employees = Employee::all();
        return view('leave-requests.edit', compact('leaveRequest', 'employees'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // --- PERUBAHAN 5: TAMBAHAN KEAMANAN ---
        // Cek otorisasi yang sama
        if (!Auth::user()->can('leave_confirm_reject') && 
            ($leaveRequest->employee_id !== Auth::user()->employee_id || $leaveRequest->status !== 'pending')) 
        {
            abort(403, 'Unauthorized action.');
        }

        // Admin bisa ganti 'employee_id'
        if (Auth::user()->can('leave_confirm_reject')) {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'leave_type' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
        } else {
        // Karyawan biasa tidak bisa ganti 'employee_id'
            $validated = $request->validate([
                'leave_type' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
            // Pastikan 'employee_id' tidak di-override
            $validated['employee_id'] = Auth::user()->employee_id;
        }

        $leaveRequest->update($validated);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully.');
    }

    public function confirm(int $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->update(['status' => 'approved']); 

        return redirect()->route('leave-requests.index')->with('success', 'Leave request confirmed successfully.');
    }

    public function reject(int $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->update(['status' => 'rejected']); 

        return redirect()->route('leave-requests.index')->with('success', 'Leave request rejected successfully.');
    }

    public function destroy(LeaveRequest $leaveRequest){
        if (!Auth::user()->can('leave_confirm_reject') && 
            ($leaveRequest->employee_id !== Auth::user()->employee_id || $leaveRequest->status !== 'pending')) 
        {
            abort(403, 'Unauthorized action. You can only delete your own pending requests.');
        }

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted successfully.');
    }   
}