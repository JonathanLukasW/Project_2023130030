@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Generate Payroll Otomatis</h3>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Pilih Periode Gaji</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('salaries.generate') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Bulan & Tahun</label>
                    <input type="month" name="month" class="form-control" required>
                    <small class="text-muted">Pilih bulan yang ingin dihitung gajinya.</small>
                </div>

                <div class="alert alert-light-primary color-primary">
                    <i class="bi bi-info-circle"></i> <strong>Informasi Perhitungan:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Gaji Pokok:</strong> Diambil dari setting Jabatan (Position).</li>
                        <li><strong>Potongan:</strong> Rp 100.000 per kehadiran "Absent" (Alfa).</li>
                        <li><strong>Bonus Tugas:</strong> Rp 50.000 jika tugas selesai <em>sebelum</em> deadline.</li>
                        <li><strong>Denda Tugas:</strong> Rp 50.000 jika tugas selesai <em>setelah</em> deadline.</li>
                    </ul>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('salaries.index') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">Generate Gaji</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection