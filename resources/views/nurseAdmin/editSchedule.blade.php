@extends('nurseAdmin.layout')
@section('title', 'Edit Schedule')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Schedule</h5>
                        <a href="{{ route('nurseadminDashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('nurseadmin.updateSchedule', ['schedule' => $schedule->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nurse_id" class="form-label">Nurse</label>
                            <select class="form-select @error('nurse_id') is-invalid @enderror" name="nurse_id" required>
                                <option value="">Select Nurse</option>
                                @foreach($nurses as $nurse)
                                    <option value="{{ $nurse->id }}" {{ $schedule->nurse_id == $nurse->id ? 'selected' : '' }}>
                                        {{ $nurse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nurse_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="room_id" class="form-label">Room</label>
                            <select class="form-select @error('room_id') is-invalid @enderror" name="room_id" required>
                                <option value="">Select Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ $schedule->room_id == $room->id ? 'selected' : '' }}>
                                        Room {{ $room->room_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                   name="date" value="{{ $schedule->date->format('Y-m-d') }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shift" class="form-label">Shift</label>
                            <select class="form-select @error('shift') is-invalid @enderror" name="shift" required>
                                <option value="morning" {{ $schedule->shift == 'morning' ? 'selected' : '' }}>
                                    Morning (7AM - 3PM)
                                </option>
                                <option value="afternoon" {{ $schedule->shift == 'afternoon' ? 'selected' : '' }}>
                                    Afternoon (3PM - 11PM)
                                </option>
                                <option value="night" {{ $schedule->shift == 'night' ? 'selected' : '' }}>
                                    Night (11PM - 7AM)
                                </option>
                            </select>
                            @error('shift')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="scheduled" {{ $schedule->status == 'scheduled' ? 'selected' : '' }}>
                                    Scheduled
                                </option>
                                <option value="completed" {{ $schedule->status == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                                <option value="cancelled" {{ $schedule->status == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                    name="notes" rows="3">{{ $schedule->notes }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('nurseadminDashboard') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Update Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection