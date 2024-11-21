@extends('nurseAdmin.layout')
@section('title', 'Nurse Details')

@section('content')
<div class="container-fluid p-4">
    <!-- Nurse Profile Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="{{ $nurse->profile_picture_url }}" 
                         class="rounded-circle mb-3" 
                         width="150" 
                         height="150"
                         alt="Profile"
                         onerror="this.src='{{ asset('images/profile.png') }}'">
                </div>
                <div class="col-md-9">
                    <h3 class="mb-3">{{ $nurse->name }}</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Email</p>
                            <p class="fw-medium">{{ $nurse->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Current Status</p>
                            <p>
                                @if($nurse->schedules->where('date', today())->where('status', 'scheduled')->isNotEmpty())
                                    <span class="badge bg-success">On Duty</span>
                                @else
                                    <span class="badge bg-secondary">Off Duty</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Current Assignment</p>
                            <p>
                                @if($nurse->schedules->where('date', today())->isNotEmpty())
                                    @foreach($nurse->schedules->where('date', today()) as $schedule)
                                        @if($schedule->room)
                                            <span class="badge bg-info">
                                                Room {{ $schedule->room->room_number }}
                                                ({{ ucfirst($schedule->shift) }})
                                            </span>
                                        @else
                                            <span class="text-muted">No room assigned for {{ ucfirst($schedule->shift) }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-muted">Not scheduled today</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#scheduleModal"
                                onclick="preSelectNurse({{ $nurse->id }})">
                            <i class="bi bi-calendar-plus me-1"></i> Add Schedule
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedules Tab -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Upcoming Schedules</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Room</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nurse->schedules->where('date', '>=', today())->sortBy('date') as $schedule)
                        <tr>
                            <td>{{ $schedule->date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $schedule->shift_color }}-subtle text-{{ $schedule->shift_color }}">
                                    {{ ucfirst($schedule->shift) }}
                                    @php
                                        $shiftTimes = [
                                            'morning' => '(7AM - 3PM)',
                                            'afternoon' => '(3PM - 11PM)',
                                            'night' => '(11PM - 7AM)'
                                        ];
                                    @endphp
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
                            <td colspan="5" class="text-center py-4 text-muted">
                                No upcoming schedules
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assignment History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assignment History</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nurse->schedules->where('date', '>=', today())->sortBy('date') as $schedule)
                        <tr>
                            <td>
                                @if($schedule->room)
                                    <span class="badge bg-info">
                                        Room {{ $schedule->room->room_number }}
                                    </span>
                                @else
                                    <span class="text-muted">No room assigned</span>
                                @endif
                            </td>
                            <td>{{ $schedule->date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $schedule->shift_color }}-subtle text-{{ $schedule->shift_color }}">
                                    {{ ucfirst($schedule->shift) }}
                                    @php
                                        $shiftTimes = [
                                            'morning' => '(7AM - 3PM)',
                                            'afternoon' => '(3PM - 11PM)',
                                            'night' => '(11PM - 7AM)'
                                        ];
                                    @endphp
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $schedule->status_color }}-subtle text-{{ $schedule->status_color }}">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </td>
                            <td>
                                @if($schedule->notes)
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                          data-bs-toggle="tooltip" 
                                          title="{{ $schedule->notes }}">
                                        {{ $schedule->notes }}
                                    </span>
                                @else
                                    <span class="text-muted">No notes</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No assignment history
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('nurseAdmin.schedule')
@include('nurseAdmin.assignRoom')
@endsection

@push('scripts')
<script>
function preSelectNurse(nurseId) {
    document.getElementById('nurse_id').value = nurseId;
}

function deleteSchedule(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This schedule will be deleted permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/nurseadmin/schedules/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Schedule has been deleted.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete schedule');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to delete schedule'
                });
            });
        }
    });
}
</script>
@endpush 