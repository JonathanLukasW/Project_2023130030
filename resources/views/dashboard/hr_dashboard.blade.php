@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Dashboard Overview (HR Manager)</h3>
</div>

<div class="page-content">
    
    {{-- BARIS 1: STATISTIK UTAMA --}}
    <div class="row">
        {{-- Total Employees --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon blue mb-2">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Employees</h6>
                            <h6 class="font-extrabold mb-0">{{ $employee }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Departments --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon purple mb-2">
                                <i class="bi bi-building"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Departments</h6>
                            <h6 class="font-extrabold mb-0">{{ $department }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kehadiran Hari Ini (Real-time) --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon green mb-2">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Hadir Hari Ini</h6>
                            {{-- Hitung manual query di Blade (Bisa dipindah ke controller nanti) --}}
                            @php
                                $todayPresence = \App\Models\Presence::whereDate('date', now())->where('status', 'present')->count();
                            @endphp
                            <h6 class="font-extrabold mb-0">{{ $todayPresence }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Izin/Cuti Hari Ini --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon red mb-2">
                                <i class="bi bi-person-x-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Cuti/Izin Hari Ini</h6>
                            @php
                                $todayLeave = \App\Models\Presence::whereDate('date', now())->where('status', 'leave')->count();
                            @endphp
                            <h6 class="font-extrabold mb-0">{{ $todayLeave }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 2: PENGGANTI CHART (TABEL RINGKASAN KEHADIRAN BULAN INI) --}}
    {{-- Ini jauh lebih ringan daripada Chart.js --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Statistik Kehadiran (Bulan Ini)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Persentase (Estimasi)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentMonth = now()->month;
                                    $totalRec = \App\Models\Presence::whereMonth('date', $currentMonth)->count();
                                    
                                    $p_present = \App\Models\Presence::whereMonth('date', $currentMonth)->where('status', 'present')->count();
                                    $p_absent = \App\Models\Presence::whereMonth('date', $currentMonth)->where('status', 'absent')->count();
                                    $p_leave = \App\Models\Presence::whereMonth('date', $currentMonth)->where('status', 'leave')->count();
                                    
                                    $perc_present = $totalRec > 0 ? round(($p_present / $totalRec) * 100, 1) : 0;
                                    $perc_absent = $totalRec > 0 ? round(($p_absent / $totalRec) * 100, 1) : 0;
                                    $perc_leave = $totalRec > 0 ? round(($p_leave / $totalRec) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td><span class="badge bg-success">Hadir (Present)</span></td>
                                    <td>{{ $p_present }}</td>
                                    <td>
                                        <div class="progress progress-primary mb-0">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $perc_present }}%" aria-valuenow="{{ $perc_present }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small>{{ $perc_present }}%</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Alfa (Absent)</span></td>
                                    <td>{{ $p_absent }}</td>
                                    <td>
                                        <div class="progress progress-danger mb-0">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $perc_absent }}%" aria-valuenow="{{ $perc_absent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small>{{ $perc_absent }}%</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning text-dark">Cuti (Leave)</span></td>
                                    <td>{{ $p_leave }}</td>
                                    <td>
                                        <div class="progress progress-warning mb-0">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $perc_leave }}%" aria-valuenow="{{ $perc_leave }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small>{{ $perc_leave }}%</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 3: Kalender (Tetap dipertahankan karena fitur utama) --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Kalender Tugas Perusahaan</h4>
                </div>
                <div class="card-body">
                    <div id="calendar-container-hr"></div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarContainerHR = document.getElementById('calendar-container-hr');
        if (calendarContainerHR) {
            fetch('{{ route('tasks.events') }}')
                .then(response => response.json())
                .then(events => {
                    new BSCalendar(calendarContainerHR, {
                        events: events,
                        startMonth: new Date().getMonth(),
                        startYear: new Date().getFullYear(),
                        weekStart: 1, 
                        view: 'month'
                    });
                })
                .catch(error => console.error('Error calendar:', error));
        }
    });
</script>
@endpush
@endsection