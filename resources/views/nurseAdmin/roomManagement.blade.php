@extends('nurseAdmin.layout')
@section('title', 'Room Management')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Room Management</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Room
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Room Number</th>
                            <th>Floor</th>
                            <th>Total Beds</th>
                            <th>Available Beds</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                        <tr>
                            <td>{{ $room->room_number }}</td>
                            <td>{{ $room->floor }}</td>
                            <td>{{ $room->total_beds }}</td>
                            <td>{{ $room->available_beds }}</td>
                            <td>
                                <span class="badge bg-{{ $room->status === 'available' ? 'success' : 'warning' }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($room->type) }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                        onclick="showBeds({{ $room->id }})">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success me-1 edit-room-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editRoomModal" 
                                        onclick="editRoom({{ $room->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('nurseadmin.rooms.destroy', ['room' => $room->id]) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this room?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No rooms found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('nurseAdmin.addRoom')
@include('nurseAdmin.editRoom')
@include('nurseAdmin.beds')

@endsection
