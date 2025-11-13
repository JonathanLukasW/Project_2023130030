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

                {{-- Form ini akan mengirim data ke RolePermissionController@update --}}
                <form action="{{ route('permissions.update') }}" method="POST">
                    @csrf
                    {{-- Kita pakai POST, bukan PUT/PATCH, sesuai web.php --}}

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 20%;">Role (Peran)</th>
                                    
                                    {{-- LOOPING 1: Buat judul kolom dari semua Izin (Permission) --}}
                                    @foreach ($permissions as $permission)
                                        <th class="text-center" style="width: 10%; transform: rotate(-45deg); white-space: nowrap;">
                                            {{-- Ubah 'task_edit' jadi 'Task Edit' biar rapi --}}
                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- LOOPING 2: Buat baris dari semua Role (Peran) --}}
                                {{-- (Ingat: 'HR Manager' tidak kita tampilkan, sesuai logic di Controller) --}}
                                @foreach ($roles as $role)
                                <tr>
                                    <td><strong>{{ $role->name }}</strong></td>
                                    
                                    {{-- LOOPING 3: Buat checkbox di setiap sel --}}
                                    @foreach ($permissions as $permission)
                                    <td class="text-center">
                                        <div class="form-check form-check-inline d-flex justify-content-center">
                                            <input 
                                                class="form-check-input" 
                                                type="checkbox"
                                                
                                                {{-- Ini penting: 'name' harus array --}}
                                                {{-- Format: permissions[ROLE_ID][] = PERMISSION_NAME --}}
                                                name="permissions[{{ $role->id }}][]"
                                                value="{{ $permission->name }}"
                                                id="check-{{ $role->id }}-{{ $permission->id }}"
                                                
                                                {{-- Ini logic untuk mencentang otomatis --}}
                                                {{-- Cek: "Apakah nama izin ini ada di dalam array izin yg dimiliki role?" --}}
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