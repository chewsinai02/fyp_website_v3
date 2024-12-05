@extends('nurse.layout')
@section('title', 'Patient Tasks')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="text-gradient">Tasks for {{ $patient->name }}</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="bi bi-plus-lg"></i> Add Task
                </button>
            </div>
        </div>
    </div>

    @php
        // Define the priority color function if not already defined
        if (!function_exists('getPriorityColor')) {
            function getPriorityColor($priority) {
                return match(strtolower($priority)) {
                    'low' => 'success',
                    'medium' => 'warning',
                    'high' => 'orange',
                    'urgent' => 'danger',
                    default => 'secondary',
                };
            }
        }

        if (!function_exists('getStatusColor')) {
            function getStatusColor($status) {
                return match(strtolower($status)) {
                    'completed' => 'success',
                    'pending' => 'warning',
                    'passed' => 'danger',
                    'cancelled' => 'secondary',
                    default => 'secondary',
                };
            }
        }
    @endphp

    <div class="calendar-container">
        <div class="calendar-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="header-title">{{ $date->format('F Y') }}</h4>
                <div class="nav-buttons">
                    <button class="btn btn-sm btn-outline-primary" id="prev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary mx-2" id="today">Today</button>
                    <button class="btn btn-sm btn-outline-primary" id="next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <table class="table table-bordered calendar-table">
            <thead>
                <tr>
                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                        <th class="text-center bg-dark text-white">{{ $day }}</th>
                    @endforeach
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
                               {{ $currentDate->isToday() ? 'current-day' : '' }}"
                        data-date="{{ $currentDate->format('Y-m-d') }}">
                        <div class="calendar-date">
                            <span>{{ $currentDate->day }}</span>
                        </div>
                        <div class="task-container">
                            @foreach($tasks as $task)
                                @if($task->due_date->format('Y-m-d') === $currentDate->format('Y-m-d'))
                                    <div class="task-item badge bg-{{ getPriorityColor($task->priority) }} w-100 mb-1 view-task" 
                                         data-task-id="{{ $task->id }}"
                                         title="{{ $task->description }}">
                                        {{ Str::limit($task->title, 20) }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
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

<!-- Task List -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0" id="taskListDate">Tasks for {{ \Carbon\Carbon::today()->format('F d, Y') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="taskTable">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Task</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Due Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr data-task-id="{{ $task->id }}" data-date="{{ $task->due_date->format('Y-m-d') }}">
                                    <td>
                                        <input type="checkbox" 
                                               class="form-check-input task-status-checkbox" 
                                               data-task-id="{{ $task->id }}"
                                               {{ $task->status === 'completed' ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ getPriorityColor($task->priority) }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ getStatusColor($task->status) }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $task->due_date->format('Y-m-d') }}</td>
                                    <td>{{ $task->due_date->format('H:i') }}</td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-info view-task" 
                                                data-task-id="{{ $task->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-task" 
                                                data-task-id="{{ $task->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="no-tasks">
                                    <td colspan="6" class="text-center">No tasks for today</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm" action="{{ route('nurse.patient.tasks.store', $patient->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" required>
                            <option value="">Select Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Date and Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" 
                                   name="due_date" 
                                   required
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   step="1800">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalTaskTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="schedule-info">
                    <div class="info-group">
                        <label class="form-label fw-bold">Title:</label>
                        <p id="modalTaskTitle" class="mb-2"></p>
                    </div>
                    <div class="info-group">
                        <label class="form-label fw-bold">Description:</label>
                        <p id="modalTaskDescription" class="mb-2"></p>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Priority:</label>
                            <p id="modalTaskPriority"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p id="modalTaskStatus"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Due Date:</label>
                        <p id="modalTaskDueDate"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between border-0 pt-0">
                <button type="button" class="btn btn-sm btn-outline-primary" id="editTaskButton">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" 
                        class="btn btn-sm btn-outline-danger delete-task" 
                        id="deleteTaskButton">
                    <i class="bi bi-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" id="repeatWeeklyButton">
                    <i class="bi bi-arrow-repeat"></i> Weekly
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" id="repeatMonthlyButton">
                    <i class="bi bi-arrow-repeat"></i> Monthly
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" onclick="location.reload()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_task_id" name="task_id">
                    
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Task Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_priority" class="form-label">Priority</label>
                        <select class="form-select" id="edit_priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_due_date" class="form-label">Due Date</label>
                        <input type="datetime-local" class="form-control" id="edit_due_date" name="due_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditButton">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-content {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}

#modalTaskDescription {
    border-left: 3px solid #e9ecef;
    min-height: 50px;
}

.modal-footer .btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

.modal-footer .btn i {
    font-size: 0.875rem;
}

/* Calendar Styles */
.calendar-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    padding: 1rem;
}

.calendar-table {
    table-layout: fixed;
}

.calendar-table th {
    text-align: center;
    padding: 10px;
    font-weight: 500;
}

.calendar-table td {
    height: 120px;
    vertical-align: top;
    padding: 8px;
    position: relative;
}

.calendar-date {
    cursor: pointer;
    font-weight: 500;
    margin-bottom: 5px;
}

.calendar-date:hover {
    background-color: #f8f9fa;
    border-radius: 4px;
}

.calendar-date span {
    display: inline-block;
    width: 25px;
    height: 25px;
    text-align: center;
    line-height: 25px;
    border-radius: 50%;
}

.current-day .calendar-date span {
    background-color: #0d6efd;
    color: white;
}

.other-month {
    background-color: #f8f9fa;
    opacity: 0.7;
}

.task-container {
    max-height: 60px;
    overflow-y: auto;
    margin-bottom: 5px;
}

.task-item {
    cursor: pointer;
    font-size: 0.8rem;
    padding: 2px 5px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    text-align: left;
}

.task-item:hover {
    opacity: 0.9;
}

/* Add Task Button */
.add-task-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    padding: 2px 6px;
    font-size: 0.8rem;
}

