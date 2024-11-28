@extends('nurse.layout')
@section('title', 'Patient Tasks')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="text-gradient">Tasks for {{ $patient->name }}</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="bi bi-plus-lg"></i> Add Task
                </button>
            </div>
        </div>
    </div>

    <div class="calendar-container">
        <table class="table table-bordered calendar-table">
            <thead>
                <tr>
                    <td colspan="7">
                        <div class="nav-buttons text-end">
                            <button class="btn btn-sm btn-outline-primary" id="prev">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" id="today">
                                <i class="fa-solid fa-calendar-days"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" id="next">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary add-task-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#addTaskModal">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="text-center">
                    <th class="text-center bg-dark text-white">Sunday</th>
                    <th class="text-center bg-dark text-white">Monday</th>
                    <th class="text-center bg-dark text-white">Tuesday</th>
                    <th class="text-center bg-dark text-white">Wednesday</th>
                    <th class="text-center bg-dark text-white">Thursday</th>
                    <th class="text-center bg-dark text-white">Friday</th>
                    <th class="text-center bg-dark text-white">Saturday</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $currentDate = $startDate->copy();
                @endphp

                @while ($currentDate <= $endDate)
                    @if ($currentDate->dayOfWeek === 0)
                        <tr>
                    @endif

                    <td class="{{ $currentDate->month !== $date->month ? 'other-month' : '' }} 
                               {{ $currentDate->isToday() ? 'current-day' : '' }}">
                        <div class="calendar-date">
                            <span>{{ $currentDate->day }}</span>
                        </div>
                        <div class="task-container">
                            @foreach($tasks ?? [] as $task)
                                @if($task->due_date->format('Y-m-d') === $currentDate->format('Y-m-d'))
                                    <div class="badge rounded-pill bg-{{ getPriorityColor($task->priority) }} task-item" 
                                         data-task-id="{{ $task->id }}" 
                                         style="cursor: pointer;">
                                        {{ $task->title }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <button class="btn btn-sm btn-outline-primary add-task-btn m-1"
                                data-date="{{ $currentDate->format('Y-m-d') }}"
                                data-bs-toggle="modal"
                                data-bs-target="#addTaskModal">
                            +
                        </button>
                    </td>

                    @if ($currentDate->dayOfWeek === 6)
                        </tr>
                    @endif

                    @php
                        $currentDate->addDay();
                    @endphp
                @endwhile
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .calendar-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.1);
        margin: 20px;
        padding: 20px;
    }

    .calendar-table {
        background: white;
        margin-bottom: 0;
    }

    .calendar-table th {
        background: #f8f9fa;
        padding: 15px;
        font-weight: 600;
    }

    .calendar-table td {
        height: 120px;
        width: 14.28%;
        padding: 8px;
        vertical-align: top;
        border: 1px solid #dee2e6;
    }

    .calendar-date {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .calendar-date span {
        font-weight: 500;
        font-size: 1.1rem;
    }

    .other-month .calendar-date span {
        color: #adb5bd;
    }

    .current-day {
        background-color: #e8f5e9;
    }

    .task-container {
        min-height: 60px;
    }

    .task-item {
        padding: 6px 8px;
        border-radius: 4px;
        margin-bottom: 5px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .task-item:hover {
        transform: translateY(-2px);
    }

    .add-task-btn {
        text-align: right;
        margin-top: 5px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navigation handlers
    document.getElementById('prev').addEventListener('click', () => {
        let newDate = new Date(currentYear, currentMonth - 1, 1);
        window.location.href = `/nurse/patient/{{ $patient->id }}/tasks?month=${newDate.getMonth() + 1}&year=${newDate.getFullYear()}`;
    });
    
    document.getElementById('next').addEventListener('click', () => {
        let newDate = new Date(currentYear, currentMonth + 1, 1);
        window.location.href = `/nurse/patient/{{ $patient->id }}/tasks?month=${newDate.getMonth() + 1}&year=${newDate.getFullYear()}`;
    });
    
    document.getElementById('today').addEventListener('click', () => {
        window.location.href = '/nurse/patient/{{ $patient->id }}/tasks';
    });

    // Update Add Task button to set the date
    $('.add-task-btn').click(function() {
        const taskDate = $(this).data('date');
        $('input[name="due_date"]').val(taskDate);
    });
});
</script>
@endpush
