<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;
use Carbon\CarbonPeriod;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $query = LeaveRequest::with('employee');
        if (!Auth::user()->can('leave_manage')) {
            $query->where('employee_id', Auth::user()->employee_id);
        }
        
        $leaveRequests = $query->orderBy('start_date', 'desc')->get();
        return view('leave-requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        return view('leave-requests.create');
    }

    public function store(Request $request)
    {
        $employeeId = Auth::user()->employee_id;

        if (Auth::user()->can('leave_manage') && $request->filled('employee_id')) {
            $employeeId = $request->employee_id;
        }

        $request->validate([
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        LeaveRequest::create([
            'employee_id' => $employeeId,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
            'attachment' => $attachmentPath
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Pengajuan cuti berhasil dibuat.');
    }

    public function edit($id)
    {
        try {
            $decryptedId = decrypt($id);
            $leaveRequest = LeaveRequest::findOrFail($decryptedId);
            
            if ($leaveRequest->employee_id != Auth::user()->employee_id && !Auth::user()->can('leave_manage')) {
                abort(403);
            }

            $employees = Employee::where('status', 'active')->orderBy('fullname')->get();

            return view('leave-requests.edit', compact('leaveRequest', 'employees'));

        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $decryptedId = decrypt($id);
            $leaveRequest = LeaveRequest::findOrFail($decryptedId);

            $leaveRequest->update($request->only(['leave_type', 'start_date', 'end_date', 'reason']));

            return redirect()->route('leave-requests.index')->with('success', 'Pengajuan cuti diperbarui.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = decrypt($id); 
            $leaveRequest = LeaveRequest::findOrFail($decryptedId);
            
            if ($leaveRequest->attachment) {
                Storage::disk('public')->delete($leaveRequest->attachment);
            }

            $leaveRequest->delete();
            return redirect()->route('leave-requests.index')->with('success', 'Pengajuan dibatalkan.');
        } catch (DecryptException $e) {
            abort(404);
        }
    }

    public function confirm($id) {
        $this->updateStatus($id, 'approved');
        return back()->with('success', 'Cuti disetujui & Absensi diperbarui.');
    }

    public function reject($id) {
        $this->updateStatus($id, 'rejected');
        return back()->with('success', 'Cuti ditolak.');
    }

    private function updateStatus($id, $status) {
        try {
            $decryptedId = decrypt($id);
            $leave = LeaveRequest::findOrFail($decryptedId);
            
            $leave->update(['status' => $status]);

            if ($status == 'approved') {
                $period = CarbonPeriod::create($leave->start_date, $leave->end_date);

                foreach ($period as $date) {
                    Presence::updateOrCreate(
                        [
                            'employee_id' => $leave->employee_id,
                            'date' => $date->format('Y-m-d'),
                        ],
                        [
                            'status' => 'leave',
                            'check_in' => null,
                            'check_out' => null,
                        ]
                    );
                }
            }

        } catch (DecryptException $e) {
            abort(404);
        }
    }
}