/* Task List Styles */
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.task-status-checkbox {
    cursor: pointer;
}

.badge {
    font-weight: 500;
}

/* Navigation Buttons */
.nav-buttons .btn {
    padding: 0.25rem 0.5rem;
}

/* Scrollbar Styles */
.task-container::-webkit-scrollbar {
    width: 4px;
}

.task-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.task-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.task-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Priority Colors */
.bg-low {
    background-color: #28a745 !important;
}

.bg-medium {
    background-color: #ffc107 !important;
}

.bg-high {
    background-color: #fd7e14 !important;
}

.bg-urgent {
    background-color: #dc3545 !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .calendar-table td {
        height: 100px;
    }
    
    .task-container {
        max-height: 40px;
    }
    
    .calendar-date span {
        width: 20px;
        height: 20px;
        line-height: 20px;
        font-size: 0.9rem;
    }
}

.task-item {
    cursor: pointer;
    transition: opacity 0.2s;
}

.task-item:hover {
    opacity: 0.8;
}

.modal-body label {
    color: #666;
    font-size: 0.9rem;
}

.modal-body p {
    font-size: 1rem;
    color: #333;
}

.calendar-date {
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
}

.calendar-date:hover {
    background-color: #f0f0f0;
}

.task-item {
    cursor: pointer;
    transition: opacity 0.2s;
}

.task-item:hover {
    opacity: 0.8;
}

.selected-date {
    background-color: #e9ecef;
}

.bg-orange {
    background-color: #fd7e14 !important; /* Bootstrap's orange color */
}
</style>

<!--calendar navigation-->
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

        // Update navigation buttons with correct route
        document.getElementById('prev').addEventListener('click', () => {
            let newDate = new Date(currentYear, currentMonth - 1, 1);
            window.location.href = `/nurse/patient/{{ $patient->id }}/tasks?month=${newDate.getMonth() + 1}&year=${newDate.getFullYear()}`;
        });
        
        document.getElementById('next').addEventListener('click', () => {
            let newDate = new Date(currentYear, currentMonth + 1, 1);
            window.location.href = `/nurse/patient/{{ $patient->id }}/tasks?month=${newDate.getMonth() + 1}&year=${newDate.getFullYear()}`;
        });
        
        document.getElementById('today').addEventListener('click', () => {
            window.location.href = '{{ route('nurse.patient.tasks', $patient->id) }}';
        });
    });
</script>


