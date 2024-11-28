@extends('nurse.layout')
@section('title', 'Patient Details')

@section('content')
<div class="container-fluid p-4">

    <!-- Patient Information Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Patient Information</h5>
                @if(isset($bed) && $bed && $bed->condition)
                    <span class="badge bg-{{ $bed->condition_color }} text-white">
                        {{ $bed->condition }}
                    </span>
                @else
                    <span class="badge bg-secondary text-white">
                        Not Set
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <img src="{{ $user->profile_picture_url }}" 
                         class="rounded-circle mb-3" 
                         width="150" 
                         height="150">
                    <h4>{{ $user->name }}</h4>
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">IC Number</label>
                            <p class="fw-medium">{{ $user->ic_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Gender</label>
                            <p class="fw-medium">{{ $user->gender }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Room & Bed</label>
                            <p class="fw-medium">
                                @if($user->bed)
                                    Room {{ $user->bed->room->room_number }} - 
                                    Bed {{ $user->bed->bed_number }}
                                @else
                                    Not Assigned
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Contact Number</label>
                            <p class="fw-medium">{{ $user->contact_number }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Medical History</label>
                            <p class="fw-medium">{{ is_array($user->medical_history) ? implode(', ', $user->medical_history) : $user->medical_history }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vital Signs Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Vital Signs</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVitalsModal">
                    <i class="bi bi-plus-lg"></i> Add New Reading
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Temperature</th>
                            <th>Blood Pressure</th>
                            <th>Heart Rate</th>
                            <th>Respiratory Rate</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->vitalSigns()->with('nurse')->latest()->take(5)->get() as $vital)
                        <tr>
                            <td>{{ $vital->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $vital->temperature }}°C</td>
                            <td>{{ $vital->blood_pressure }}</td>
                            <td>{{ $vital->heart_rate }} bpm</td>
                            <td>{{ $vital->respiratory_rate }} /min</td>
                            <td>{{ $vital->nurse->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Notes Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Notes</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                    <i class="bi bi-plus-lg"></i> Add Note
                </button>
            </div>
        </div>
        <div class="card-body">
            @foreach($notes as $note)
            <div class="border-bottom mb-3 pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">{{ $note->nurse_name }}</h6>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($note->created_at)->format('M d, Y H:i') }}</small>
                </div>
                <p class="mb-0">{{ $note->content }}</p>
            </div>
            @endforeach

            @if($notes->isEmpty())
                <p class="text-muted text-center mb-0">No notes available</p>
            @endif
        </div>
    </div>
</div>


<style>
/* Keep existing styles */
</style>

@include('nurse.add-vitals')
@include('nurse.add-note')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@endsection