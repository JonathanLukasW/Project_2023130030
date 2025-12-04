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
                <h3>Employees</h3>
                <p class="text-subtitle text-muted">Handle employee</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.html">Employee</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Employees
                </h5>
            </div>
            <div class="card-body">

                {{-- PERUBAHAN: TAMBAH TOMBOL EXPORT --}}
                <div class="d-flex justify-content-between">
                    {{-- TOMBOL EXPORT BARU --}}
                    @can('employee_manage')
                    <a href="{{ route('export.employees') }}" class="btn btn-success mb-3 me-2">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                    @endcan
                    {{-- TOMBOL CREATE LAMA --}}
                    @can('employee_manage')
                    <a href="{{ route('employees.create')}}" class="btn btn-primary mb-3">New employee</a>
                    @endcan
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>department_id</th>
                            <th>Position</th>
                            <th>status</th>
                            <th>salary</th>
                            <th>option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td>{{ $employee->fullname }}</td>
                            <td>{{ $employee->email}}</td>
                            <td>{{ $employee->department->name }}</td>
                            <td>{{ $employee->position->title}}</td>
                            <td>
                                @if($employee->status == 'active')
                                    <span class="text-success">{{ ucfirst($employee->status) }}</span>
                                @else
                                    <span class="text-danger">{{ ucfirst($employee->status) }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($employee->salary) }}</td>

                            <td>

                                <a href="{{ route('employees.show', $employee->id) }}" target="_blank" class="btn btn-info btn-sm" rel="noopener noreferrer">view</a>
                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning btn-sm">edit</a>
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Sure?')">Delete</button>
                                </form>
                            </td>
                            
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</div>
@endsection