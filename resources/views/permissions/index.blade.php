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
                <h3>Manage Permissions</h3>
                <p class="text-subtitle text-muted">Atur izin (permission) untuk setiap peran (role).</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Permissions</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Permission Matrix</h5>
            </div>
            <div class="card-body">

                {{-- Notifikasi Sukses atau Error --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('permissions.update') }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    {{-- Kolom Pojok Kiri Atas --}}
                                    <th style="width: 25%;">Permission (Izin)</th>
                                    
                                    {{-- LOOPING 1 (BARU): Buat judul kolom dari semua ROLE (Peran) --}}
                                    @foreach ($roles as $role)
                                        <th class="text-center" style="width: 15%;">{{ $role->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- LOOPING 2 (BARU): Buat baris dari semua PERMISSION (Izin) --}}
                                @foreach ($permissions as $permission)
                                
                                    {{-- PERBAIKAN: SKIP izin yang tidak perlu ditampilkan di Matriks --}}
                                    {{-- Kita hapus Izin 'task_view_all' karena sudah diganti 'task_manage' --}}
                                    @if ($permission->name == 'task_view_all')
                                        @continue
                                    @endif

                                    {{-- PERBAIKAN: Ganti 'task_view' jadi 'Task View (Self)' --}}
                                    @if ($permission->name == 'task_view')
                                        @php $displayName = 'Task View (Self)'; @endphp
                                    @else
                                        @php $displayName = ucwords(str_replace('_', ' ', $permission->name)); @endphp
                                    @endif
                                    
                                <tr>
                                    {{-- Kolom Nama Izin --}}
                                    <td>
                                        <strong>{{ $displayName }}</strong>
                                    </td>
                                    
                                    {{-- LOOPING 3 (BARU): Buat checkbox untuk setiap Role --}}
                                    @foreach ($roles as $role)
                                    <td class="text-center">
                                        <div class="form-check form-check-inline d-flex justify-content-center">
                                            <input 
                                                class="form-check-input" 
                                                type="checkbox"
                                                
                                                name="permissions[{{ $role->id }}][]"
                                                value="{{ $permission->name }}"
                                                id="check-{{ $role->id }}-{{ $permission->id }}"
                                                
                                                @if(in_array($permission->name, $rolePermissions[$role->id]))
                                                    checked
                                                @endif
                                            >
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12 d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">Update Permissions</button>
                    </div>

                </form>
            </div>
        </div>
    </section>
</div>
@endsection