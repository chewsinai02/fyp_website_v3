<style>
    .select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
    padding: 0.375rem 0.75rem;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 0;
        line-height: 1.5;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #dee2e6;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
    }

    .select2-container--bootstrap-5 .select2-results__option {
        padding: 0.5rem 0.75rem;
    }

    .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
        padding: 0.375rem 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
</style>

<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="scheduleForm" action="{{ route('nurseadmin.addSchedule') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Nurse</label>
                        <select class="form-select" name="nurse_id" id="nurse_id" required>
                            @foreach(\App\Models\User::where('role', 'nurse')->get() as $nurse)
                                <option value="{{ $nurse->id }}">{{ $nurse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" id="date" required 
                               min="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Shift</label>
                        <select class="form-select" name="shift" id="shift" required>
                            <option value="morning">Morning (7AM - 3PM)</option>
                            <option value="afternoon">Afternoon (3PM - 11PM)</option>
                            <option value="night">Night (11PM - 7AM)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room</label>
                        <select class="form-select select2" name="room_id" id="room_id" required>
                            @foreach(\App\Models\Room::all() as $room)
                                <option value="{{ $room->id }}">
                                    Room {{ $room->room_number }} - {{ ucfirst($room->type) }} 
                                    ({{ $room->available_beds }} beds available)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveButton">
                        <span class="d-flex align-items-center">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" id="saveSpinner"></span>
                            Save Changes
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Search for a room...',
        allowClear: true,
        dropdownParent: $('#scheduleModal'),
        templateResult: formatRoom,
        templateSelection: formatRoom
    });

    // Form submission handling
    $('#scheduleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get the button and spinner
        const saveButton = $('#saveButton');
        const saveSpinner = $('#saveSpinner');
        
        // Validate form fields
        const nurse_id = $('#nurse_id').val();
        const date = $('#date').val();
        const shift = $('#shift').val();
        const room_id = $('#room_id').val();

        if (!nurse_id || !date || !shift || !room_id) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // Disable button and show spinner
        saveButton.prop('disabled', true);
        saveSpinner.removeClass('d-none');
        
        const formData = new FormData(this);

        // Show processing alert
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we save your changes.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        })
        .done(function(response) {
            // Close the processing alert
            Swal.close();
            
            if (response.success) {
                // Show success message with details
                Swal.fire({
                    icon: 'success',
                    title: 'Schedule Updated Successfully!',
                    html: `
                        <div class="text-start">
                            <p>${response.message}</p>
                            <p><strong>Details:</strong></p>
                            <ul>
                                <li>Nurse: ${$('#nurse_id option:selected').text()}</li>
                                <li>Date: ${new Date(date).toLocaleDateString()}</li>
                                <li>Shift: ${shift.charAt(0).toUpperCase() + shift.slice(1)}</li>
                                <li>Room: ${$('#room_id option:selected').text()}</li>
                            </ul>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    // Reset form and close modal
                    $('#scheduleForm')[0].reset();
                    $('#scheduleModal').modal('hide');
                    // Reload page or update schedule display
                    window.location.reload();
                });
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Update Schedule',
                    text: response.message || 'An error occurred while saving changes.',
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .fail(function(xhr) {
            // Close the processing alert
            Swal.close();
            
            let errorMessage = 'Failed to save changes. Please try again.';
            let errorDetails = [];
            
            // Handle validation errors
            if (xhr.status === 422 && xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    errorDetails = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errorDetails.join('<br>');
                } else if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
            }
            
            // Show detailed error message
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: `
                    <div class="text-start">
                        <p>${errorMessage}</p>
                        ${errorDetails.length ? `
                            <p><strong>Please check the following:</strong></p>
                            <ul>
                                ${errorDetails.map(error => `<li>${error}</li>`).join('')}
                            </ul>
                        ` : ''}
                    </div>
                `,
                confirmButtonColor: '#dc3545'
            });
        })
        .always(function() {
            // Re-enable button and hide spinner
            saveButton.prop('disabled', false);
            saveSpinner.addClass('d-none');
        });
    });

    // Reset form when modal is closed
    $('#scheduleModal').on('hidden.bs.modal', function() {
        $('#scheduleForm')[0].reset();
        $('#saveButton').prop('disabled', false);
        $('#saveSpinner').addClass('d-none');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    });
});

// Custom formatting for room options
function formatRoom(room) {
    if (!room.id) return room.text;
    
    let roomData = $(room.element).data();
    let html = `<div class="d-flex justify-content-between align-items-center">
        <div>
            <strong>Room ${room.text.split(' - ')[0]}</strong>
            <small class="text-muted ms-2">${room.text.split(' - ')[1]}</small>
        </div>
        <span class="badge bg-${roomData.availableSlots > 0 ? 'success' : 'warning'} ms-2">
            ${roomData.currentNurses}/${roomData.maxNurses} nurses
        </span>
    </div>`;
    
    return $(html);
}

// Check existing assignment function
function checkExistingAssignment(nurseId) {
    if (!nurseId) return;

    // Clear and disable room selection while checking
    const roomSelect = $('#room_id');
    roomSelect.empty().append('<option value="">Loading rooms...</option>').prop('disabled', true);

    // Show current assignment if any
    fetch(`/nurseadmin/nurses/${nurseId}/current-assignment`)
        .then(response => response.json())
        .then(data => {
            const assignmentDiv = document.getElementById('current_assignment');
            if (data.current_assignment) {
                assignmentDiv.innerHTML = `Current Assignment: Room ${data.current_assignment.room_number}`;
            } else {
                assignmentDiv.innerHTML = '';
            }
        });

    // Get available rooms
    fetch('/nurseadmin/available-rooms')
        .then(response => response.json())
        .then(rooms => {
            roomSelect.empty().append('<option value="">Search for a room...</option>');
            rooms.forEach(room => {
                let option = new Option(
                    `Room ${room.room_number} - ${room.type.charAt(0).toUpperCase() + room.type.slice(1)}`,
                    room.id,
                    false,
                    false
                );
                $(option).data('status', room.status);
                $(option).data('availableBeds', room.available_beds);
                roomSelect.append(option);
            });
            roomSelect.prop('disabled', false).trigger('change');
        });
}

// Test function to check if SweetAlert2 is working
function testSweetAlert() {
    Swal.fire({
        title: 'Test Alert',
        text: 'SweetAlert2 is working!',
        icon: 'success'
    });
}
</script>
@endpush 