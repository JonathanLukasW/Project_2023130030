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
        $query = LeaveRequest::with('employee');

        // Cek izin
        if (!Auth::user()->can('leave_confirm_reject')) {
            $query->where('employee_id', Auth::user()->employee_id);
        }
        
        $leaveRequests = $query->orderBy('start_date', 'desc')->get();

        // --- TAMBAHAN: Ambil data employees untuk dropdown filter/create di view index ---
        $employees = Employee::orderBy('fullname', 'asc')->get(); 

        // Kirim $employees ke view
        return view('leave-requests.index', compact('leaveRequests', 'employees'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('leave-requests.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // ... validasi yang sudah ada ...

        // Tambah validasi file (opsional, max 2MB, gambar/pdf)
        $request->validate([
            // ... rule lain ...
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Siapkan data
        $data = $request->except(['attachment']); // Ambil semua kecuali file dulu

        // Logic Upload File
        if ($request->hasFile('attachment')) {
            // Simpan ke folder 'public/leave_attachments'
            $path = $request->file('attachment')->store('leave_attachments', 'public');
            $data['attachment'] = $path;
        }

        $data['status'] = 'pending';

        // Logic Employee ID otomatis (jika user biasa) -> sama seperti kodemu sebelumnya
        if (!Auth::user()->can('leave_confirm_reject')) {
            $data['employee_id'] = Auth::user()->employee_id;
        }

        LeaveRequest::create($data);

        return redirect()->route('leave-requests.index')->with('success', 'Request created.');
    }
    public function edit(LeaveRequest $leaveRequest)
    {
        // --- PERUBAHAN 4: TAMBAHAN KEAMANAN ---
        // Cek otorisasi:
        // 1. User adalah admin (bisa confirm/reject)
        // ATAU
        // 2. Ini adalah request miliknya DAN statusnya masih 'pending'
        if (
            !Auth::user()->can('leave_confirm_reject') &&
            ($leaveRequest->employee_id !== Auth::user()->employee_id || $leaveRequest->status !== 'pending')
        ) {
            abort(403, 'Unauthorized action. You can only edit your own pending requests.');
        }

        $employees = Employee::all();
        return view('leave-requests.edit', compact('leaveRequest', 'employees'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // --- PERUBAHAN 5: TAMBAHAN KEAMANAN ---
        // Cek otorisasi yang sama
        if (
            !Auth::user()->can('leave_confirm_reject') &&
            ($leaveRequest->employee_id !== Auth::user()->employee_id || $leaveRequest->status !== 'pending')
        ) {
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

    public function destroy(LeaveRequest $leaveRequest)
    {
        if (
            !Auth::user()->can('leave_confirm_reject') &&
            ($leaveRequest->employee_id !== Auth::user()->employee_id || $leaveRequest->status !== 'pending')
        ) {
            abort(403, 'Unauthorized action. You can only delete your own pending requests.');
        }

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted successfully.');
    }
}
