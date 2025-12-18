@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Leave Requests</h3>
                <p class="text-subtitle text-muted">Daftar pengajuan cuti dan izin karyawan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Leave Requests</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Data Pengajuan</h5>
            </div>
            <div class="card-body">

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('leave-requests.create')}}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Ajukan Cuti Baru
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Tipe Cuti</th>
                                <th>Tanggal</th>
                                <th>Durasi</th>
                                <th>Lampiran</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRequests as $leaveRequest)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <img src="https://ui-avatars.com/api/?name={{ $leaveRequest->employee->fullname }}&background=random" alt="Avatar">
                                        </div>
                                        <span>{{ $leaveRequest->employee->fullname }}</span>
                                    </div>
                                </td>
                                <td>{{ $leaveRequest->leave_type }}</td>
                                <td>
                                    <small>
                                        {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M') }} - 
                                        {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1 }} Hari
                                </td>
                                <td>
                                    @if($leaveRequest->attachment)
                                        <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" target="_blank" class="btn btn-sm btn-light-secondary">
                                            <i class="bi bi-paperclip"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted text-sm">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($leaveRequest->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($leaveRequest->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- TOMBOL AKSI UNTUK HR MANAGER (APPROVE/REJECT) --}}
                                    @can('leave_confirm_reject')
                                        @if($leaveRequest->status == 'pending')
                                            <a href="{{ route('leave-requests.confirm', $leaveRequest->id) }}" class="btn btn-success btn-sm" title="Approve" onclick="return confirm('Setujui pengajuan ini?')">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="{{ route('leave-requests.reject', $leaveRequest->id) }}" class="btn btn-danger btn-sm" title="Reject" onclick="return confirm('Tolak pengajuan ini?')">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    {{-- TOMBOL AKSI UNTUK PEMILIK (EDIT/DELETE) --}}
                                    {{-- Hanya bisa edit/hapus jika status masih pending --}}
                                    @if($leaveRequest->status == 'pending')
                                        @if(Auth::user()->employee_id == $leaveRequest->employee_id || Auth::user()->can('leave_confirm_reject'))
                                            <a href="{{ route('leave-requests.edit', $leaveRequest->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('leave-requests.destroy', $leaveRequest->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pengajuan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-secondary btn-sm" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('table1');
        if (table) {
            new simpleDatatables.DataTable(table, {
                searchable: true,
                perPageSelect: [5, 10, 20, 50],
            });
        }
    });
</script>
@endpush