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
                <h3>Tasks Management</h3>
                <p class="text-subtitle text-muted">Daftar tugas yang harus diselesaikan dan yang sudah selesai.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tasks</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Task List</h5>
            </div>
            <div class="card-body">

                <div class="d-flex">
                    @can('task_create')
                    <a href="{{ route('tasks.create')}}" class="btn btn-primary mb-3 ms-auto">New Task</a>
                    @endcan
                </div>

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped" id="table1"> 
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->employee->fullname ?? 'N/A' }}</td>
                                <td>{{ $task->due_date }}</td>
                                <td>
                                    @if($task->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($task->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                    @else
                                    <span class="badge bg-info">{{ ucfirst($task->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info btn-sm">View</a>

                                    @can('task_mark_status')
                                        @if($task->status == 'pending')
                                        <a href="{{ route('tasks.done', $task->id) }}" class="btn btn-success btn-sm">Mark as Done</a>
                                        @else
                                        <a href="{{ route('tasks.pending', $task->id) }}" class="btn btn-warning btn-sm">Mark as Pending</a>
                                        @endif
                                    @endcan

                                    @can('task_edit')
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    @endcan
                                    
                                    @can('task_delete')
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>
</div>

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
@endsection