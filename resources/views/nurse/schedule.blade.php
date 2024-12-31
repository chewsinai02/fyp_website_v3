@extends('nurse.layout')
@section('title', 'My Schedule')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">My Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Shift</th>
                                    <th>Room</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $schedule->shift_color }}-subtle text-{{ $schedule->shift_color }}">
                                                {{ ucfirst($schedule->shift) }}
                                            </span>
                                        </td>
                                        <td>Room {{ $schedule->room_number }}</td>
                                        <td>
                                            <span class="badge bg-{{ $schedule->date->isPast() ? 'secondary' : 'success' }}-subtle text-{{ $schedule->date->isPast() ? 'secondary' : 'success' }}">
                                                {{ $schedule->date->isPast() ? 'Completed' : 'Upcoming' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No schedules found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Badge styling */
.badge.bg-info-subtle {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.badge.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.badge.bg-dark-subtle {
    background-color: rgba(33, 37, 41, 0.1) !important;
}

.badge.text-info {
    color: #0dcaf0 !important;
}

.badge.text-warning {
    color: #ffc107 !important;
}

.badge.text-dark {
    color: #212529 !important;
}

/* Make badges more readable */
.badge {
    font-size: 0.85rem;
    padding: 0.5em 0.85em;
    font-weight: 500;
}

/* Status badge colors */
.badge.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.badge.bg-secondary-subtle {
    background-color: rgba(108, 117, 125, 0.1) !important;
}

.badge.text-success {
    color: #198754 !important;
}

.badge.text-secondary {
    color: #6c757d !important;
}
</style>
@endsection 