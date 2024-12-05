@extends('nurse.layout')
@section('content')
<style>
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

.table {
    border-radius: 0.5rem; /* Rounded corners for the table */
    overflow: hidden; /* Prevents overflow of rounded corners */
}

.table th {
    background-color: #f8f9fa; /* Light background for header */
    font-weight: bold; /* Bold text for headers */
}

.table td {
    vertical-align: middle; /* Center align the content */
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f2f2f2; /* Light gray for odd rows */
}

.table-hover tbody tr:hover {
    background-color: #e9ecef; /* Light hover effect */
}

.task-status-checkbox {
    cursor: pointer; /* Pointer cursor for checkboxes */
}

.badge {
    font-weight: 500; /* Slightly bolder text for badges */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table {
        font-size: 0.9rem; /* Smaller font size on mobile */
    }
}
</style>


<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-gradient">Tasks List Today: {{ Carbon\Carbon::now()->format('F jS, Y') }}</h2>
            <div class="btn-group align-items-center float-end mb-3">
                <button class="btn btn-outline-primary" onclick="filterTasks('all')">All</button>
                <button class="btn btn-outline-primary" onclick="filterTasks('pending')">Pending</button>
                <button class="btn btn-outline-primary" onclick="filterTasks('completed')">Completed</button>
                <button class="btn btn-outline-primary" onclick="filterTasks('passed')">Passed</button>
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
                    'pending' => 'warning',
                    'completed' => 'success',
                    'passed' => 'danger',
                    'cancelled' => 'danger',
                    default => 'secondary',
                };
            }
        }
    @endphp

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Status</th>
                <th>Patient</th>
                <th>Bed Number</th>
                <th>Task</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr data-task-id="{{ $task->id }}" data-status="{{ $task->status }}" data-date="{{ $task->due_date->format('Y-m-d') }}">
                    <td>
                        <input type="checkbox" 
                               class="form-check-input task-status-checkbox" 
                               data-task-id="{{ $task->id }}"
                               {{ $task->status === 'completed' ? 'checked' : '' }}>
                    </td>
                    <td>{{ $task->patient->name }}</td>
                    <td>{{ $task->patient->bed->bed_number }}</td>
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
                    <td>
                        <button type="button" class="btn btn-sm btn-info view-task" data-task-id="{{ $task->id }}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-task" data-task-id="{{ $task->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr class="no-tasks">
                    <td colspan="5" class="text-center">No tasks available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-labelledby="taskDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailsModalLabel">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="schedule-info">
                    <div class="info-group">
                        <label class="form-label fw-bold">Patient:</label>
                        <p id="taskPatient" class="mb-2"></p>
                    </div>
                    <div class="info-group">
                        <label class="form-label fw-bold">Bed Number:</label>
                        <p id="taskBedNumber" class="mb-2"></p>
                    </div>
                    <div class="info-group">
                        <label class="form-label fw-bold">Title:</label>
                        <p id="taskTitle" class="mb-2"></p>
                    </div>
                    <div class="info-group">
                        <label class="form-label fw-bold">Description:</label>
                        <p id="taskDescription" class="mb-2"></p>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Priority:</label>
                            <p id="taskPriority"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p id="taskStatus"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Due Date:</label>
                        <p id="taskDueDate"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal HTML -->
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alertModalLabel"></h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close" onclick="location.reload()"></button>
      </div>
      <div class="modal-body" id="alertModalBody"></div>
      <div class="modal-footer">
        <!-- For regular alerts -->
        <div id="alertFooter" style="display: none;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="location.reload()">Close</button>
        </div>
        <!-- For confirmations -->
        <div id="confirmFooter" style="display: none;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmYes">Yes</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include Moment.js (if used for date formatting) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
