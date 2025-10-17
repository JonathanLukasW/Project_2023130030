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
                <h3>Salaries</h3>
                <p class="text-subtitle text-muted">Manage Salaries</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.html">salary</a></li>
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
                    salary
                </h5>
            </div>
            <div class="card-body">

                <div class="d-flex">
                    @if(session('role') == 'HR')
                        <a href="{{ route('salaries.create')}}" class="btn btn-primary mb-3 ms-auto">New Salary</a>
                    @endif
                </div>

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Salary</th>
                            <th>Deductions</th>
                            <th>Bonuses</th>
                            <th>Net Salary</th>
                            <th>Pay Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaries as $salary)
                        <tr>
                            <td>{{ $salary->employee->fullname }}</td>
                            <td>{{ number_format($salary->salary) }}</td>
                            <td>{{ number_format($salary->deductions) }}</td>
                            <td>{{ number_format($salary->bonuses) }}</td>
                            <td>{{ number_format($salary->net_salary) }}</td>
                            <td>{{ $salary->pay_date}}</td>
                            <td>
                                <a href="{{ route('salaries.show', $salary->id) }}" class="btn btn-info btn-sm">Salary Slip</a>

                                @if(session('role') == 'HR')
                                    <a href="{{ route('salaries.edit', $salary->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('salaries.destroy', $salary->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this salary?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                @endif
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