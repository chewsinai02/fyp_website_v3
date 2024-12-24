@extends('nurse.layout')
@section('title', 'Patient Tasks')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="text-gradient">Tasks for {{ $patient->name }}</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal" aria-label="Add new task">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Task
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
            <div>
                <h6 class="header-title">Description Status</h6>
                <span class="badge bg-success">Low</span>
                <span class="badge bg-warning">Medium</span>
                <span class="badge bg-orange">High</span>
                <span class="badge bg-danger">Urgent</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="header-title">{{ $date->format('F Y') }}</h4>
                <div class="nav-buttons">
                    <button class="btn btn-sm btn-outline-primary" id="prev" aria-label="Previous month">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary mx-2" id="today" aria-label="Go to today">Today</button>
                    <button class="btn btn-sm btn-outline-primary" id="next" aria-label="Next month">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
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
                        data-date="{{ $currentDate->format('Y-m-d') }}"
                        role="button"
                        tabindex="0"
                        aria-label="{{ $currentDate->format('F j, Y') }}">
                        <div class="calendar-date">
                            <span>{{ $currentDate->day }}</span>
                        </div>
                        <div class="task-container">
                            @foreach($tasks as $task)
                                @if($task->due_date->format('Y-m-d') === $currentDate->format('Y-m-d'))
                                    <div class="task-item badge bg-{{ getPriorityColor($task->priority) }} w-100 mb-1 view-task" 
                                         data-task-id="{{ $task->id }}"
                                         role="button"
                                         tabindex="0"
                                         aria-label="View task: {{ $task->title }}">
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
                                               aria-label="Mark task as {{ $task->status === 'completed' ? 'incomplete' : 'complete' }}"
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
                                                data-task-id="{{ $task->id }}"
                                                aria-label="View task details">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-task" 
                                                data-task-id="{{ $task->id }}"
                                                aria-label="Delete task">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="location.reload()"></button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title</label>
                        <input type="text" 
                               class="form-control" 
                               id="title" 
                               name="title" 
                               required 
                               aria-label="Task title"
                               placeholder="Enter task title">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  aria-label="Task description"
                                  placeholder="Enter task description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                    </div>

                    <button type="submit" class="btn btn-primary" id="addTaskButton">Create Task</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="location.reload()"></button>
            </div>
            <div class="modal-body">
                <div class="schedule-info">
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
                    <i class="bi bi-arrow-repeat"></i> Week
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" id="repeatMonthlyButton">
                    <i class="bi bi-arrow-repeat"></i> Month
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="location.reload()"></button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="location.reload()">Cancel</button>
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

.animated {
    animation-duration: 0.5s;
    animation-fill-mode: both;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translate3d(0, -20px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.fadeInDown {
    animation-name: fadeInDown;
}

.swal2-popup {
    border-radius: 15px !important;
}

.swal2-title {
    font-weight: 600 !important;
}

.badge {
    padding: 0.5em 0.8em;
    font-weight: 500;
}

.task-container {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Add focus styles for better accessibility */
button:focus,
input:focus,
textarea:focus,
select:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
}

/* Accessibility focus styles */
.calendar-table td:focus,
.task-item:focus {
    outline: 2px solid #0d6efd;
    outline-offset: -2px;
    position: relative;
    z-index: 1;
}

/* Interactive elements */
.calendar-table td,
.task-item {
    cursor: pointer;
    transition: all 0.2s ease;
}

.calendar-table td:hover {
    background-color: rgba(13, 110, 253, 0.1);
}

.task-item:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}

/* Selected state */
.selected-date {
    background-color: rgba(13, 110, 253, 0.1);
    font-weight: 500;
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
    // Add click and keyboard handlers for calendar cells
    document.querySelectorAll('.calendar-table td').forEach(cell => {
        // Handle click events
        cell.addEventListener('click', handleDateSelection);
        
        // Handle keyboard events for accessibility
        cell.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleDateSelection.call(this);
            }
        });
    });

    // Handle task item events
    document.querySelectorAll('.task-item').forEach(task => {
        // Handle click events
        task.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const taskId = $(this).data('task-id');
            if (!taskId) return;
            
            // Show loading state on the clicked element
            $(this).css('opacity', '0.7');
            
            viewTaskDetails(taskId, () => {
                // Reset opacity after loading
                $(this).css('opacity', '1');
            });
        });

        // Handle keyboard events
        task.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                e.stopPropagation();
                handleTaskClick(this);
            }
        });
    });

    function handleDateSelection() {
        const selectedDate = this.dataset.date;
        if (!selectedDate) return;

        // Remove previous selection
        document.querySelectorAll('.calendar-table td').forEach(td => {
            td.classList.remove('selected-date');
            td.setAttribute('aria-selected', 'false');
        });

        // Add new selection
        this.classList.add('selected-date');
        this.setAttribute('aria-selected', 'true');

        // Format date for display
        const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });

        // Update task list
        updateTaskList(selectedDate, formattedDate);
    }

    function handleTaskClick(taskElement) {
        const taskId = taskElement.dataset.taskId;
        if (!taskId) return;

        // Show loading state
        taskElement.style.opacity = '0.7';

        // Trigger view task modal
        viewTaskDetails(taskId, () => {
            taskElement.style.opacity = '1';
        });
    }

    function updateTaskList(selectedDate, formattedDate) {
        // Update header
        document.getElementById('taskListDate').textContent = `Tasks for ${formattedDate}`;

        // Update task visibility
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

        // Update "No tasks" message
        const noTasksRow = document.querySelector('.no-tasks');
        if (noTasksRow) {
            if (!hasVisibleTasks) {
                noTasksRow.style.display = '';
                noTasksRow.querySelector('td').textContent = `No tasks for ${formattedDate}`;
            } else {
                noTasksRow.style.display = 'none';
            }
        }
    }

    function viewTaskDetails(taskId, callback) {
        $.ajax({
            url: `/nurse/tasks/${taskId}/details`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                // Show loading state
                Swal.fire({
                    title: 'Loading...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.close();
                
                if (response && response.task) {
                    const task = response.task;
                    
                    // Update modal content
                    $('#taskDetailsModal .modal-title').text(task.title);
                    $('#modalTaskTitle').text(task.title);
                    $('#modalTaskDescription').text(task.description || 'No description provided');
                    
                    // Update Priority with badge
                    const priorityClass = getPriorityBadgeClass(task.priority);
                    $('#modalTaskPriority').html(`
                        <span class="badge bg-${priorityClass}">
                            ${capitalizeFirst(task.priority)}
                        </span>
                    `);
                    
                    // Update Status with badge
                    const statusClass = getStatusBadgeClass(task.status);
                    $('#modalTaskStatus').html(`
                        <span class="badge bg-${statusClass}">
                            ${capitalizeFirst(task.status)}
                        </span>
                    `);
                    
                    // Format and set due date
                    const dueDate = moment(task.due_date).format('MMMM D, YYYY h:mm A');
                    $('#modalTaskDueDate').text(dueDate);

                    // Store task ID for other operations
                    $('#deleteTaskButton').data('task-id', task.id);
                    $('#editTaskButton').data('task-id', task.id);
                    $('#repeatWeeklyButton').data('task-id', task.id);
                    $('#repeatMonthlyButton').data('task-id', task.id);

                    // Show the modal
                    $('#taskDetailsModal').modal('show');
                } else {
                    throw new Error('Invalid response format');
                }
                
                // Call the callback function if provided
                if (typeof callback === 'function') {
                    callback();
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('Error loading task details:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load task details. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
                
                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    }

    // Add these helper functions if not already present
    function getPriorityBadgeClass(priority) {
        const classes = {
            'low': 'success',
            'medium': 'warning',
            'high': 'orange',
            'urgent': 'danger'
        };
        return classes[priority?.toLowerCase()] || 'secondary';
    }

    function getStatusBadgeClass(status) {
        const classes = {
            'completed': 'success',
            'pending': 'warning',
            'passed': 'danger',
            'cancelled': 'secondary'
        };
        return classes[status?.toLowerCase()] || 'secondary';
    }

    function capitalizeFirst(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // View Task Details
    $(document).on('click', '.view-task', function() {
        const taskId = $(this).data('task-id');
        const patientId = '{{ $patient->id }}';
        
        console.log('Loading task details:', { taskId, patientId }); // Debug log

        // Show loading state
        $('#taskDetailsModal').modal('hide');
        
        $.ajax({
            url: `/nurse/tasks/${taskId}/details`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Task details response:', response); // Debug log
                
                if (response.success && response.task) {
                    const task = response.task; // Get the task object from response
                    
                    // Update modal content
                    $('.modal-title').text(task.title);
                    $('#modalTaskTitle').text(task.title);
                    $('#modalTaskDescription').text(task.description || 'No description provided');
                    
                    // Update Priority with badge
                    const priorityClass = getPriorityBadgeClass(task.priority);
                    $('#modalTaskPriority').html(`
                        <span class="badge bg-${priorityClass}">
                            ${capitalizeFirst(task.priority)}
                        </span>
                    `);
                    
                    // Update Status with badge
                    const statusClass = getStatusBadgeClass(task.status);
                    $('#modalTaskStatus').html(`
                        <span class="badge bg-${statusClass}">
                            ${capitalizeFirst(task.status)}
                        </span>
                    `);
                    
                    // Format and set due date using moment.js
                    const dueDate = moment(task.due_date).format('MMMM D, YYYY h:mm A');
                    $('#modalTaskDueDate').text(dueDate);

                    // Store task ID for other operations
                    $('#deleteTaskButton').data('task-id', task.id);
                    $('#editTaskButton').data('task-id', task.id);
                    $('#repeatWeeklyButton').data('task-id', task.id);
                    $('#repeatMonthlyButton').data('task-id', task.id);

                    // Show the modal
                    $('#taskDetailsModal').modal('show');
                } else {
                    Swal.fire('Error', 'Invalid task data received', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading task details:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load task details. Please try again.'
                });
            }
        });
    });

    // Helper functions
    function getPriorityBadgeClass(priority) {
        const classes = {
            'low': 'success',
            'medium': 'warning',
            'high': 'orange',
            'urgent': 'danger'
        };
        return classes[priority?.toLowerCase()] || 'secondary';
    }

    function getStatusBadgeClass(status) {
        const classes = {
            'completed': 'success',
            'pending': 'warning',
            'passed': 'danger',
            'cancelled': 'secondary'
        };
        return classes[status?.toLowerCase()] || 'secondary';
    }

    function capitalizeFirst(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }
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
$(document).ready(function() {
    $('#addTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        const patientId = '{{ $patient->id }}';
        const formData = {
            title: $('#title').val(),
            description: $('#description').val(),
            priority: $('#priority').val(),
            due_date: $('#due_date').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: `/nurse/patient/${patientId}/tasks`,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Close the modal
                    $('#addTaskModal').modal('hide');
                    
                    // Show success alert
                    Swal.fire({
                        title: 'Success!',
                        text: 'Task created successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'animated fadeInDown'
                        }
                    }).then(() => {
                        // Clear the form
                        $('#addTaskForm')[0].reset();
                        // Reload the page
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Failed to create task',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error details:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });

                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to create task. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Handle checkbox status change
    $(document).on('change', '.task-status-checkbox', function() {
        const taskId = $(this).data('task-id');
        const isChecked = $(this).is(':checked');
        const status = isChecked ? 'completed' : 'pending';

        $.ajax({
            url: `/nurse/tasks/${taskId}/status`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { status: status },
            success: function(response) {
                if (response.success) {
                    // Show success alert
                    Swal.fire({
                        title: 'Success!',
                        text: `Task ${status === 'completed' ? 'completed' : 'marked as pending'}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'animated fadeInDown'
                        }
                    }).then(() => {
                        // Reload the page to update the UI
                        location.reload();
                    });
                } else {
                    // Show error alert
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Failed to update task status',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    }).then(() => {
                        // Revert checkbox state if there was an error
                        $(this).prop('checked', !isChecked);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error details:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });

                // Show error alert
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update task status. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                }).then(() => {
                    // Revert checkbox state if there was an error
                    $(this).prop('checked', !isChecked);
                });
            }
        });
    });
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
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Populate edit form
                $('#edit_task_id').val(response.id);
                $('#edit_title').val(response.title);
                $('#edit_description').val(response.description);
                $('#edit_priority').val(response.priority);
                
                // Adjust the due_date for local timezone
                const dueDate = new Date(response.due_date);
                const offset = dueDate.getTimezoneOffset();
                dueDate.setMinutes(dueDate.getMinutes() - offset);
                const formattedDate = dueDate.toISOString().slice(0, 16);
                $('#edit_due_date').val(formattedDate);

                // Close details modal and open edit modal
                $('#taskDetailsModal').modal('hide');
                $('#editTaskModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching task:', error);
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

<script>
$(document).ready(function() {
    // Handle Repeat Weekly Button Click
    $('#repeatWeeklyButton').on('click', function() {
        const taskId = $(this).closest('.modal').find('.delete-task').data('task-id');

        Swal.fire({
            title: 'Schedule Task',
            text: 'Create the same task for the next 6 days(7 days total)?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, schedule it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/nurse/tasks/${taskId}/repeat-weekly`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Scheduled!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to schedule task', 'error');
                    }
                });
            }
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Handle Repeat Monthly Button Click
    $('#repeatMonthlyButton').on('click', function() {
        const taskId = $(this).closest('.modal').find('.delete-task').data('task-id');

        Swal.fire({
            title: 'Create Monthly Tasks?',
            text: 'This will create the same task for each day over the next 30 days(31 days total)',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, create them',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/nurse/tasks/${taskId}/repeat-monthly`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to create monthly tasks', 'error');
                    }
                });
            }
        });
    });
});
</script>

@endsection

