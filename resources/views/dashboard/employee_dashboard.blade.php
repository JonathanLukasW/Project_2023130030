@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Dashboard Karyawan</h3>
</div>

<div class="page-content">
    
    {{-- RINGKASAN SEDERHANA (Hanya untuk Karyawan) --}}
    <div class="row">
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
                            <h6 class="text-muted font-semibold">My Role</h6>
                            {{-- $user_role dikirim dari DashboardController --}}
                            <h6 class="font-extrabold mb-0">{{ $user_role }}</h6> 
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
                            <h6 class="text-muted font-semibold">Tugas Pending</h6>
                            {{-- $tasks_pending dikirim dari DashboardController --}}
                            <h6 class="font-extrabold mb-0">{{ $tasks_pending }}</h6> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KALENDER TUGAS (EVENT) - Hanya Tugas Milik Sendiri --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Jadwal Tugas Saya</h4>
                    <p class="text-subtitle text-muted">Kalender Tugas hanya menampilkan tugas yang ditugaskan kepada Anda.</p>
                </div>
                <div class="card-body">
                    <div id="calendar-container-employee"></div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- CHART KEHADIRAN - Hanya Kehadiran Milik Sendiri --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Chart Kehadiran Pribadi</h4>
                    <p class="text-subtitle text-muted">Total Kehadiran Bulanan Anda (2025).</p>
                </div>
                <div class="card-body">
                    <canvas id="presence-employee"></canvas>
                </div>
            </div>
        </div>
    </div>
    
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- LOGIC KALENDER TUGAS (Personal) ---
        const calendarContainer = document.getElementById('calendar-container-employee');
        if (calendarContainer) {
            // Memanggil endpoint tasks.events dengan flag 'personal=true'
            fetch('{{ route('tasks.events') }}?personal=true') 
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal memuat data tugas.');
                    }
                    return response.json();
                })
                .then(events => {
                    // Inisialisasi Kalender
                    new BSCalendar(calendarContainer, {
                        events: events,
                        startMonth: new Date().getMonth(), 
                        startYear: new Date().getFullYear(),
                        weekStart: 1, 
                        view: 'month'
                    });
                })
                .catch(error => {
                    console.error('Error in calendar initialization:', error);
                    calendarContainer.innerHTML = `<div class="alert alert-danger">Gagal memuat kalender: ${error.message}</div>`;
                });
        }
        
        // --- LOGIC CHART KEHADIRAN (Personal) ---
        if (document.getElementById('presence-employee')) {
            var ctxBar = document.getElementById('presence-employee').getContext('2d');
            var myBar = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    datasets: [{
                        label: 'Kehadiran Saya',
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
                            text: 'Total Kehadiran Bulanan Anda (2025)'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 30
                        }
                    }
                }
            });

            function updateData() {
                // Memanggil AJAX endpoint dengan flag 'employee=true'
                fetch('{{ url("/dashboard/presence") }}?employee=true') 
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