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
                        <li class="breadcrumb-item active" aria-current="page">New</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">New Position</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('positions.store')}}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="" class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                {{-- INPUT GAJI POKOK --}}
                <div class="mb-3">
                    <label for="base_salary" class="form-label">Gaji Pokok (Base Salary)</label>
                    <input type="number" class="form-control" name="base_salary" required placeholder="Contoh: 5000000">
                </div>

                <button type="submit" class="btn btn-primary">Create Position</button>
                <a href="{{ route('positions.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</section>
</div>
@endsection