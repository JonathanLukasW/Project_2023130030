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
                    role
                </h5>
            </div>
            <div class="card-body">

                <div class="d-flex">
                    <a href="{{ route('presences.create')}}" class="btn btn-primary mb-3 ms-auto">New Presence</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Check In</th>
                            <th>Check out</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($presences as $presence)
                        <tr>
                            <td>{{ $presence->employee->fullname }}</td>
                            <td>{{ $presence->check_in}}</td>
                            <td>{{ $presence->check_out}}</td>
                            <td>{{ $presence->date }}</td>
                            <td>
                                @if($presence->status == 'present')
                                    <span class="badge bg-success">{{ ucfirst($presence->status) }}</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($presence->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('presences.edit', $presence->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('presences.destroy', $presence->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this presence?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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