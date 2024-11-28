@extends('nurse.layout')
@section('title', 'Tasks')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="text-gradient mb-0">Daily Tasks</h4>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="filterTasks('all')">All</button>
                    <button class="btn btn-outline-primary" onclick="filterTasks('pending')">Pending</button>
                    <button class="btn btn-outline-primary" onclick="filterTasks('completed')">Completed</button>
                </div>
            </div>
        </div>
    </div>

    @forelse($tasks as $date => $dayTasks)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                            <span class="badge bg-primary ms-2">{{ $dayTasks->count() }} tasks</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">Status</th>
                                        <th>Task</th>
                                        <th>Patient</th>
                                        <th>Priority</th>
                                        <th>Due Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dayTasks as $task)
                                        <tr class="task-row {{ $task->status }}">
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input task-checkbox" 
                                                           data-task-id="{{ $task->id }}"
                                                           {{ $task->status === 'completed' ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $task->title }}</strong>
                                                @if($task->description)
                                                    <p class="small text-muted mb-0">{{ $task->description }}</p>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('nurse.patient.view', $task->patient) }}" 
                                                   class="text-decoration-none">
                                                    {{ $task->patient->name }}
                                                </a>
                                            </td>
                                            <td>
                                                @php
                                                    function getPriorityColor($priority) {
                                                        return [
                                                            'low' => 'info',
                                                            'medium' => 'warning',
                                                            'high' => 'danger',
                                                            'urgent' => 'dark'  
                                                        ][$priority] ?? 'secondary';
                                                    }
                                                @endphp
                                                <span class="badge bg-{{ getPriorityColor($task->priority) }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>{{ $task->due_date->format('h:i A') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-task" 
                                                        data-task-id="{{ $task->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-task"
                                                        data-task-id="{{ $task->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clipboard-check display-4 text-muted mb-3"></i>
                        <h5>No tasks found</h5>
                        <p class="text-muted">There are no tasks assigned for your patients today.</p>
                    </div>
                </div>
            </div>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
function getPriorityColor(priority) {
    return {
        'low': 'info',
        'medium': 'warning',
        'high': 'danger',
        'urgent': 'dark'
    }[priority] || 'secondary';
}

$(document).ready(function() {
    // Handle task checkbox changes
    $('.task-checkbox').change(function() {
        const taskId = $(this).data('task-id');
        const status = this.checked ? 'completed' : 'pending';
        
        $.ajax({
            url: `/nurse/tasks/${taskId}/status`,
            type: 'PATCH',
            data: { status },
            success: function() {
                toastr.success('Task status updated');
            },
            error: function() {
                toastr.error('Failed to update task status');
            }
        });
    });

    // Filter tasks
    window.filterTasks = function(status) {
        if (status === 'all') {
            $('.task-row').show();
        } else {
            $('.task-row').hide();
            $(`.task-row.${status}`).show();
        }
    }
});
</script>
@endpush
@endsection