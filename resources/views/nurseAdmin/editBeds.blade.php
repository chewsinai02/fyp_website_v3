<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="modal fade" id="editBedModal" tabindex="-1" aria-labelledby="editBedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editBedModalLabel">Manage Bed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Action Selection -->
        <div id="availableBedOptions">
          <div class="mb-3">
            <label for="availableActionSelect" class="form-label">What do you want to do?</label>
            <select class="form-select" id="availableActionSelect" onchange="handleActionChange()" aria-label="Select action">
              <option value="assignPatient" selected>Assign to Patient</option>
              <option value="setToMaintenance">Set to Maintenance</option>
            </select>
          </div>

          <!-- Patient Selection -->
          <div id="assignPatientSection">
            <div class="mb-3">
              <label for="patientSelect" class="form-label fw-medium">Select Patient</label>
              <select class="form-select" id="patientSelect" style="width:100%" aria-label="Select patient">
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
                    {{ $patient->name }} - {{ $patient->ic_number }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Patient Details Display -->
            <div id="patientDetailsSection" class="mt-3" style="display: none;">
              <h6 class="mb-3">Patient Details</h6>
              <div class="row">
                <div class="col-md-6">
                  <p><strong>Name:</strong> <span id="patientName"></span></p>
                  <p><strong>IC Number:</strong> <span id="patientIC"></span></p>
                  <p><strong>Gender:</strong> <span id="patientGender"></span></p>
                  <p><strong>Blood Type:</strong> <span id="patientBloodType"></span></p>
                </div>
                <div class="col-md-6">
                  <p><strong>Contact:</strong> <span id="patientContact"></span></p>
                  <p><strong>Email:</strong> <span id="patientEmail"></span></p>
                  <p><strong>Address:</strong> <span id="patientAddress"></span></p>
                  <p><strong>Emergency Contact:</strong> <span id="patientEmergencyContact"></span></p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Maintenance Section -->
        <div id="maintenanceSection" style="display: none;">
          <div class="mb-3">
            <label for="status" class="form-label fw-medium">Make Bed Available</label>
            <select class="form-select" id="status" aria-label="Select bed status">
              <option value="available">Yes</option>
              <option value="maintenance">No (Maintenance)</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="maintenanceNotes" class="form-label fw-medium">Notes (Optional)</label>
            <textarea class="form-control" id="maintenanceNotes" rows="3"
              placeholder="Enter maintenance details..." aria-label="Maintenance notes"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cancel">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveChangesButton" onclick="saveChangesButton()" aria-label="Save changes">
          Save Changes
        </button>
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

  $(document).ready(function () {
    $('#patientSelect').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: "Search for a patient...",
      allowClear: true,
      dropdownParent: $('#editBedModal'),
      templateResult: formatPatient,
      templateSelection: formatPatientSelection
    }).on('change', function() {
      const patientId = $(this).val();
      if (patientId) {
        loadPatientDetails(patientId);
      } else {
        $('#patientDetailsSection').hide();
      }
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

  function editBedStatus(bedId, status) {
    console.log('Editing bed:', bedId, 'Current status:', status); // Debug log

    currentBedId = bedId; // Store the current bed ID
    currentBedStatus = status;

    // Assuming you have a way to get the patient ID from the current bed
    const patientId = document.getElementById(`patientId_${bedId}`).value; // Get patient ID from a hidden input

    // Store the patient ID in a global variable or use it directly later
    window.currentPatientId = patientId;

    // Show appropriate options based on status
    const availableOptions = document.getElementById('availableBedOptions');
    const occupiedOptions = document.getElementById('occupiedBedOptions');
    const maintenanceOptions = document.getElementById('maintenanceBedOptions');
    const saveChangesButton = document.getElementById('saveChangesButton');

    // Hide all options first
    availableOptions.style.display = 'none';
    occupiedOptions.style.display = 'none';
    maintenanceOptions.style.display = 'none';

    if (status === 'available') {
      availableOptions.style.display = 'block';
      $('#availableActionSelect').val('assignPatient');
      handleActionChange();
      saveChangesButton.setAttribute('onclick', 'addPatientToBed()'); // Set onclick for assigning patient
    } else if (status === 'occupied') {
      occupiedOptions.style.display = 'block';
      $('#occupiedActionSelect').val('transferPatient');
      $('#transferSection').show();
      loadAvailableRooms();
      saveChangesButton.setAttribute('onclick', 'transferRoomOrBed()'); // Set onclick for transferring
    } else if (['maintenance', 'cleaning', 'repair', 'inspection'].includes(status)) {
      maintenanceOptions.style.display = 'block';
      $('#maintenanceActionSelect').val('setToAvailable');
      saveChangesButton.setAttribute('onclick', 'setToAvailable()'); // Set onclick for changing status
    }

    // Show the modal
    $('#editBedModal').modal('show');
  }

  // Function to set the bed status to available    
  function setToAvailable(bedId) {
    console.log('setToAvailable function called'); // Confirm function call
    console.log('Bed ID:', bedId); // Log the bed ID

    // Get the status change notes (if any) from the input field
    const statusChangeNotes = $('#statusChangeNotes').val();

    // Make the AJAX request
    $.ajax({
      url: '/api/beds/change-status', // Ensure this URL is correct
      type: 'POST',
      contentType: 'application/json', // Set content type to JSON
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
      },
      data: JSON.stringify({ // Send data as JSON
        bed_id: bedId,
        status: 'available',
        notes: statusChangeNotes
      }),
      success: function (response) {
        console.log('Success:', response);
        if (response.success) {
          alert('Bed status updated successfully!');
          window.location.reload(); // Refresh the page on success
        } else {
          alert('Failed to update bed status: ' + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error('Error:', error);
        alert('An error occurred: ' + xhr.responseText); // Show error message
      }
    });
  }


  // Function to load available rooms and beds dynamically
  function loadAvailableRooms() {
    $.ajax({
      url: '/api/rooms-and-beds', // Replace with your API endpoint
      type: 'GET',
      success: function (response) {
        $('#roomSelect').empty().append('<option value="">Select a room</option>');
        $.each(response.rooms, function (index, room) {
          $('#roomSelect').append('<option value="' + room.id + '">' + room.name + '</option>');
        });

        $('#bedSelect').empty().append('<option value="">Select a room first</option>').prop('disabled', true);

        $('#roomSelect').on('change', function () {
          var selectedRoomId = $(this).val();
          $('#bedSelect').prop('disabled', false).empty().append('<option value="">Select a bed</option>');

          $.each(response.beds[selectedRoomId], function (index, bed) {
            $('#bedSelect').append('<option value="' + bed.id + '">' + bed.number + '</option>');
          });
        });
      },
      error: function (error) {
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

  function saveChangesButton() {
    // Check which section is currently visible and call the appropriate function
    if ($('#availableBedOptions').is(':visible') && $('#availableActionSelect').val() === 'assignPatient') {
      addPatientToBed();
    } else if ($('#maintenanceBedOptions').is(':visible') && $('#maintenanceActionSelect').val() === 'setToAvailable') {
      setToAvailable(currentBedId);
    } else if ($('#occupiedBedOptions').is(':visible') && $('#occupiedActionSelect').val() === 'transferPatient') {
      transferRoomOrBed();
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Validation Error',
        text: 'Please select a valid action.',
        confirmButtonColor: '#3085d6'
      });
    }
  }
  function addPatientToBed() {
    console.log('Button was clicked');
    
    const patientId = $('#patientSelect').val();
    const bedId = currentBedId;

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

    $.ajax({
        url: "{{ route('manageBed') }}",
        type: 'POST',
        data: {
            id: bedId,
            patient_id: patientId,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: response.title,
                    text: response.message,
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#editBedModal').modal('hide');
                        window.location.reload();
                    }
                });
            } else if (response.status === 'error') {
                // Handle error response
                Swal.fire({
                    icon: 'error',
                    title: response.title,
                    html: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            
            if (response && response.currentAssignment) {
                // Show existing assignment error
                Swal.fire({
                    icon: 'error',
                    title: response.title,
                    html: `
                        <div class="text-start">
                            <p>${response.message}</p>
                            <p><strong>Current Assignment:</strong></p>
                            <ul>
                                <li>Room: ${response.currentAssignment.room}</li>
                                <li>Bed: ${response.currentAssignment.bed}</li>
                            </ul>
                        </div>
                    `,
                    confirmButtonColor: '#dc3545'
                });
            } else {
                // Show generic error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response?.message || 'An error occurred while updating the bed assignment.',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    });
  }

  // Assuming you have a function that loads available beds when a room is selected
  document.getElementById('roomSelect').addEventListener('change', function (e) {
    const roomId = e.target.value;
    const bedSelect = document.getElementById('bedSelect');

    if (!roomId) {
      // If no room is selected, show "Select room first" and disable the bed select
      bedSelect.innerHTML = '<option value="">Select room first</option>';
      bedSelect.disabled = true; // Disable the bed select
    } else {
      // If a room is selected, enable the bed select and show "Select a bed"
      bedSelect.innerHTML = '<option value="">Select a bed</option>';
      bedSelect.disabled = false; // Enable the bed select

      // Fetch available beds for the selected room
      fetch(`/api/rooms/${roomId}/available-beds`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          // Check if the data contains beds
          if (data.beds && data.beds.length > 0) {
            // Populate the bed select with available beds
            data.beds.forEach(bed => {
              const option = document.createElement('option');
              option.value = bed.id;
              option.textContent = `Bed ${bed.bed_number}`;
              bedSelect.appendChild(option);
            });
          } else {
            // If no beds are available, show a message
            bedSelect.innerHTML = '<option value="">No beds available</option>';
          }
        })
        .catch(error => {
          console.error('Error fetching beds:', error);
          bedSelect.innerHTML = '<option value="">Error loading beds</option>';
        });
    }
  });

  function transferRoomOrBed() {
    console.log('transferRoomOrBed function called');

    // Get the selected room ID from the room select dropdown
    const roomSelect = document.getElementById('roomSelect');
    const roomId = roomSelect.value;

    // Get the selected bed ID from the bed select dropdown
    const bedSelect = document.getElementById('bedSelect');
    const newBedId = bedSelect.value;

    const patientId = document.getElementById(`patientId_${currentBedId}`).value; // Get patient ID from a hidden input
    alert(patientId);
    // Validate the selections
    if (!patientId || !roomId || !newBedId || !currentBedId) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Information',
        text: 'Please select a patient, room, and bed to transfer.',
        confirmButtonColor: '#dc3545'
      });
      return; // Exit the function if validation fails
    }

    // Prepare the data to send to the server
    const data = {
      patient_id: patientId,
      room_id: roomId,
      bed_id: newBedId,
      current_bed_id: currentBedId,
      _token: "{{ csrf_token() }}" // Include CSRF token for security
    };

    // Make the AJAX request to transfer the patient
    $.ajax({
      url: '/api/patients/transfer', // Ensure this URL is correct
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(data),
      success: function (response) {
        console.log('Success:', response);
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'Transfer Successful',
            text: 'Patient has been transferred successfully!',
            confirmButtonColor: '#28a745'
          }).then((result) => {
            if (result.isConfirmed) {
              $('#editBedModal').modal('hide');
              window.location.reload(); // Refresh the page to see the updated data
            }
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Transfer Failed',
            text: response.message,
            confirmButtonColor: '#dc3545'
          });
        }
      },
      error: function (xhr, status, error) {
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
            text: 'Failed to transfer patient: ' + error,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
          });
        }
      }
    });
  }

  function assignPatient(bedId, patientId) {
    Swal.fire({
        title: 'Confirm Assignment',
        text: 'Are you sure you want to assign this patient to this bed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, assign patient'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/nurseadmin/manage-bed',
                method: 'POST',
                data: {
                    id: bedId,
                    patient_id: patientId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: response.title,
                        text: response.message,
                        icon: response.icon
                    }).then(() => {
                        if (response.status === 'success') {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response.currentAssignment) {
                        Swal.fire({
                            title: response.title,
                            html: `
                                <div class="text-start">
                                    <p>${response.message}</p>
                                    <p><strong>Current Assignment:</strong></p>
                                    <ul>
                                        <li>Room: ${response.currentAssignment.room}</li>
                                        <li>Bed: ${response.currentAssignment.bed}</li>
                                    </ul>
                                </div>
                            `,
                            icon: response.icon,
                            confirmButtonColor: '#d33'
                        });
                    } else {
                        Swal.fire({
                            title: response.title || 'Error',
                            text: response.message || 'An error occurred',
                            icon: response.icon || 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            });
        }
    });
  }

  // Update the loadPatientDetails function
  function loadPatientDetails(patientId) {
    if (!patientId) return;

    $.ajax({
      url: `/api/patients/${patientId}`,
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response && !response.error) {
          updatePatientDetailsUI(response);
          $('#patientDetailsSection').show();
        } else {
          console.error('Patient details error:', response.error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.error || 'Patient details not found'
          });
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading patient details:', error);
        console.error('Response:', xhr.responseText);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to load patient details. Please try again.'
        });
      }
    });
  }

  // Update the updatePatientDetailsUI function
  function updatePatientDetailsUI(patient) {
    if (!patient) return;
    
    $('#patientName').text(patient.name || 'N/A');
    $('#patientIC').text(patient.ic_number || 'N/A');
    $('#patientGender').text(patient.gender || 'N/A');
    $('#patientBloodType').text(patient.blood_type || 'N/A');
    $('#patientContact').text(patient.contact_number || 'N/A');
    $('#patientEmail').text(patient.email || 'N/A');
    $('#patientAddress').text(patient.address || 'N/A');
    $('#patientEmergencyContact').text(patient.emergency_contact || 'N/A');
  }
</script>

<style>
  .modal-lg {
    max-width: 800px;
    /* Adjust modal width */
  }

  .form-label {
    font-weight: 600;
    /* Bold labels */
  }

  .select2-container--bootstrap-5 .select2-selection {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    height: calc(2.25rem + 2px);
  }

  .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: 2.25;
    /* Center text vertically */
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