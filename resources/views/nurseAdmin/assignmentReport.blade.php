@extends('nurseAdmin.layout')
@section('title', 'Assignment Report')

@section('content')
<div class="container-fluid p-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assignment Report</h5>
                <div>
                    <button class="btn btn-primary" onclick="exportReport('assignment')">
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
                            <th>Nurse</th>
                            <th>Total Assignments</th>
                            <th>Current Assignment</th>
                            <th>Upcoming Assignments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedSchedules as $nurseId => $nurseSchedules)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $nurseSchedules->first()->nurse->user->profile_picture ?? asset('images/profile.png') }}" 
                                             class="rounded-circle me-2" 
                                             width="32" 
                                             height="32"
                                             alt="Profile">
                                        {{ $nurseSchedules->first()->nurse->name }}
                                    </div>
                                </td>
                                <td>{{ $nurseSchedules->count() }}</td>
                                <td>
                                    @php
                                        $currentAssignment = $nurseSchedules
                                            ->where('date', today())
                                            ->first();
                                    @endphp
                                    @if($currentAssignment)
                                        <span class="badge bg-info">
                                            Room {{ $currentAssignment->room->room_number }}
                                            ({{ ucfirst($currentAssignment->shift) }})
                                        </span>
                                    @else
                                        <span class="text-muted">No current assignment</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $upcomingAssignments = $nurseSchedules
                                            ->where('date', '>', today())
                                            ->take(3);
                                    @endphp
                                    @forelse($upcomingAssignments as $assignment)
                                        <span class="badge bg-secondary me-1">
                                            {{ $assignment->date->format('M d') }} - 
                                            Room {{ $assignment->room->room_number }}
                                            ({{ ucfirst($assignment->shift) }})
                                        </span>
                                    @empty
                                        <span class="text-muted">No upcoming assignments</span>
                                    @endforelse
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No assignments found
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
    window.location.href = `{{ route('nurseadmin.exportAssignmentReport') }}?type=${type}`;
}
</script>
@endpush