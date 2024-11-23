@extends('nurseAdmin.layout')
@section('title', 'Schedule Report')

@section('content')
<div class="container-fluid p-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Schedule Report</h5>
                <div>
                    <button class="btn btn-primary" onclick="exportReport('schedule')">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Nurse</th>
                            <th>Shift</th>
                            <th>Room Assignment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->date->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $schedule->nurse->user->profile_picture ?? asset('images/profile.png') }}" 
                                         class="rounded-circle me-2" 
                                         width="32" 
                                         height="32"
                                         alt="Profile">
                                    {{ $schedule->nurse->name }}
                                </div>
                            </td>
                            <td>{{ ucfirst($schedule->shift) }}</td>
                            <td>
                                @if($schedule->room)
                                    <span class="badge bg-info">Room {{ $schedule->room->room_number }}</span>
                                @else
                                    <span class="text-muted">No room assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $schedule->status_color }}-subtle text-{{ $schedule->status_color }}">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No schedules found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportReport(type) {
    window.location.href = `{{ route('nurseadmin.exportScheduleReport') }}?type=${type}`;
}
</script>
@endpush
