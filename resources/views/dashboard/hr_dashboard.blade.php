@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Dashboard HR Manager</h3>
</div>

<div class="page-content">
    
    {{-- BARIS 1: KARTU RINGKASAN --}}
    <div class="row">
        {{-- Employee --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon blue mb-2"><i class="bi bi-people-fill"></i></div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Employees</h6>
                            <h6 class="font-extrabold mb-0">{{ $employee }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Department --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon purple mb-2"><i class="bi bi-building"></i></div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Departments</h6>
                            <h6 class="font-extrabold mb-0">{{ $department }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Hadir Hari Ini --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon green mb-2"><i class="bi bi-check-circle-fill"></i></div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Hadir Hari Ini</h6>
                            @php $todayP = \App\Models\Presence::whereDate('date', now())->where('status', 'present')->count(); @endphp
                            <h6 class="font-extrabold mb-0">{{ $todayP }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Cuti Hari Ini --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon red mb-2"><i class="bi bi-person-x-fill"></i></div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Cuti Hari Ini</h6>
                            @php $todayL = \App\Models\Presence::whereDate('date', now())->where('status', 'leave')->count(); @endphp
                            <h6 class="font-extrabold mb-0">{{ $todayL }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 2: CHART PRESENSI --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Statistik Presensi</h4>
                </div>
                <div class="card-body">
                    {{-- Filter Chart --}}
                    <div class="row mb-4 g-2">
                        <div class="col-md-4">
                            <select id="filter-employee" class="form-select" onchange="updateChart()">
                                <option value="all">Semua Karyawan</option>
                                @foreach(\App\Models\Employee::all() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="filter-month" class="form-select" onchange="updateChart()">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="filter-year" class="form-select" onchange="updateChart()">
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                <option value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div style="height: 300px; position: relative;">
                                <canvas id="presence-chart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-circle-fill text-primary me-2"></i> Hadir (Present)</span>
                                    <span class="badge bg-primary rounded-pill fs-6" id="count-present">0</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-circle-fill text-danger me-2"></i> Alfa (Absent)</span>
                                    <span class="badge bg-danger rounded-pill fs-6" id="count-absent">0</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-circle-fill text-warning me-2"></i> Cuti (Leave)</span>
                                    <span class="badge bg-warning text-dark rounded-pill fs-6" id="count-leave">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 3: KALENDER TUGAS --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="mb-0">Kalender Tugas</h4>
                </div>
                <div class="card-body">
                    {{-- Filter Kalender --}}
                    <div class="row mb-3 g-2">
                        <div class="col-md-4">
                            <label class="form-label">Filter Karyawan</label>
                            <select id="cal-employee" class="form-select">
                                <option value="all">Semua Karyawan</option>
                                @foreach(\App\Models\Employee::all() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bulan</label>
                            <select id="cal-month" class="form-select">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ date('n') == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun</label>
                            <select id="cal-year" class="form-select">
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                <option value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                                <option value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                            </select>
                        </div>
                    </div>

                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- MODAL DETAIL TASK (POP-UP) --}}
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Task Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Karyawan</th>
                        <td id="modalEmployee">: -</td>
                    </tr>
                    <tr>
                        <th>Deadline</th>
                        <td id="modalDate">: -</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span id="modalStatus" class="badge bg-secondary">-</span></td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td><p id="modalDesc" class="text-muted mb-0">-</p></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                {{-- TOMBOL BUKA HALAMAN PENUH DIHAPUS --}}
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- SCRIPT CHART --}}
<script>
    let presenceChart = null;

    function initChart() {
        const ctx = document.getElementById('presence-chart').getContext('2d');
        presenceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Alfa', 'Cuti'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ['#435ebe', '#dc3545', '#ffc107'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
        updateChart(); 
    }

    function updateChart() {
        const empId = document.getElementById('filter-employee').value;
        const month = document.getElementById('filter-month').value;
        const year = document.getElementById('filter-year').value;

        fetch(`{{ url('/dashboard/presence') }}?employee_id=${empId}&month=${month}&year=${year}`)
            .then(res => res.json())
            .then(data => {
                presenceChart.data.datasets[0].data = data.data;
                presenceChart.update();
                document.getElementById('count-present').innerText = data.data[0];
                document.getElementById('count-absent').innerText = data.data[1];
                document.getElementById('count-leave').innerText = data.data[2];
            });
    }

    document.addEventListener('DOMContentLoaded', initChart);
</script>

{{-- SCRIPT CALENDAR --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var modal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            headerToolbar: false, 
            events: {
                url: '{{ route("tasks.events") }}',
                extraParams: function() { 
                    return { employee_id: document.getElementById('cal-employee').value };
                }
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();  
                var props = info.event.extendedProps;
                document.getElementById('modalTitle').innerText = info.event.title;
                document.getElementById('modalEmployee').innerText = ': ' + props.employee;
                document.getElementById('modalDate').innerText = ': ' + props.due_date_fmt;
                document.getElementById('modalDesc').innerText = props.description;
                var statusBadge = document.getElementById('modalStatus');
                statusBadge.innerText = props.status;
                statusBadge.className = 'badge ' + (props.status === 'Completed' ? 'bg-success' : 'bg-warning');

                modal.show();
            }
        });
        
        calendar.render();

        document.getElementById('cal-employee').addEventListener('change', function() {
            calendar.refetchEvents();
        });

        function jumpDate() {
            var m = document.getElementById('cal-month').value;
            var y = document.getElementById('cal-year').value;
            calendar.gotoDate(y + '-' + m + '-01');
        }

        document.getElementById('cal-month').addEventListener('change', jumpDate);
        document.getElementById('cal-year').addEventListener('change', jumpDate);
    });
</script>
@endpush
@endsection