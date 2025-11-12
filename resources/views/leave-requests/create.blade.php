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
                <h3>Presences</h3>
                <p class="text-subtitle text-muted">Handle data presence</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.html">presence</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Create
                </h5>
            </div>
            <div class="card-body">

                {{-- --- PERUBAHAN: Ganti 'session' ke '@can' --- --}}
                {{-- Cek pakai Izin (Permission) Spatie, bukan Session! --}}
                {{-- (Izin 'presence_view_all' hanya dimiliki HR/Admin di Seeder & Controller) --}}
                @can('presence_view_all')

                {{-- INI FORM UNTUK ADMIN (INPUT MANUAL) --}}
                <form action="{{ route('presences.store')}}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="" class="form-label">Employee</label>
                        <select name="employee_id" class="form-control @error('employee_id') is-invalid @enderror">
                            <option value="">Select an Employee</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->fullname }}</option>
                            @endforeach
                        </select>
                        @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Check In</label>
                        <input type="text" class="form-control datetime" name="check_in" required>
                        @error('check_in')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Check Out</label>
                        <input type="text" class="form-control datetime" name="check_out" required>
                        @error('check_out')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Date</label>
                        <input type="text" class="form-control date" name="date" required>
                        @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="leave">Leave</option> {{-- Ganti dari 'sick' --}}
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('presences.index') }}" class="btn btn-secondary">Back</a>

                </form>

                @else

                {{-- INI FORM UNTUK KARYAWAN BIASA (PAKAI GPS) --}}
                <form action="{{ route('presences.store') }}" method="POST">
                    @csrf

                    <div class="mb-3"><b>Note</b> : Mohon izinkan akses lokasi, supaya presensi diterima</div>

                    <div class="mb-3">
                        <label for="" class="form-label">Latitude</label>
                        <input type="text" class="form-control " name="latitude" id="latitude" required readonly>
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Longitude</label>
                        <input type="text" class="form-control " name="longitude" id="longitude" required readonly>
                    </div>

                    <div class="mb-3">
                        <iframe width="500" height="300" src="" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btn-present" disabled>Presence</button>
                </form>
                @endcan
                {{-- --- AKHIR PERUBAHAN --- --}}
            </div>
        </div>

    </section>
</div>

<script>
    if (document.getElementById('latitude')) {
        const iframe = document.querySelector('iframe');
        const officeLat = -6.895505;
        const officeLong = 107.613252;
        const threshold = 0.1; 

        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const long = position.coords.longitude;
            iframe.src = `https://maps.google.com/maps?q=${lat},${long}&output=embed`;
        });

        document.addEventListener('DOMContentLoaded', (event) => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const long = position.coords.longitude;

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = long;

                    const distance = Math.sqrt(Math.pow(lat - officeLat, 2) + Math.pow(long - officeLong, 2));

                    if (distance <= threshold) {
                        console.log('Kamu berada di Kantor, ayo bekerja');
                        document.getElementById('btn-present').removeAttribute('disabled');
                    } else {
                        console.error('Kamu tidak berada di kantor, tolong absen di kantor');
                    }
                });
            } else {
                console.log('Geolocation is not supported by this browser.');
            }
        });
    }
</script>
@endsection