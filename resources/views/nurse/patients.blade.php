@extends('nurse.layout')
@section('title', 'My Patients')

@section('content')
<div class="container-fluid p-4">
    <!-- Patients List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Patients</h5>
                    <span class="badge bg-primary">{{ $patients->count() }} patients</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Room</th>
                                    <th>Bed</th>
                                    <th>Latest Vitals</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patients as $patient)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('images/profile.png') }}" 
                                                    class="rounded-circle me-2" 
                                                    width="32" 
                                                    height="32">
                                                {{ $patient->name }}
                                            </div>                                           
                                        </td>
                                        <td>Room {{ $patient->bed->room->room_number }}</td>
                                        <td>Bed {{ $patient->bed->bed_number }}</td>
                                        <td>
                                            @if($patient->vital_signs->isNotEmpty())
                                                <span class="text-success">
                                                    <i class="bi bi-check-circle"></i>
                                                    {{ $patient->vital_signs->first()->updated_at->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="text-warning">
                                                    <i class="bi bi-exclamation-circle"></i>
                                                    No vitals recorded
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('nurse.patient.view', $patient->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="{{ route('nurse.patient.tasks', $patient->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-list-check"></i> Tasks
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                                                No patients assigned to your rooms today
                                            </div>
                                        </td>
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
@endsection 