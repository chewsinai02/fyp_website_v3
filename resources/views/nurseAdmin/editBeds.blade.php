<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="modal fade" id="editBedModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Bed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="availableBedOptions">
          <div class="mb-3">
            <label class="form-label">What do you want to do?</label>
            <select class="form-select" id="availableActionSelect" onchange="handleActionChange()">
              <option value="assignPatient" selected>Assign to Patient</option>
              <option value="setToMaintenance">Set to Maintenance</option>
            </select>
          </div>

          <div id="assignPatientSection">
            <div class="mb-3">
              <label class="form-label fw-medium">Select Patient</label>
              <select class="form-select" id="patientSelect" style="width:100%">
                <option value="">Search patient details...</option>
                @foreach ($patients as $patient)
                  <option value="{{ $patient->id }}" 
                          data-name="{{ $patient->name }}"
                          data-ic="{{ $patient->ic_number }}"
                          data-gender="{{ $patient->gender }}"
                          data-blood="{{ $patient->blood_type }}"
                          data-contact="{{ $patient->contact_number }}"
                          data-email="{{ $patient->email }}"
                          data-address="{{ $patient->address }}"
                          data-emergency="{{ $patient->emergency_contact }}">
                    {{ $patient->name }}
                    {{ $patient->ic_number }}
                    {{ $patient->gender }}
                    {{ $patient->blood_type }}
                    {{ $patient->contact_number }}
                    {{ $patient->email }}
                    {{ $patient->address }}
                    {{ $patient->emergency_contact }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div id="maintenanceSection" style="display: none;">
            <div class="mb-3">
              <label class="form-label fw-medium">Select Status</label>
              <select class="form-select" id="maintenanceStatusSelect">
                <option value="cleaning">Cleaning</option>
                <option value="repair">Under Repair</option>
                <option value="inspection">Inspection</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-medium">Notes (Optional)</label>
              <textarea class="form-control" id="maintenanceNotes" rows="3" placeholder="Enter maintenance details..."></textarea>
            </div>
          </div>
        </div>

        <div id="occupiedBedOptions" style="display: none;">
          <div class="mb-3">
            <label class="form-label">What do you want to do?</label>
            <select class="form-select" id="occupiedActionSelect">
              <option value="transferPatient">Transfer Patient</option>
              <option value="setToMaintenance">Set to Maintenance</option>
            </select>
          </div>

          <div id="transferSection">
            <div class="mb-3">
              <label class="form-label">Select Room</label>
              <select class="form-select" id="roomSelect">
                <option value="">Loading rooms...</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Select Bed</label>
              <select class="form-select" id="bedSelect" disabled>
                <option value="">Select room first</option>
              </select>
            </div>
          </div>
        </div>

        <div id="maintenanceBedOptions" style="display: none;">
            <div class="mb-3">
                <label class="form-label">What do you want to do?</label>
                <select class="form-select" id="maintenanceActionSelect">
                    <option value="setToAvailable">Set to Available</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Notes (Optional)</label>
                <textarea class="form-control" id="statusChangeNotes" rows="3" placeholder="Enter any notes about the status change..."></textarea>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="addPatientToBed()">Save Changes</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Add this before your other JavaScript code
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(document).ready(function() {
    $('#patientSelect').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: "Search for a patient...",
      allowClear: true,
      dropdownParent: $('#editBedModal'),
      templateResult: formatPatient,
      templateSelection: formatPatientSelection
    });
  });

  function editBedStatus(bedId, status) {
    console.log('Editing bed:', bedId, 'Current status:', status); // Debug log
    
    currentBedId = bedId;
    currentBedStatus = status;

    const availableOptions = document.getElementById('availableBedOptions');
    const occupiedOptions = document.getElementById('occupiedBedOptions');
    const maintenanceOptions = document.getElementById('maintenanceBedOptions');

    // Hide all options first
    availableOptions.style.display = 'none';
    occupiedOptions.style.display = 'none';
    maintenanceOptions.style.display = 'none';

    // Show appropriate options based on status
    if (status === 'available') {
        availableOptions.style.display = 'block';
        $('#availableActionSelect').val('assignPatient');
        handleActionChange();
    } else if (status === 'occupied') {
        occupiedOptions.style.display = 'block';
        $('#occupiedActionSelect').val('transferPatient');
        $('#transferSection').show();
        loadAvailableRooms();
    } else if (['maintenance', 'cleaning', 'repair', 'inspection'].includes(status)) {
        // Show maintenance options for any maintenance-related status
        maintenanceOptions.style.display = 'block';
        $('#maintenanceActionSelect').val('setToAvailable');
        
        // Set appropriate default notes based on status
        let defaultNotes = '';
        switch(status) {
            case 'cleaning':
                defaultNotes = 'Cleaning completed';
                break;
            case 'repair':
                defaultNotes = 'Repairs completed';
                break;
            case 'inspection':
                defaultNotes = 'Inspection completed';
                break;
            default:
                defaultNotes = '';
        }
        $('#statusChangeNotes').val(defaultNotes);
    }

    // Show the modal
    $('#editBedModal').modal('show');
  }

  // Function to load available rooms and beds dynamically
  function loadAvailableRooms() {
    $.ajax({
      url: '/api/rooms-and-beds', // Replace with your API endpoint
      type: 'GET',
      success: function(response) {
        $('#roomSelect').empty().append('<option value="">Select a room</option>');
        $.each(response.rooms, function(index, room) {
          $('#roomSelect').append('<option value="' + room.id + '">' + room.name + '</option>');
        });

        $('#bedSelect').empty().append('<option value="">Select a room first</option>').prop('disabled', true);

        $('#roomSelect').on('change', function() {
          var selectedRoomId = $(this).val();
          $('#bedSelect').prop('disabled', false).empty().append('<option value="">Select a bed</option>');

          $.each(response.beds[selectedRoomId], function(index, bed) {
            $('#bedSelect').append('<option value="' + bed.id + '">' + bed.number + '</option>');
          });
        });
      },
      error: function(error) {
        console.error('Error loading rooms and beds:', error);
      }
    });
  }

  // Function to format the dropdown options
  function formatPatient(patient) {
    if (!patient.id) {
      return patient.text; // Return the text if no ID
    }
    return $('<div class="patient-option">' +
      '<div class="patient-info">' +
        '<span class="fw-bold">' + $(patient.element).data('name') + '</span><br>' +
        '<span class="text-muted">IC: ' + $(patient.element).data('ic') + '</span><br>' +
      '</div>' +
      '<div class="patient-details">' +
        '<span class="badge bg-soft-info text-black">' + $(patient.element).data('gender') + '</span>' +
        '<span class="badge bg-soft-danger text-black">' + $(patient.element).data('blood') + '</span><br>' +
        '<span class="text-muted">📞 ' + $(patient.element).data('contact') + '</span><br>' +
        '<span class="text-muted">✉️ ' + $(patient.element).data('email') + '</span><br>' +
      '</div>' +
      '<div class="patient-address">' +
        '<span class="text-muted">🏠 ' + $(patient.element).data('address') + '</span><br>' +
        '<span class="text-muted">🚑 ' + $(patient.element).data('emergency') + '</span><br>' +
      '</div>' +
    '</div>');
  }

  // Function to format the selected option
  function formatPatientSelection(patient) {
    return patient.text; // Return the text for the selected option
  }

    function addPatientToBed() {
        console.log('Button was clicked');
        console.log('saveBedChanges function called');
    
        // Get the selected patient ID and bed ID
        const patientId = $('#patientSelect').val();
        const bedId = currentBedId;

        console.log('Selected Patient ID:', patientId);
        console.log('Current Bed ID:', bedId);

        // Validate selections
        if (!patientId || !bedId) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select both a patient and a bed.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we update the bed assignment.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Make the AJAX call
        $.ajax({
            url: "{{ route('manageBed') }}",
            type: 'POST',
            data: {
                id: bedId,
                patient_id: patientId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log('Success:', response);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Bed updated successfully!',
                        confirmButtonColor: '#28a745',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#editBedModal').modal('hide');
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Operation Failed',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Error details:', {
                    status: xhr.status,
                    responseText: xhr.responseText,
                    error: error
                });
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    const errorMessage = errorResponse.errors ? 
                        Object.values(errorResponse.errors).flat().join('\n') : 
                        (errorResponse.message || 'Unknown error occurred');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update bed: ' + error,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    }
</script>

<style>
  .modal-lg {
    max-width: 800px; /* Adjust modal width */
  }

  .form-label {
    font-weight: 600; /* Bold labels */
  }

  .select2-container--bootstrap-5 .select2-selection {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    height: calc(2.25rem + 2px);
  }

  .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: 2.25; /* Center text vertically */
  }

  .select2-results__option {
    padding: 10px 15px;
  }

  .select2-results__option--highlighted {
    background-color: #e9ecef;
  }

  .badge {
    font-size: 0.85rem;
  }

  .patient-card {
    border-radius: 8px;
    transition: background-color 0.2s ease;
  }

  .patient-card:hover {
    background-color: #f8f9fa;
  }

  .patient-option {
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
  }

  .patient-option:hover {
    background-color: #f8f9fa;
  }

  .patient-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .patient-details {
    margin-top: 5px;
  }

  .patient-address {
    margin-top: 5px;
  }

  /* SweetAlert2 Custom Styles */
  .swal2-popup {
    font-size: 1rem;
    border-radius: 0.5rem;
  }

  .swal2-title {
    font-size: 1.5rem;
    font-weight: 600;
  }

  .swal2-content {
    font-size: 1rem;
  }

  .swal2-confirm {
    padding: 0.5rem 1.5rem !important;
    font-size: 1rem !important;
  }

  .swal2-cancel {
    padding: 0.5rem 1.5rem !important;
    font-size: 1rem !important;
  }

  #maintenanceSection {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 10px;
  }

  #maintenanceStatusSelect {
    background-color: white;
  }

  #maintenanceNotes {
    resize: vertical;
    min-height: 80px;
  }
</style>