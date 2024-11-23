@extends('nurseAdmin.layout')
@section('title', 'Schedule List')

@section('content')
<div class="container-fluid p-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Nurse Schedules</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Schedule
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Date Filter -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Filter</span>
                        <input type="date" 
                               class="form-control" 
                               id="date_filter"
                               name="date" 
                               value="{{ request('date', date('Y-m-d')) }}"
                               onchange="filterSchedules(this.value)">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nurse</th>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Room Assignment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="schedules_table">
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $schedule->nurse->profile_picture_url }}" 
                                            class="rounded-circle me-2" 
                                            width="32" 
                                            height="32"
                                            alt="{{ $schedule->nurse->name }}'s Profile"
                                            onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                        {{ $schedule->nurse->name }}
                                    </div>
                                </td>
                                <td>{{ $schedule->date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $schedule->shift_color }}-subtle text-{{ $schedule->shift_color }}">
                                        {{ ucfirst($schedule->shift) }}
                                    </span>
                                </td>
                                <td>
                                    @if($schedule->room)
                                        <span class="badge bg-info">
                                            Room {{ $schedule->room->room_number }}
                                        </span>
                                    @else
                                        <span class="text-muted">No room assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $schedule->status_color }}-subtle text-{{ $schedule->status_color }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('nurseadmin.editSchedule', ['schedule' => $schedule->id]) }}" 
                                    class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('nurseadmin.deleteSchedule', ['schedule' => $schedule->id]) }}" 
                                        method="POST" 
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this schedule?');">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No schedules found for this date
                                </td>
                            </tr>
                            @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include the schedule modal with nurses data -->
@include('nurseAdmin.schedule', ['nurses' => $nurses])

@push('scripts')
<script>
    function filterSchedules(date) {
       console.log("Filtering schedules for date:", date); // Debugging line
       if (date) {
           window.location.href = `{{ route('nurseadmin.scheduleList') }}?date=${date}`;
       }
   }
</script>
@endpush 
@endsection