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
                <h3>Ajukan Cuti / Izin</h3>
                <p class="text-subtitle text-muted">Form pengajuan cuti karyawan.</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Cuti</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Jika Admin/HR yang inputkan untuk karyawan lain --}}
                    @can('leave_manage')
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Pilih Karyawan (Mode Admin)</label>
                        <select name="employee_id" class="form-select">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach(\App\Models\Employee::all() as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->fullname }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Biarkan kosong jika ini pengajuan untuk diri sendiri.</small>
                    </div>
                    @endcan

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="leave_type" class="form-label">Jenis Cuti / Izin</label>
                            <select name="leave_type" class="form-select @error('leave_type') is-invalid @enderror">
                                <option value="Sick">Sakit (Sick)</option>
                                <option value="Vacation">Liburan (Vacation)</option>
                                <option value="Personal">Izin Pribadi (Personal)</option>
                                <option value="Maternity">Melahirkan (Maternity)</option>
                                <option value="Other">Lainnya</option>
                            </select>
                            @error('leave_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="attachment" class="form-label">Lampiran (Surat Dokter/dll)</label>
                            <input type="file" name="attachment" class="form-control">
                            <small class="text-muted">Format: PDF, JPG, PNG (Max 2MB). Opsional.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan Cuti</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Jelaskan alasan pengajuan cuti..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('leave-requests.index') }}" class="btn btn-light-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection