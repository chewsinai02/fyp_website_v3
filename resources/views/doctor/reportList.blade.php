@extends('doctor.layout')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-primary-to-secondary p-4 mb-4 rounded-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2 fw-bold">Patient Reports</h2>
                <p class="text-white-50 fs-5 mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    {{ $appointment->patient->name }}'s Medical History
                </p>
            </div>
            <a href="{{ route('doctor.addReport', $appointment->patient->id) }}" 
               class="btn btn-light btn-lg">
                <i class="bi bi-plus-lg me-2"></i>New Report
            </a>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="card border-0 shadow-sm hover-shadow mb-4" 
         style="border-radius: 15px; transition: all 0.3s ease;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="me-4">
                    @if($appointment->patient->profile_picture)
                        <img src="{{ asset($appointment->patient->profile_picture) }}" 
                             alt="{{ $appointment->patient->name }}" 
                             class="rounded-circle shadow-sm"
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center text-white shadow-sm"
                             style="width: 80px; height: 80px; font-size: 32px;">
                            {{ strtoupper(substr($appointment->patient->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="mb-1 fw-bold text-gradient">{{ $appointment->patient->name }}</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-person me-1"></i>
                            {{ ucfirst($appointment->patient->gender) }}
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-calendar me-1"></i>
                            {{ $appointment->patient->getAgeFromIc() }} years
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $appointment->patient->contact_number }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports List Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 rounded-start">
                                <i class="bi bi-calendar-date me-2"></i>Date
                            </th>
                            <th class="border-0">
                                <i class="bi bi-file-text me-2"></i>Title
                            </th>
                            <th class="border-0">
                                <i class="bi bi-clipboard2-pulse me-2"></i>Diagnosis
                            </th>
                            <th class="border-0">
                                <i class="bi bi-check-circle me-2"></i>Status
                            </th>
                            <th class="border-0 rounded-end text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr class="align-middle">
                                <td>
                                    <span class="text-muted">
                                        {{ $report->created_at->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $report->title }}</span>
                                </td>
                                <td>{{ $report->diagnosis }}</td>
                                <td>
                                    <span class="badge {{ $report->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('doctor.viewReport', $report->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="View Report">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('doctor.editReport', $report->id) }}" 
                                           class="btn btn-sm btn-outline-secondary"
                                           data-bs-toggle="tooltip"
                                           title="Edit Report">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('doctor.deleteReport', $report->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this report?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip"
                                                    title="Delete Report">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-folder2-open display-4"></i>
                                        <p class="mt-2">No reports found for this patient.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($reports->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Modern Styling */
.bg-gradient-primary-to-secondary {
    background: linear-gradient(45deg, #1a237e, #0277bd);
}

.text-gradient {
    background: linear-gradient(45deg, #1a237e, #0277bd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.table > :not(caption) > * > * {
    padding: 1rem;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}

.btn-sm {
    padding: .4rem .8rem;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endpush

@endsection
