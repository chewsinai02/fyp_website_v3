@extends('nurseAdmin.layout')
@section('title', 'Reports')

@section('content')
<div class="container-fluid p-4">
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Schedule Status</h6>
                    <canvas id="scheduleChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Room Status</h6>
                    <canvas id="roomChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Nurse Workload</h6>
                    <canvas id="nurseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Schedule Report</h5>
                    <p class="text-muted">Generate detailed schedule reports for all nurses.</p>
                    <a href="{{ route('nurseadmin.scheduleReport') }}" class="btn btn-primary">
                        <i class="bi bi-calendar-check me-1"></i> View Schedule Report
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Assignment Report</h5>
                    <p class="text-muted">View detailed room assignment history.</p>
                    <a href="{{ route('nurseadmin.assignmentReport') }}" class="btn btn-primary">
                        <i class="bi bi-door-open me-1"></i> View Assignment Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pass PHP variables to JavaScript
const scheduleStats = @json($scheduleStats);
const roomStats = @json($roomStats);
const nurseStats = @json($nurseStats);

document.addEventListener('DOMContentLoaded', function() {
    // Schedule Status Chart
    new Chart(document.getElementById('scheduleChart'), {
        type: 'pie',
        data: {
            labels: scheduleStats.map(item => item.status),
            datasets: [{
                data: scheduleStats.map(item => item.count),
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Room Status Chart
    new Chart(document.getElementById('roomChart'), {
        type: 'pie',
        data: {
            labels: roomStats.map(item => item.status),
            datasets: [{
                data: roomStats.map(item => item.count),
                backgroundColor: [
                    '#198754',
                    '#ffc107'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Nurse Workload Chart
    new Chart(document.getElementById('nurseChart'), {
        type: 'bar',
        data: {
            labels: nurseStats.map(nurse => nurse.name),
            datasets: [{
                label: 'Schedules This Month',
                data: nurseStats.map(nurse => nurse.schedules_count),
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush 