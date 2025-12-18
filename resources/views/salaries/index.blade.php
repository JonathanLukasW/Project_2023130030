@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Salaries (Payroll)</h3>
</div>
<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Data Penggajian</h5>
        </div>
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                {{-- Tombol Generate Otomatis (Hanya HR) --}}
                @can('salary_view_all')
                <a href="{{ route('salaries.generate_form') }}" class="btn btn-success">
                    <i class="bi bi-calculator"></i> Hitung Gaji Otomatis
                </a>
                @endcan
                
                {{-- Tombol Manual SUDAH DIHAPUS --}}
            </div>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-striped" id="table1">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Gaji Pokok</th>
                        <th>Potongan</th>
                        <th>Bonus</th>
                        <th>Total (Net)</th>
                        <th>Periode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                    <tr>
                        <td>{{ $salary->employee->fullname }}</td>
                        <td>Rp {{ number_format($salary->salary, 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format($salary->deductions, 0, ',', '.') }}</td>
                        <td class="text-success">Rp {{ number_format($salary->bonuses, 0, ',', '.') }}</td>
                        <td class="fw-bold">Rp {{ number_format($salary->net_salary, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($salary->pay_date)->format('F Y') }}</td>
                        <td>
                            <a href="{{ route('salaries.show', $salary->id) }}" class="btn btn-info btn-sm" title="Lihat Slip">
                                <i class="bi bi-receipt"></i> Slip
                            </a>

                            @can('salary_view_all')
                                {{-- Tombol Edit DIHAPUS --}}
                                
                                <form action="{{ route('salaries.destroy', $salary->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data gaji ini? Anda bisa meng-generate ulang nanti.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>
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
                searchable: true,
                perPageSelect: [5, 10, 20, 50],
            });
        }
    });
</script>
@endpush