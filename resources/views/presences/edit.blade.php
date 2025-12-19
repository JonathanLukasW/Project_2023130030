@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Edit Presence</h3>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit Data Absensi</h5>
        </div>
        <div class="card-body">

            <form action="{{ route('presences.update', encrypt($presence->id))}}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="" class="form-label">Employee</label>
                    <select name="employee_id" class="form-control" disabled>
                        {{-- Kita disable agar HR tidak salah ganti orang, tapi kita kirim hidden id --}}
                        <option value="{{ $presence->employee_id }}">{{ $presence->employee->fullname }}</option>
                    </select>
                    {{-- Input hidden agar validasi controller tetap lolos --}}
                    <input type="hidden" name="employee_id" value="{{ $presence->employee_id }}">
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" name="date" value="{{ old('date', $presence->date) }}" required>
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" onchange="toggleTimeInput()">
                        <option value="present" {{ $presence->status == 'present' ? 'selected' : '' }}>Hadir (Present)</option>
                        <option value="absent" {{ $presence->status == 'absent' ? 'selected' : '' }}>Alfa (Absent)</option>
                        <option value="leave" {{ $presence->status == 'leave' ? 'selected' : '' }}>Cuti/Izin (Leave)</option>
                    </select>
                </div>

                {{-- HAPUS 'required' DI SINI --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="" class="form-label">Check In</label>
                        {{-- Perhatikan format value untuk datetime-local: Y-m-d\TH:i:s --}}
                        <input type="datetime-local" id="check_in" class="form-control" name="check_in" 
                            value="{{ $presence->check_in ? \Carbon\Carbon::parse($presence->check_in)->format('Y-m-d\TH:i:s') : '' }}">
                        <small class="text-muted">Kosongkan jika Alfa/Cuti</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="" class="form-label">Check Out</label>
                        <input type="datetime-local" id="check_out" class="form-control" name="check_out" 
                            value="{{ $presence->check_out ? \Carbon\Carbon::parse($presence->check_out)->format('Y-m-d\TH:i:s') : '' }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Absensi</button>
                <a href="{{ route('presences.index') }}" class="btn btn-secondary">Kembali</a>

            </form>
        </div>
    </div>
</section>

{{-- Script Kecil: Jika pilih Absent/Leave, otomatis kosongkan jam --}}
<script>
    function toggleTimeInput() {
        const status = document.getElementById('status').value;
        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');

        if (status === 'absent' || status === 'leave') {
            checkIn.value = '';
            checkOut.value = '';
        } else {
        }
    }
</script>
@endsection