@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Daftar Presensi</h3>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Data Kehadiran</h5>
                <div>
                    @can('presence_view_all')
                    <a href="{{ route('export.presences') }}" class="btn btn-success btn-sm"><i class="bi bi-file-excel"></i> Export</a>
                    @endcan
                    @can('presence_create')
                    <a href="{{ route('presences.create')}}" class="btn btn-primary btn-sm">Absen Baru</a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body">
            
            {{-- --- FILTER FORM --- --}}
            <form action="{{ route('presences.index') }}" method="GET" class="mb-4 p-3 bg-light rounded">
                <div class="row g-3">
                    @can('presence_view_all')
                    <div class="col-md-3">
                        <label class="form-label text-sm">Karyawan</label>
                        <select name="employee_id" class="form-select form-select-sm">
                            <option value="">Semua Karyawan</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endcan

                    <div class="col-md-3">
                        <label class="form-label text-sm">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir (Present)</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Alfa (Absent)</option>
                            <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>Cuti/Izin (Leave)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-sm">Tanggal</label>
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm me-2">Filter</button>
                        <a href="{{ route('presences.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </div>
            </form>
            {{-- --- END FILTER --- --}}

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-striped" id="table1">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Waktu</th> {{-- Digabung biar hemat tempat --}}
                        <th>Status</th>
                        <th>Bukti (Foto & Lokasi)</th> {{-- KOLOM BARU --}}
                        @can('presence_view_all') <th>Actions</th> @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($presences as $presence)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $presence->employee->fullname }}</div>
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($presence->date)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="small">
                                <div>In: <strong>{{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->format('H:i') : '-' }}</strong></div>
                                <div>Out: <strong>{{ $presence->check_out ? \Carbon\Carbon::parse($presence->check_out)->format('H:i') : '-' }}</strong></div>
                            </div>
                        </td>
                        <td>
                            @if($presence->status == 'present')
                                <span class="badge bg-success">Hadir</span>
                            @elseif($presence->status == 'leave')
                                <span class="badge bg-warning text-dark">Cuti/Izin</span>
                            @else
                                <span class="badge bg-danger">Alfa/Absen</span>
                            @endif
                        </td>
                        
                        {{-- KOLOM BUKTI --}}
                        <td>
                            <div class="d-flex gap-2">
                                {{-- 1. Foto Selfie --}}
                                @if($presence->photo)
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#photoModal{{ $presence->id }}">
                                        <i class="bi bi-camera"></i> Foto
                                    </button>

                                    <div class="modal fade" id="photoModal{{ $presence->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Foto Absensi: {{ $presence->employee->fullname }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ asset('storage/' . $presence->photo) }}" class="img-fluid rounded" alt="Foto Absen">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif

                                {{-- 2. Lokasi Maps --}}
                                @if($presence->latitude && $presence->longitude)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $presence->latitude }},{{ $presence->longitude }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-geo-alt"></i> Peta
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        @can('presence_view_all')
                        <td>
                            <a href="{{ route('presences.edit', $presence->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('presences.destroy', $presence->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('table1');
        if (table) {
            new simpleDatatables.DataTable(table, {
                searchable: false, 
                perPageSelect: [10, 25, 50],
            });
        }
    });
</script>
@endpush