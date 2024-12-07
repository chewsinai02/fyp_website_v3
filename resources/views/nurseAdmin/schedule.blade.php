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
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="refreshPage()"></button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include Moment.js (if used for date formatting) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Search for a room...',
        allowClear: true,
        dropdownParent: $('#scheduleModal')
    });

    // Form submission handling with SweetAlert2
    $('#scheduleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const nurse = $('#nurse_id option:selected').text();
        const date = $('#date').val();
        const shift = $('#shift').val();
        const room = $('#room_id option:selected').text();

        // Show confirmation dialog
        Swal.fire({
            title: 'Confirm Schedule Assignment',
            html: `
                <div class="text-start">
                    <p>Are you sure you want to assign:</p>
                    <ul>
                        <li><strong>Nurse:</strong> ${nurse}</li>
                        <li><strong>Date:</strong> ${new Date(date).toLocaleDateString()}</li>
                        <li><strong>Shift:</strong> ${shift.charAt(0).toUpperCase() + shift.slice(1)}</li>
                        <li><strong>Room:</strong> ${room}</li>
                    </ul>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, assign!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Assigning Schedule...',
                    html: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form data via AJAX
                const formData = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: response.title,
                                html: `
                                    <div class="text-start">
                                        <p>${response.message}</p>
                                        <p><strong>Schedule Details:</strong></p>
                                        <ul>
                                            <li>Nurse: ${response.details.nurse}</li>
                                            <li>Date: ${response.details.date}</li>
                                            <li>Shift: ${response.details.shift}</li>
                                            <li>Room: ${response.details.room}</li>
                                        </ul>
                                    </div>
                                `,
                                icon: response.icon,
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                $('#scheduleModal').modal('hide');
                                window.location.href = response.data.redirect;
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            title: response.title || 'Error!',
                            text: response.message || 'An error occurred while creating the schedule.',
                            icon: response.icon || 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });

        // Delete schedule confirmation
        $('.delete-schedule').on('click', function(e) {
            e.preventDefault();
            const deleteUrl = $(this).data('url');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This schedule will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Schedule has been deleted.',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete schedule.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });
    });
});
</script>
@endpush 