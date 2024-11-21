@extends('nurseAdmin.layout')
@section('title', 'Nurse List')

@section('content')
<div class="container-fluid p-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Nurse List</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Current Assignment</th>
                            <th>Today's Schedule</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nurses as $nurse)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $nurse->profile_picture_url }}" 
                                         class="rounded-circle me-2" 
                                         width="32" 
                                         height="32"
                                         alt="{{ $nurse->name }}'s Profile"
                                         onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                    {{ $nurse->name }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $todaySchedules = $nurse->schedules()
                                        ->with('room')
                                        ->whereDate('date', now()->toDateString())
                                        ->get();
                                @endphp
                                
                                @if($todaySchedules->isNotEmpty())
                                    @foreach($todaySchedules as $schedule)
                                        <span class="badge bg-info">
                                            Room {{ $schedule->room->room_number ?? 'N/A' }} 
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No room assigned</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $todaySchedule = $nurse->schedules->where('date', today())->first();
                                @endphp
                                @if($todaySchedule)
                                    <span class="badge bg-{{ $todaySchedule->status_color }}-subtle text-{{ $todaySchedule->status_color }}">
                                        {{ ucfirst($todaySchedule->shift) }}
                                    </span>
                                @else
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </td>
                            <td>
                                @if($todaySchedule)
                                    <span class="badge bg-success">On Duty</span>
                                @else
                                    <span class="badge bg-secondary">Off Duty</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                        onclick="window.location.href='{{ route('nurseadmin.showNurse', $nurse->id) }}'">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#scheduleModal"
                                        onclick="preSelectNurse({{ $nurse->id }})">
                                    <i class="bi bi-calendar-plus"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No nurses found
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

// Your other JavaScript functions
</script>
@endpush 