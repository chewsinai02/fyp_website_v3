@extends('nurseAdmin.layout')
@section('title', 'Calendar')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        overflow-x: hidden;
        overflow-y: hidden;
    }

    .content-wrapper {
        height: calc(100vh - 60px);
        overflow: hidden;
        padding: 1rem;
        max-width: 100%;
    }

    .calendar-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.1);
        margin: 0;
        padding: 15px;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        width: 100%;
    }

    .calendar-header {
        position: sticky;
        top: 0;
        z-index: 10;
        background: white;
        margin: 0 0 15px 0;
        padding: 15px;
        flex-shrink: 0;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .header-title {
        font-size: 1.875rem;
        font-weight: 800;
        color: #1a1a1a;
        letter-spacing: -0.025em;
    }

    .nav-buttons {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        padding: 0.5rem;
    }

    .nav-group {
        display: flex;
        background: white;
        padding: 0.25rem;
        border-radius: 12px;
        gap: 0.25rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .nav-btn {
        border: none;
        background: transparent;
        color: #1e293b;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.15s ease;
    }

    .nav-btn:hover {
        color: #2563eb;
        background: #eff6ff;
    }

    .nav-btn.active {
        background: #2563eb;
        color: white;
    }

    /* Today Button Special Styling */
    #today {
        background: #f3f4f6;
        border-color: #d1d5db;
        font-weight: 600;
    }

    /* FullCalendar Customization */
    .fc {
        background: white;
        padding: 20px;
        border-radius: 15px;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 600;
        color: #2c3e50;
    }

    .fc-day-today {
        background: #f8fafc !important;
    }

    .fc-event {
        border-radius: 6px;
        padding: 4px 8px;
        margin: 2px 0;
        border: none !important;
        transition: transform 0.2s;
    }

    .fc-event:hover {
        transform: translateY(-2px);
    }

    .fc-day-grid-event {
        white-space: normal !important;
        overflow: hidden;
    }

    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        border-bottom: 1px solid #e5e7eb;
        padding: 1.25rem 1.5rem;
        background: white;
    }

    .modal-title {
        font-weight: 600;
        color: #111827;
        font-size: 1.125rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .schedule-info {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .info-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .info-group label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .info-group span {
        font-weight: 500;
        color: #111827;
    }

    .shift-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .shift-badge.morning {
        background: #dbeafe;
        color: #1e40af;
    }

    .shift-badge.evening {
        background: #fff7ed;
        color: #9a3412;
    }

    .shift-badge.night {
        background: #ede9fe;
        color: #5b21b6;
    }

    .room-badge {
        background: #f3f4f6;
        color: #374151;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Add style for Add Schedule button */
    .add-schedule-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        padding: 4px 8px;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        margin-left: 10px;
        font-size: 0.8rem;
    }

    .add-schedule-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    .calendar-table {
        background: white;
        margin: 0;
        min-width: 800px;
    }

    .calendar-table {
        background: white;
        margin-bottom: 0;
    }

    .calendar-table th {
        background: #f8f9fa;
        padding: 15px;
        font-weight: 600;
    }

    .calendar-table td {
        height: 100px;
        width: 14.28%;
        padding: 5px;
        vertical-align: top;
        border: 1px solid #dee2e6;
    }

    .calendar-date {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .calendar-date span {
        font-weight: 500;
        font-size: 1.1rem;
    }

    .other-month .calendar-date span {
        color: #adb5bd;
    }

    .current-day {
        background-color: #e8f5e9;
    }

    .schedule-container {
        min-height: 60px;
    }

    .schedule-item {
        padding: 4px 6px;
        border-radius: 4px;
        margin-bottom: 3px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .schedule-item:hover {
        transform: translateY(-2px);
    }

    .morning-shift {
        background-color: #E3F2FD;
        border-left: 3px solid #2196F3;
    }

    .evening-shift {
        background-color: #FFF3E0;
        border-left: 3px solid #FF9800;
    }

    .night-shift {
        background-color: #E8EAF6;
        border-left: 3px solid #3F51B5;
    }

    .add-schedule-cell {
        text-align: right;
        margin-top: 5px;
    }

    /* Additional badge colors */
    .text-bg-indigo {
        background-color: rgb(102, 16, 242) !important;
        color: white !important;
    }

    .text-bg-purple {
        background-color: rgb(111, 66, 193) !important;
        color: white !important;
    }

    .text-bg-pink {
        background-color: rgb(214, 51, 132) !important;
        color: white !important;
    }

    .text-bg-orange {
        background-color: rgb(253, 126, 20) !important;
        color: white !important;
    }

    .text-bg-teal {
        background-color: rgb(32, 201, 151) !important;
        color: white !important;
    }

    .text-bg-cyan {
        background-color: rgb(13, 202, 240) !important;
        color: white !important;
    }

    /* Ensure text is readable */
    .badge a.nurse-name {
        color: white;
        text-decoration: none;
    }

    /* Hover effect */
    .badge:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    /* Badge and text styling */
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
    }

    .badge a.nurse-name {
        color: white !important;  /* Force white text */
        text-decoration: none;
    }

    /* Custom badge colors */
    .bg-indigo {
        background-color: rgb(102, 16, 242) !important;
    }

    .bg-purple {
        background-color: rgb(111, 66, 193) !important;
    }

    .bg-pink {
        background-color: rgb(214, 51, 132) !important;
    }

    .bg-orange {
        background-color: rgb(253, 126, 20) !important;
    }

    .bg-teal {
        background-color: rgb(32, 201, 151) !important;
    }

    .bg-cyan {
        background-color: rgb(13, 202, 240) !important;
    }

    /* Hover effects */
    .badge:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    /* Light badge specific styling */
    .bg-light a.nurse-name {
        color: #212529 !important;  /* Dark text for light background */
    }

    .badge {
        text-decoration: none !important;
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .badge .nurse-name {
        text-decoration: none !important;
        cursor: pointer;
    }

    /* Default light badge */
    .bg-light .nurse-name {
        color: #212529 !important;
    }

    /* Ensure text colors are applied */
    .text-white {
        color: #ffffff !important;
    }

    .text-dark {
        color: #212529 !important;
    }

    /* Make table header sticky */
    .calendar-table thead tr th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #212529;
    }

    /* Adjust cell heights */
    .calendar-table td {
        height: 100px;
        padding: 5px;
    }

    /* Make schedule items more compact */
    .schedule-item {
        padding: 4px 6px;
        margin-bottom: 3px;
        font-size: 0.8rem;
    }

    /* Adjust add schedule button */
    .add-schedule-btn {
        padding: 4px 8px;
        font-size: 0.8rem;
    }

    /* Make badges more compact */
    .badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    /* Adjust navigation buttons container */
    .nav-buttons {
        padding: 0.5rem;
    }

    /* Make modals more compact on smaller screens */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 0.5rem;
        }

        .calendar-container {
            padding: 10px;
        }

        .calendar-header {
            padding: 10px;
        }

        .header-title {
            font-size: 1.5rem;
        }

        .calendar-table td {
            height: 80px;
        }

        .modal-dialog {
            margin: 0.5rem;
        }
    }

    .table-responsive {
        flex: 1;
        overflow: auto;
        min-height: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .calendar-table {
        margin: 0;
        width: 100% !important;
        min-width: unset !important;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .calendar-table td,
    .calendar-table th {
        width: 14.28% !important;
        min-width: unset !important;
        padding: 5px;
        word-wrap: break-word;
        overflow: hidden;
    }

    .schedule-item {
        padding: 3px 5px;
        margin-bottom: 2px;
        font-size: 0.75rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
        display: block;
    }

    .calendar-table thead th {
        font-size: 0.9rem;
        padding: 8px 4px;
        white-space: nowrap;
    }

    .calendar-table {
        min-width: unset !important;
        max-width: 100% !important;
    }

    .calendar-date span {
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .calendar-table td {
            padding: 3px;
        }

        .schedule-item {
            font-size: 0.7rem;
            padding: 2px 4px;
        }

        .calendar-date span {
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('content')
@php
    $today = \Carbon\Carbon::now();
    // Get the first day of the current month
    $firstDayOfMonth = $date->copy()->startOfMonth();
    // Get the first day of the calendar (last Sunday before or on the first day of month)
    $startDate = $firstDayOfMonth->copy()->startOfWeek(Carbon\Carbon::SUNDAY);
    // Get the last day of the month
    $lastDayOfMonth = $date->copy()->endOfMonth();
    // Get the last day of the calendar (next Saturday after or on the last day of month)
    $endDate = $lastDayOfMonth->copy()->endOfWeek(Carbon\Carbon::SATURDAY);

    // Define available Bootstrap badge colors
    $badgeColors = [
        'primary' => ['bg' => 'primary', 'text' => 'white'],
        'secondary' => ['bg' => 'secondary', 'text' => 'white'],
        'success' => ['bg' => 'success', 'text' => 'white'],
        'danger' => ['bg' => 'danger', 'text' => 'white'],
        'warning' => ['bg' => 'warning', 'text' => 'dark'],  // Dark text for better contrast
        'info' => ['bg' => 'info', 'text' => 'dark'],       // Dark text for better contrast
        'dark' => ['bg' => 'dark', 'text' => 'white']
    ];

    // Additional colors if needed
    $extraColors = [
        'indigo' => 'rgb(102, 16, 242)',
        'purple' => 'rgb(111, 66, 193)',
        'pink' => 'rgb(214, 51, 132)',
        'orange' => 'rgb(253, 126, 20)',
        'teal' => 'rgb(32, 201, 151)',
        'cyan' => 'rgb(13, 202, 240)'
    ];

    // Get or create color mapping for nurses
    if (!session()->has('nurseColors')) {
        $nurseColors = [];
        foreach ($nurses as $index => $nurse) {
            if ($index < count($badgeColors)) {
                $nurseColors[$nurse->id] = array_keys($badgeColors)[$index];
            } else {
                // Random color for additional nurses
                $randomColor = array_rand($badgeColors);
                $nurseColors[$nurse->id] = $randomColor;
            }
        }
        session(['nurseColors' => $nurseColors]);
    }
    $nurseColors = session('nurseColors');
@endphp

<div class="content-wrapper">
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="header-content">
                <h2 class="header-title">{{ $date->format('F Y') }}</h2>
                
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered calendar-table">
                <thead>
                    <tr>
                        <td colspan="7">
                            <div class="nav-buttons text-end">
                                <button class="btn btn-sm btn-outline-primary" id="prev"><i class="fa-solid fa-chevron-left"></i></button>
                                <button class="btn btn-sm btn-outline-primary" id="today" style="font-size: 0.9rem;height: 2.5rem;">Today</button>
                                <button class="btn btn-sm btn-outline-primary" id="next"><i class="fa-solid fa-chevron-right"></i></button>
                                <button class="btn btn-sm btn-outline-primary add-schedule-btn" data-bs-toggle="modal" data-bs-target="#addEventModal"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="text-center">
                        <th class="text-center bg-dark text-white">Sunday</th>
                        <th class="text-center bg-dark text-white">Monday</th>
                        <th class="text-center bg-dark text-white">Tuesday</th>
                        <th class="text-center bg-dark text-white">Wednesday</th>
                        <th class="text-center bg-dark text-white">Thursday</th>
                        <th class="text-center bg-dark text-white">Friday</th>
                        <th class="text-center bg-dark text-white">Saturday</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentDate = $startDate->copy();
                    @endphp

                    @while ($currentDate <= $endDate)
                        @if ($currentDate->dayOfWeek === 0)
                            <tr>
                        @endif

                        <td class="{{ $currentDate->month !== $date->month ? 'other-month' : '' }} 
                                   {{ $currentDate->isToday() ? 'current-day' : '' }}">
                            <div class="calendar-date">
                                <span>{{ $currentDate->day }}</span>
                            </div>
                            <div class="schedule-container">
                                @foreach($initialSchedules ?? [] as $schedule)
                                    @if(Carbon\Carbon::parse($schedule->date)->format('Y-m-d') === $currentDate->format('Y-m-d'))
                                        <div class="badge rounded-pill bg-{{ $nurseColors[$schedule->nurse->id] ?? 'light' }} schedule-item {{ $schedule->shift }}-shift" data-schedule-id="{{ $schedule->id }}" style="cursor: pointer;">
                                            <span class="nurse-name" data-bs-toggle="modal" data-bs-target="#scheduleDetailsModal">
                                                {{ optional($schedule->nurse)->name }}
                                                <span class="shift-badge {{ $schedule->shift }}">
                                                    ({{ ucfirst($schedule->shift) }})
                                                </span>
                                            </span>
                                            <div class="schedule-details" style="display: none;">
                                                <span class="shift-badge {{ $schedule->shift }}">
                                                    {{ ucfirst($schedule->shift) }}
                                                </span>
                                                <span class="room-badge">
                                                    Room {{ optional($schedule->room)->room_number }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <button class="btn btn-sm btn-outline-primary add-schedule-btn m-1"
                                    data-date="{{ $currentDate->format('Y-m-d') }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addEventModal">
                                +
                            </button>
                        </td>

                        @if ($currentDate->dayOfWeek === 6)
                            </tr>
                        @endif

                        @php
                            $currentDate->addDay();
                        @endphp
                    @endwhile
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addScheduleForm">
                    <div class="mb-3">
                        <label class="form-label">Nurse</label>
                        <select class="form-select" name="nurse_id" required>
                            @foreach($nurses as $nurse)
                                <option value="{{ $nurse->id }}">{{ $nurse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room</label>
                        <select class="form-select" name="room_id" required>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">Room {{ $room->room_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shift</label>
                        <select class="form-select" name="shift" required>
                            <option value="morning">Morning</option>
                            <option value="evening">Evening</option>
                            <option value="night">Night</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveSchedule">Save Schedule</button>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="text-muted">Nurse</label>
                    <div class="fw-bold" id="eventNurse"></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Room</label>
                    <div class="fw-bold" id="eventRoom"></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Shift</label>
                    <div class="fw-bold" id="eventShift"></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Status</label>
                    <div class="fw-bold" id="eventStatus"></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Notes</label>
                    <div class="fw-bold" id="eventNotes"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Details Modal -->
<div class="modal fade" id="scheduleDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="schedule-info">
                    <div class="info-group">
                        <label>Name: </label>
                        <span id="modalNurseName"></span>
                    </div>
                    <div class="info-group">
                        <label>Shift: </label>
                        <span id="modalShift" class="shift-badge"></span>
                    </div>
                    <div class="info-group">
                        <label>Room: </label>
                        <span id="modalRoom" class="room-badge"></span>
                    </div>
                    <div class="info-group">
                        <label>Date: </label>
                        <span id="modalDate"></span>
                    </div>
                    <div class="info-group">
                        <label>Status: </label>
                        <span id="modalStatus" class="status-badge"></span>
                    </div>
                    <div class="info-group">
                        <label>Notes (optional): </label>
                        <span id="modalNotes"></span>
                    </div>
                    <div class="info-group m-4 text-center">
                        <button class="btn btn-sm btn-outline-primary" id="assignWeek"><i class="fa-solid fa-repeat"></i> Week</button>
                        <button class="btn btn-sm btn-outline-primary" id="assignMonth"><i class="fa-solid fa-repeat"></i> Month</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Add these lines to get current date from PHP
    let currentDate = new Date('{{ $date->format('Y-m-d') }}');
    let currentYear = currentDate.getFullYear();
    let currentMonth = currentDate.getMonth();

    // Update navigation buttons
    document.getElementById('prev').addEventListener('click', () => {
        let newDate = new Date(currentYear, currentMonth - 1, 1);
        window.location.href = `/full-calendar?month=${newDate.getMonth() + 1}&year=${newDate.getFullYear()}`;
    });
    
    document.getElementById('next').addEventListener('click', () => {
        let newDate = new Date(currentYear, currentMonth + 1, 1);
        window.location.href = `/full-calendar?month=${newDate.getMonth() + 1}&year=${newDate.getFullYear()}`;
    });
    
    document.getElementById('today').addEventListener('click', () => {
        window.location.href = '/full-calendar';
    });

    // Update Add Schedule button to set the date
    $('.add-schedule-btn').click(function() {
        const scheduleDate = $(this).data('date');
        $('input[name="date"]').val(scheduleDate);
    });

    // Save new schedule
    $('#saveSchedule').click(function() {
        const form = $('#addScheduleForm');
        const formData = {
            nurse_id: form.find('select[name="nurse_id"]').val(),
            room_id: form.find('select[name="room_id"]').val(),
            shift: form.find('select[name="shift"]').val(),
            date: form.find('input[name="date"]').val(),
            notes: form.find('textarea[name="notes"]').val(),
            type: 'add'
        };

        // Validate required fields
        if (!formData.nurse_id || !formData.room_id || !formData.shift || !formData.date) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Please fill in all required fields'
            });
            return;
        }

        // Show loading state
        $('#saveSchedule').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
        );

        $.ajax({
            url: "{{ route('schedules.action') }}",
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Hide modal
                $('#addEventModal').modal('hide');
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Schedule has been saved successfully',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload the page to show new schedule
                    window.location.reload();
                });
            },
            error: function(xhr, status, error) {
                console.error('Save error:', {xhr, status, error});
                
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Failed to save schedule. Please try again.'
                });
            },
            complete: function() {
                // Reset button state
                $('#saveSchedule').prop('disabled', false).text('Save Schedule');
            }
        });
    });

    // Reset form when modal is closed
    $('#addEventModal').on('hidden.bs.modal', function() {
        $('#addScheduleForm')[0].reset();
    });

    // Delete schedule
    $('#deleteSchedule').click(function() {
        if (confirm('Are you sure you want to delete this schedule?')) {
            const scheduleId = $(this).closest('.schedule-item').data('schedule-id');
            
            $.ajax({
                url: '/calendar/action',
                type: 'POST',
                data: {
                    id: scheduleId,
                    type: 'delete'
                },
                success: function(response) {
                    $('#eventModal').modal('hide');
                    window.location.reload();
                    toastr.success('Schedule deleted successfully');
                },
                error: function() {
                    toastr.error('Failed to delete schedule');
                }
            });
        }
    });

    // Schedule Details Modal
    $(document).on('click', '.schedule-item', function() {
        const scheduleId = $(this).data('schedule-id');
        $.ajax({
            url: `/calendar/schedule/${scheduleId}`,
            type: 'GET',
            success: function(schedule) {
                $('#modalNurseName').text(schedule.nurse?.name ?? 'Unassigned');
                $('#modalRoom').text(schedule.room ? `Room ${schedule.room.room_number}` : 'Unassigned');
                $('#modalShift').text(schedule.shift ? schedule.shift.charAt(0).toUpperCase() + schedule.shift.slice(1) : '');
                $('#modalDate').text(moment(schedule.date).format('YYYY-MM-DD'));
                $('#modalStatus').text(schedule.status ? schedule.status.charAt(0).toUpperCase() + schedule.status.slice(1) : 'Pending');
                $('#modalNotes').text(schedule.notes || 'No notes');
                
                // Store the schedule data for week/month assignment
                window.selectedSchedule = schedule;
                
                const modal = new bootstrap.Modal(document.getElementById('scheduleDetailsModal'));
                modal.show();
            }
        });
    });

    // Week Assignment Button
    $('#assignWeek').click(function() {
        if (!window.selectedSchedule) {
            toastr.error('Please select a schedule first');
            return;
        }

        Swal.fire({
            title: 'Weekly Schedule Assignment',
            html: `
                <form id="weeklyAssignForm" class="text-start">
                    <div class="mb-3">
                        <label class="form-label">Nurse</label>
                        <select class="form-select" id="weeklyNurse" required disabled>
                            <option value="${window.selectedSchedule.nurse.id}" selected>
                                ${window.selectedSchedule.nurse.name}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room</label>
                        <select class="form-select" id="weeklyRoom" required disabled>
                            <option value="${window.selectedSchedule.room.id}" selected>
                                Room ${window.selectedSchedule.room.room_number}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shift</label>
                        <select class="form-select" id="weeklyShift" required disabled>
                            <option value="${window.selectedSchedule.shift}" selected>
                                ${window.selectedSchedule.shift.charAt(0).toUpperCase() + window.selectedSchedule.shift.slice(1)}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Week Starting From</label>
                        <input type="date" class="form-control" id="weekStartDate" required 
                               value="${window.selectedSchedule.date}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Rest Days</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input weekly-rest-day" 
                                           value="{{ $index }}" id="weeklyRest{{ $index }}">
                                    <label class="form-check-label" for="weeklyRest{{ $index }}">
                                        {{ $day }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-danger">* At least one rest day required</small>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Assign Schedule',
            didOpen: () => {
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('weekStartDate').min = today;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = {
                    nurse_id: window.selectedSchedule.nurse.id,
                    room_id: window.selectedSchedule.room.id,
                    shift: window.selectedSchedule.shift,
                    start_date: $('#weekStartDate').val(),
                    rest_days: $('.weekly-rest-day:checked').map(function() {
                        return parseInt($(this).val());
                    }).get()
                };

                $.ajax({
                    url: '/nurseadmin/schedules/assign-week',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire('Success', 'Weekly schedule assigned successfully', 'success')
                            .then(() => window.location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to assign schedule', 'error');
                    }
                });
            }
        });
    });

    // Month Assignment Button
    $('#assignMonth').click(function() {
        if (!window.selectedSchedule) {
            toastr.error('Please select a schedule first');
            return;
        }

        Swal.fire({
            title: 'Monthly Schedule Assignment',
            html: `
                <form id="monthlyAssignForm" class="text-start">
                    <div class="mb-3">
                        <label class="form-label">Nurse</label>
                        <select class="form-select" id="monthlyNurse" required disabled>
                            <option value="${window.selectedSchedule.nurse.id}" selected>
                                ${window.selectedSchedule.nurse.name}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room</label>
                        <select class="form-select" id="monthlyRoom" required disabled>
                            <option value="${window.selectedSchedule.room.id}" selected>
                                Room ${window.selectedSchedule.room.room_number}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shift</label>
                        <select class="form-select" id="monthlyShift" required disabled>
                            <option value="${window.selectedSchedule.shift}" selected>
                                ${window.selectedSchedule.shift.charAt(0).toUpperCase() + window.selectedSchedule.shift.slice(1)}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Month</label>
                        <input type="month" class="form-control" id="monthSelect" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Weekly Rest Days</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input monthly-rest-day" 
                                           value="{{ $index }}" id="monthlyRest{{ $index }}">
                                    <label class="form-check-label" for="monthlyRest{{ $index }}">
                                        {{ $day }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-danger">* At least one rest day per week required</small>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Assign Schedule',
            didOpen: () => {
                const today = new Date();
                const minMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
                document.getElementById('monthSelect').min = minMonth;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = {
                    nurse_id: window.selectedSchedule.nurse.id,
                    room_id: window.selectedSchedule.room.id,
                    shift: window.selectedSchedule.shift,
                    month: $('#monthSelect').val(),
                    rest_days: $('.monthly-rest-day:checked').map(function() {
                        return parseInt($(this).val());
                    }).get()
                };

                $.ajax({
                    url: '/nurseadmin/schedules/assign-month',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire('Success', 'Monthly schedule assigned successfully', 'success')
                            .then(() => window.location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to assign schedule', 'error');
                    }
                });
            }
        });
    });

    // Delete Schedule
    $('#deleteSchedule').click(function() {
        const scheduleId = $(this).data('schedule-id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/nurseadmin/schedules/${scheduleId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'Schedule has been deleted.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Failed to delete schedule',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush