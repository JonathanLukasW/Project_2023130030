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
    
    {{-- BARIS CARD RINGKASAN --}}
    <div class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon purple mb-2">
                                <i class="icon dripicons dripicons-tag"></i>
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
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon blue mb-2">
                                <i class="icon dripicons dripicons-user"></i>
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
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon green mb-2">
                                <i class="icon dripicons dripicons-alarm"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Total Presences</h6>
                            <h6 class="font-extrabold mb-0">{{ $presence }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon red mb-2">
                                <i class="icon dripicons dripicons-to-do"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Salaries Recorded</h6>
                            <h6 class="font-extrabold mb-0">{{ $salary }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KALENDER TUGAS (EVENT) - HR Manager (Semua Tugas) --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Kalender Semua Tugas Perusahaan</h4>
                    <p class="text-subtitle text-muted">Melihat jadwal dan *deadline* semua karyawan.</p>
                </div>
                <div class="card-body">
                    <div id="calendar-container-hr"></div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- CHART KEHADIRAN - HR Manager (Semua Karyawan) --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Chart Kehadiran Seluruh Karyawan</h4>
                    <p class="text-subtitle text-muted">Total Kehadiran Bulanan (2025).</p>
                </div>
                <div class="card-body">
                    <canvas id="presence-hr"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    {{-- TABEL RINGKASAN TUGAS --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Latest Tasks Summary</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-lg">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Detail</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                <tr>
                                    <td class="col-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="https://ui-avatars.com/api/?name={{ $task->employee->fullname }}&background=random">
                                            </div>
                                            <p class="font-bold ms-3 mb-0">{{ $task->employee->fullname }}</p>
                                        </div>
                                    </td>
                                    <td class="col-auto">
                                        <p class=" mb-0">{{ $task->title }}</p>
                                    </td>
                                    <td class="col-auto">
                                        <p class=" mb-0">{{ ucfirst($task->status) }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Script untuk Kalender Tugas dan Chart Kehadiran --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- LOGIC KALENDER TUGAS (HR Manager) ---
        const calendarContainerHR = document.getElementById('calendar-container-hr');
        if (calendarContainerHR) {
            // Ambil SEMUA data events (tanpa filter personal)
            fetch('{{ route('tasks.events') }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal memuat data tugas.');
                    }
                    return response.json();
                })
                .then(events => {
                    // Inisialisasi Kalender
                    new BSCalendar(calendarContainerHR, {
                        events: events,
                        startMonth: new Date().getMonth(),
                        startYear: new Date().getFullYear(),
                        weekStart: 1, 
                        view: 'month'
                    });
                })
                .catch(error => {
                    console.error('Error in HR calendar initialization:', error);
                    calendarContainerHR.innerHTML = `<div class="alert alert-danger">Gagal memuat kalender: ${error.message}</div>`;
                });
        }
        
        // --- LOGIC CHART KEHADIRAN (HR Manager) ---
        if (document.getElementById('presence-hr')) {
            var ctxBar = document.getElementById('presence-hr').getContext('2d');
            var myBar = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    datasets: [{
                        label: 'Total Kehadiran',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Total Kehadiran Bulanan (2025)'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 30 // Maksimal jumlah hari kerja/hadir
                        }
                    }
                }
            });

            function updateData() {
                // Memanggil AJAX endpoint tanpa filter (semua karyawan)
                fetch('{{ url("/dashboard/presence") }}') 
                    .then(response => response.json())
                    .then((output) => {
                        if (output && Array.isArray(output) && output.length === 12) {
                            myBar.data.datasets[0].data = output;
                            myBar.update();
                        } else {
                            console.error("Data kehadiran yang diterima dari server tidak valid:", output);
                            myBar.data.datasets[0].data = Array(12).fill(0);
                            myBar.update();
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                    });
            }
            updateData();
        }
    });
</script>
@endpush
@endsection