$(document).ready(function() {
    // Filter tasks based on status
    window.filterTasks = function(status) {
        const taskRows = $('tbody tr'); // Select all task rows

        taskRows.show(); // Show all tasks initially

        if (status !== 'all') {
            taskRows.each(function() {
                const taskStatus = $(this).data('status'); // Get the status from the data attribute
                if (taskStatus !== status) {
                    $(this).hide(); // Hide tasks that do not match the selected status
                }
            });
        }
    };

    // Initial filter to show all tasks
    filterTasks('all');

    // Handle view task button click
    $(document).on('click', '.view-task', function() {
        const taskId = $(this).data('task-id'); // Get the task ID from the button

        // AJAX request to fetch task details
        $.ajax({
            url: `/nurse/tasks/${taskId}/details`, // URL to fetch task details
            method: 'GET',
            success: function(data) {
                console.log('Task details:', data); // Log the entire response
                // Populate the modal with task details
                $('#taskTitle').text(data.title);
                $('#taskDescription').text(data.description);
                $('#taskPatient').text(data.patient ? data.patient.name : 'N/A');
                $('#taskBedNumber').text(data.patient && data.patient.bed ? data.patient.bed.bed_number : 'N/A');

                // Set the priority with color
                const priorityClass = getPriorityClass(data.priority); // Function to get the class based on priority
                $('#taskPriority').html(`<span class="badge ${priorityClass}">${data.priority}</span>`); // Use HTML to include the badge
                
                const statusClass = getStatusClass(data.status); // Function to get the class based on status
                $('#taskStatus').html(`<span class="badge ${statusClass}">${data.status}</span>`); // Use HTML to include the badge
                
                // Format the due date
                const dueDate = new Date(data.due_date); // Parse the date
                const formattedDueDate = dueDate.toISOString().slice(0, 10) + ' ' + dueDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); // Format to YYYY-MM-DD and time

                // Set the formatted due date
                $('#taskDueDate').text(formattedDueDate); 
                
                // Show the modal
                $('#taskDetailsModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching task details:', error);
                alert('Failed to fetch task details. Please try again.');
            }
        });
    });

    // Function to return the appropriate class based on priority
    function getPriorityClass(priority) {
        switch (priority.toLowerCase()) {
            case 'low':
                return 'bg-success'; // Green
            case 'medium':
                return 'bg-warning'; // Yellow
            case 'high':
                return 'bg-danger'; // Red
            case 'urgent':
                return 'bg-danger'; // Red (or you can create a custom class)
            default:
                return 'bg-secondary'; // Default class for unknown priority
        }
    }

    function getStatusClass(status) {
        switch (status.toLowerCase()) {
            case 'pending':
                return 'bg-warning'; // Yellow
            case 'completed':
                return 'bg-success'; // Green
            case 'passed':
                return 'bg-danger'; // Red
            case 'cancelled':
                return 'bg-danger'; // Red
            default:
                return 'bg-secondary'; // Default class for unknown status
        }
    }

    // Handle delete task button click
    $(document).on('click', '.delete-task', function() {
        const taskId = $(this).data('task-id'); // Get the task ID from the button

        showModal('Confirmation', 'Are you sure you want to delete this task?', function() {
            $.ajax({
                url: `/nurse/tasks/${taskId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showModal('Success', 'Task deleted successfully');
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    } else {
                        showModal('Error', 'Failed to delete task');
                    }
                },
                error: function() {
                    showModal('Error', 'Failed to delete task. Please try again.');
                }
            });
        });
    });

    // Handle checkbox status change
    $(document).on('change', '.task-status-checkbox', function() {
        const taskId = $(this).data('task-id');
        const isChecked = $(this).prop('checked');
        const status = isChecked ? 'completed' : 'pending';
        const checkbox = $(this);

        $.ajax({
            url: `/nurse/tasks/${taskId}/status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Update the status badge with the returned status
                    const actualStatus = response.status; // Use the status returned from the server
                    const statusBadge = checkbox.closest('tr')
                        .find('.badge:contains("Pending"), .badge:contains("Completed"), .badge:contains("Passed")');
                    
                    // Remove all possible status classes
                    statusBadge.removeClass('bg-warning bg-success bg-danger')
                        .addClass(getStatusClass(actualStatus))
                        .text(actualStatus.charAt(0).toUpperCase() + actualStatus.slice(1));
                    
                    // Update the row's data-status attribute
                    checkbox.closest('tr').attr('data-status', actualStatus);
                    
                    // If the status was changed to 'passed', uncheck the checkbox
                    if (actualStatus === 'passed') {
                        checkbox.prop('checked', false);
                    }
                    
                    // Show success message
                    showModal('Success', `Task status has been updated to ${actualStatus}`);
                } else {
                    // Revert checkbox if update failed
                    checkbox.prop('checked', !isChecked);
                    showModal('Error', 'Failed to update task status');
                }
            },
            error: function() {
                // Revert checkbox if request failed
                checkbox.prop('checked', !isChecked);
                showModal('Error', 'Failed to update task status. Please try again.');
            }
        });
    });
});
</script>

<!-- JavaScript Function -->
<script>
function showModal(title, message, yesCallback = null) {
    $('#alertModalLabel').text(title);
    $('#alertModalBody').text(message);
    
    // Show/hide appropriate footer
    if (yesCallback) {
        $('#alertFooter').hide();
        $('#confirmFooter').show();
        
        // Set up Yes button click handler
        $('#confirmYes').off('click').on('click', function() {
            $('#alertModal').modal('hide');
            yesCallback();
        });
    } else {
        $('#confirmFooter').hide();
        $('#alertFooter').show();
    }

    $('#alertModal').modal('show');
}
</script>

@endsection