<!--calendar-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for calendar cells
    document.querySelectorAll('.calendar-table td').forEach(cell => {
        cell.addEventListener('click', function() {
            const selectedDate = this.dataset.date;
            if (!selectedDate) return; // Skip if no date (empty cell)

            // Visual feedback for selected date
            document.querySelectorAll('.calendar-table td').forEach(td => {
                td.classList.remove('selected-date');
            });
            this.classList.add('selected-date');

            // Format the selected date for display
            const formattedDate = new Date(selectedDate);
            const dateString = formattedDate.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });

            // Update the task list header with selected date
            document.getElementById('taskListDate').textContent = `Tasks for ${dateString}`;

            // Filter tasks for the selected date
            const taskRows = document.querySelectorAll('#taskTable tbody tr:not(.no-tasks)');
            let hasVisibleTasks = false;

            taskRows.forEach(row => {
                if (row.dataset.date === selectedDate) {
                    row.style.display = '';
                    hasVisibleTasks = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide "No tasks" message
            const noTasksRow = document.querySelector('.no-tasks');
            if (noTasksRow) {
                if (!hasVisibleTasks) {
                    noTasksRow.style.display = '';
                    noTasksRow.querySelector('td').textContent = `No tasks for ${dateString}`;
                } else {
                    noTasksRow.style.display = 'none';
                }
            }
        });
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $(document).on('click', '.view-task', function() {
        const taskId = $(this).data('task-id'); // Get the task ID from the data attribute
        const patientId = '{{ $patient->id }}'; // Get the patient ID from the Blade variable

        // Construct the URL for fetching task details
        const url = `/nurse/patient/${patientId}/tasks/details`; // Include patientId in the URL

        // Get task details using AJAX
        $.ajax({
            url: url,
            method: 'POST', // Use POST to send data securely
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // CSRF Token for security
                'Accept': 'application/json'
            },
            data: { task_id: taskId }, // Send the task ID to the backend
            success: function(response) {
                if (response && !response.error) {
                    // Update modal content with fetched data
                    $('#modalTaskTitle').text(response.title || 'No title');
                    $('#modalTaskDescription').text(response.description || 'No description provided');
                    $('#modalTaskPriority').html(`
                        <span class="badge bg-${response.priority.toLowerCase()}">
                            ${response.priority.charAt(0).toUpperCase() + response.priority.slice(1)}
                        </span>
                    `);
                    const statusClass = getStatusClass(response.status); // Function to get the class based on status
                    $('#modalTaskStatus').html(`
                        <span class="badge ${statusClass}">
                            ${response.status.charAt(0).toUpperCase() + response.status.slice(1)}
                        </span>
                    `);
                    $('#modalTaskDueDate').text(moment(response.due_date).format('MMMM D, YYYY h:mm A'));

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
                    modal.show();
                } else {
                    // Handle error, e.g., display an error message
                    $('.schedule-info').html(`
                        <div class="alert alert-danger">
                            ${response.error || 'Task not found'}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                alert('Failed to load task details. Please try again.');
            }
        });
        function getStatusClass(status) {
            switch (status.toLowerCase()) {
                case 'pending':
                    return 'bg-warning'; // Yellow
                case 'completed':
                    return 'bg-success'; // Green
                case 'passed':
                    return 'bg-danger'; // Red
                case 'cancelled':
                    return 'bg-danger'; // Red (or you can create a custom class)
                default:
                    return 'bg-secondary'; // Default class for unknown status
            }
        }
    });
});
</script>

<script>
$(document).ready(function() {
    // Use event delegation to handle click events on dynamically added elements
    $(document).on('click', '.delete-task', function() {
        const taskId = $(this).data('task-id'); // Get the task ID from the button
        const patientId = '{{ $patient->id }}'; // Get the patient ID from the Blade variable

        // Use SweetAlert2 for better confirmation dialog
        Swal.fire({
            title: 'Delete Task?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Construct the URL for deleting the task
                const url = `/nurse/patient/${patientId}/tasks/${taskId}`; // Include patientId in the URL

                // Delete task using AJAX
                $.ajax({
                    url: url,
                    method: 'DELETE', // Use DELETE method
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token for security
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // Refresh the page to update the task list
                            });
                        } else {
                            Swal.fire('Error!', 'Failed to delete task', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        Swal.fire('Error!', 'Failed to delete task. Please try again.', 'error');
                    }
                });
            }
        });
    });
});
</script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include Moment.js (if used for date formatting) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get today's date in YYYY-MM-DD format
    const today = moment().format('YYYY-MM-DD');
    
    // Update the task list header with today's date
    const todayDateString = moment().format('MMMM D, YYYY');
    document.getElementById('taskListDate').textContent = `Tasks for ${todayDateString}`;

    // Filter tasks for today's date
    const taskRows = document.querySelectorAll('#taskTable tbody tr:not(.no-tasks)');
    let hasVisibleTasks = false;

    taskRows.forEach(row => {
        if (row.dataset.date === today) {
            row.style.display = '';
            hasVisibleTasks = true;
        } else {
            row.style.display = 'none';
        }
    });

    // Show/hide "No tasks" message
    const noTasksRow = document.querySelector('.no-tasks');
    if (noTasksRow) {
        if (!hasVisibleTasks) {
            noTasksRow.style.display = '';
            noTasksRow.querySelector('td').textContent = `No tasks for ${todayDateString}`;
        } else {
            noTasksRow.style.display = 'none';
        }
    }

    // Highlight today's date in the calendar
    const todayCell = document.querySelector(`td[data-date="${today}"]`);
    if (todayCell) {
        todayCell.classList.add('selected-date');
    }
});
</script>

<script>
//update task status in checkbox
$(document).ready(function() {
    $(document).on('change', '.task-status-checkbox', function() {
        const taskId = $(this).data('task-id'); // Get the task ID from the checkbox
        const isChecked = $(this).is(':checked'); // Check if the checkbox is checked
        const patientId = '{{ $patient->id }}'; // Get the patient ID from the Blade variable

        // Get the current date
        const currentDate = new Date();
        const dueDate = new Date($(this).data('due-date')); // Assuming you have a data attribute for due date

        let status;
        if (dueDate < currentDate) {
            status = 'cancelled'; // Set status to cancelled if due date has passed
        } else {
            status = isChecked ? 'completed' : 'pending'; // Set status based on checkbox state
        }

        // AJAX request to update the task status
        $.ajax({
            url: `/nurse/patient/${patientId}/tasks/${taskId}/status`, // Update the URL as needed
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token for security
            },
            data: { status: status }, // Send the new status
            success: function(response) {
                if (response.success) {
                    alert('Task status updated successfully.');
                } else {
                    alert('Failed to update task status.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                alert('Failed to update task status. Please try again.');
            }
        });
    });
});

    // Function to open the task details modal
    function openTaskDetailsModal(taskId, taskData) {
        const modal = $('#taskDetailsModal');
        
        // Set the task ID as a data attribute on the delete button
        modal.find('#deleteTaskButton').data('task-id', taskId);
        
        // Set other task details in the modal
        modal.find('#modalTaskTitle').text(taskData.title);
        modal.find('#modalTaskDescription').text(taskData.description);
        // ... set other task details ...
        
        modal.modal('show');
    }

    // Handle delete button click
    $('#deleteTaskButton').on('click', function() {
        const taskId = $(this).data('task-id');
        console.log('Deleting task:', taskId); // Debug log
        
        $.ajax({
            url: `/nurse/tasks/${taskId}/delete`,
            type: 'DELETE',
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#taskDetailsModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Failed to delete task.');
                    }
            }
        }); 
    });

    // Example usage: Open modal with task details
    $('.view-task').on('click', function() {
        const taskId = $(this).data('task-id');
        const taskData = {
            title: $(this).data('title'),
            description: $(this).data('description'),
            // ... other task data ...
        };
        openTaskDetailsModal(taskId, taskData);
    });
</script>

<script>
$(document).ready(function() {
    // Handle Edit Button Click in Task Details Modal
    $('#editTaskButton').on('click', function() {
        const taskId = $(this).closest('.modal').find('.delete-task').data('task-id');
        
        // Fetch task details
        $.ajax({
            url: `/nurse/tasks/${taskId}/edit`,
            type: 'GET',
            success: function(task) {
                // Populate edit form with task details
                $('#edit_task_id').val(task.id);
                $('#edit_title').val(task.title);
                $('#edit_description').val(task.description);
                $('#edit_priority').val(task.priority);
                $('#edit_due_date').val(task.due_date.slice(0, 16)); // Format datetime-local

                // Close task details modal and open edit modal
                $('#taskDetailsModal').modal('hide');
                $('#editTaskModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Failed to load task details', 'error');
            }
        });
    });

    // Handle Save Changes Button Click
    $('#saveEditButton').on('click', function() {
        const taskId = $('#edit_task_id').val();
        const formData = {
            title: $('#edit_title').val(),
            description: $('#edit_description').val(),
            priority: $('#edit_priority').val(),
            due_date: $('#edit_due_date').val(),
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };

        $.ajax({
            url: `/nurse/tasks/${taskId}`,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Task updated successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editTaskModal').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', 'Failed to update task', 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Failed to update task', 'error');
            }
        });
    });
});
</script>

@endsection


