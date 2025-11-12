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
                <h3>Positions</h3>
                <p class="text-subtitle text-muted">Handle data position</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.html">Position</a></li>
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
                    <a href="{{ route('positions.create')}}" class="btn btn-primary mb-3 ms-auto">New Position</a>
                </div>

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $position)
                        <tr>
                            <td>{{ $position->title}}</td>
                            <td>{{ $position->description }}</td>
                            <td>
                                <a href="{{ route('positions.edit', $position->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('positions.destroy', $position->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this position?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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