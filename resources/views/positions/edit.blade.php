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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('positions.index') }}">Position</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Edit Position
                </h5>
            </div>
            <div class="card-body">

                {{-- PERBAIKAN SYNTAX DI SINI (Pastikan kurung tutup lengkap) --}}
                <form action="{{ route('positions.update', encrypt($position->id)) }}" method="POST">
                    @csrf 
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title', $position->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TAMBAHAN: INPUT GAJI POKOK (WAJIB ADA) --}}
                    <div class="mb-3">
                        <label for="base_salary" class="form-label">Gaji Pokok (Base Salary)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="base_salary" class="form-control" value="{{ old('base_salary', $position->base_salary) }}" required>
                        </div>
                        @error('base_salary')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $position->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Position</button>
                    <a href="{{ route('positions.index') }}" class="btn btn-secondary">Back</a>

                </form>
            </div>
        </div>

    </section>
</div>
@endsection