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
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
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
        dropdownParent: $('#scheduleModal'), // This ensures the dropdown works in modal
        templateResult: formatRoom,
        templateSelection: formatRoom
    });
});

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}'
    });
@endif

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

// Update the existing checkExistingAssignment function
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

// Form submission handling
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Disable submit button and show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();
            
            // Show success message and redirect
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Redirect to schedule list
                window.location.href = '{{ route('nurseadmin.scheduleList') }}';
            });
        } else {
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An unexpected error occurred. Please try again.',
            confirmButtonText: 'OK'
        });
    });
});
</script>
@endpush 