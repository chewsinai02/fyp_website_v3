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
            <select class="form-select" 
                    id="availableActionSelect" 
                    name="availableAction"
                    aria-label="Select action for bed"
                    title="Select action"
                    onchange="handleActionChange()">
              <option value="assignPatient" selected>Assign to Patient</option>
              <option value="setToMaintenance">Update Bed Status</option>
              <option value="transferPatient">Transfer Patient</option>
            </select>
          </div>

          <!-- Patient Selection -->
          <div id="assignPatientSection">
            <div class="mb-3">
              <label for="patientSelect" class="form-label fw-medium">Select Patient</label>
              <select class="form-select" 
                      id="patientSelect" 
                      name="patient"
                      style="width:100%" 
                      aria-label="Select patient"
                      title="Select patient">
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
            <label for="status" class="form-label fw-medium">Select Status</label>
            <select class="form-select" 
                    id="status" 
                    name="status"
                    aria-label="Select bed status"
                    title="Select bed status">
              <option value="available">Available</option>
              <option value="occupied">Occupied</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="maintenanceNotes" class="form-label fw-medium">Notes (Optional)</label>
            <textarea class="form-control" 
                      id="maintenanceNotes" 
                      name="notes"
                      rows="3"
                      aria-label="Maintenance notes"
                      placeholder="Enter any relevant notes..."
                      title="Enter maintenance notes"></textarea>
          </div>
        </div>

        <!-- New Transfer Section -->
        <div id="transferSection" style="display: none;">
          <div class="mb-3">
            <label for="transferRoom" class="form-label fw-medium">Select Room</label>
            <select class="form-select" 
                    id="transferRoom" 
                    name="transferRoom"
                    title="Select a room for transfer"
                    aria-label="Select room for transfer"
                    onchange="loadAvailableBeds()">
              <option value="">Choose a room...</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="transferBed" class="form-label fw-medium">Select Bed</label>
            <select class="form-select" 
                    id="transferBed" 
                    name="transferBed"
                    title="Select a bed for transfer"
                    aria-label="Select bed for transfer"
                    disabled>
              <option value="">Choose a bed...</option>
            </select>
          </div>
          <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2" aria-hidden="true"></i>
            The current bed will be set to maintenance after transfer for cleaning.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" 
                class="btn btn-secondary" 
                data-bs-dismiss="modal"
                title="Cancel changes"
                aria-label="Cancel changes">
          <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
          Cancel
        </button>
        <button type="button" 
                class="btn btn-primary" 
                id="saveChangesButton"
                onclick="handleSaveChanges()"
                title="Save changes"
                aria-label="Save changes">
          <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
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
    console.log('Editing bed:', bedId, 'Current status:', status);

    currentBedId = bedId;
    currentBedStatus = status;

    // Reset all sections
    const availableOptions = document.getElementById('availableBedOptions');
    const maintenanceSection = document.getElementById('maintenanceSection');
    const transferSection = document.getElementById('transferSection');

    // Hide all sections first
    availableOptions.style.display = 'none';
    maintenanceSection.style.display = 'none';
    transferSection.style.display = 'none';

    // Show appropriate section based on status
    if (status === 'available') {
        availableOptions.style.display = 'block';
        $('#availableActionSelect').val('assignPatient');
        handleActionChange();
    } else if (status === 'occupied') {
        availableOptions.style.display = 'block';
        $('#availableActionSelect').val('transferPatient');
        handleActionChange();
    } else if (['maintenance'].includes(status)) {
        maintenanceSection.style.display = 'block';
        $('#status').val(status);
    }

    $('#editBedModal').modal('show');
  }

  // Add this new function to handle bed status updates
  function updateBedStatus() {
    const status = document.getElementById('status').value;
    const notes = document.getElementById('maintenanceNotes').value;

    console.log('Updating bed status:', {
        bed_id: currentBedId,
        status: status,
        notes: notes
    });

    fetch('/api/beds/change-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            bed_id: currentBedId,
            status: status,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            $('#editBedModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Bed status updated successfully'
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(result.message || 'Failed to update bed status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to update bed status'
        });
    });
  }

  // Function to load available rooms and beds dynamically
  function loadAvailableRooms() {
    console.log('Loading available rooms...'); // Debug log

    fetch('/api/rooms/available', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        const data = await response.json();
        console.log('Response:', data); // Debug log
        
        if (!response.ok) {
            if (response.status === 403) {
                console.error('Permission Error:', data);
                throw new Error(`Access Denied. Your role (${data.debug?.user_role}) does not have permission. Required roles: ${data.debug?.required_roles?.join(', ')}`);
            }
            throw new Error(data.message || 'Failed to load rooms');
        }
        
        const roomSelect = document.getElementById('transferRoom');
        roomSelect.innerHTML = '<option value="">Choose a room...</option>';
        
        if (data.rooms && Array.isArray(data.rooms)) {
            data.rooms.forEach(room => {
                roomSelect.innerHTML += `
                    <option value="${room.id}">Room ${room.room_number} (${room.available_beds} available beds)</option>
                `;
            });
        }
        
        // Reset bed select
        const bedSelect = document.getElementById('transferBed');
        bedSelect.innerHTML = '<option value="">Choose a bed...</option>';
        bedSelect.disabled = true;
    })
    .catch(error => {
        console.error('Error loading rooms:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
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

  function handleSaveChanges() {
    const action = document.getElementById('availableActionSelect')?.value;
    
    console.log('Handling save changes:', {
        action: action,
        bedId: currentBedId,
        status: currentBedStatus
    });

    if (currentBedStatus === 'available') {
        addPatientToBed();
    } else if (currentBedStatus === 'occupied') {
        if (action === 'transferPatient') {
            handleTransfer();
        }
    } else if (currentBedStatus === 'maintenance') {
        updateBedStatus();
    }
  }

  function addPatientToBed() {
    const patientId = document.getElementById('patientSelect').value;
    
    if (!patientId) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Please select a patient'
        });
        return;
    }

    fetch('/api/beds/change-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            bed_id: currentBedId,
            status: 'occupied',
            patient_id: patientId
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            $('#editBedModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Patient assigned successfully'
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(result.message || 'Failed to assign patient');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to assign patient'
        });
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

  function handleTransfer() {
    console.log('Starting transfer process');
    console.log('Current bed ID:', currentBedId);
    console.log('Selected room:', document.getElementById('transferRoom').value);
    console.log('Selected bed:', document.getElementById('transferBed').value);

    const newBedId = document.getElementById('transferBed').value;
    const roomId = document.getElementById('transferRoom').value;
    const bedElement = document.querySelector(`[data-bed-id="${currentBedId}"]`);
    const patientId = bedElement?.dataset?.patientId;
    
    console.log('Transfer details:', {
        patientId: patientId,
        currentBedId: currentBedId,
        newBedId: newBedId,
        roomId: roomId
    });

    if (!newBedId || !roomId || !patientId) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Please select both room and bed'
        });
        return;
    }
    
    fetch('/api/patients/transfer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            patient_id: patientId,
            room_id: roomId,
            bed_id: newBedId,
            current_bed_id: currentBedId
        })
    })
    .then(async response => {
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message || 'Failed to transfer patient');
        }
        return result;
    })
    .then(result => {
        $('#editBedModal').modal('hide');
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Patient transferred successfully'
        }).then(() => {
            location.reload();
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to transfer patient'
        });
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

  function handleActionChange() {
    const action = document.getElementById('availableActionSelect').value;
    const assignSection = document.getElementById('assignPatientSection');
    const maintenanceSection = document.getElementById('maintenanceSection');
    const transferSection = document.getElementById('transferSection');

    // Hide all sections first
    assignSection.style.display = 'none';
    maintenanceSection.style.display = 'none';
    transferSection.style.display = 'none';

    // Show the selected section
    switch(action) {
        case 'assignPatient':
            assignSection.style.display = 'block';
            break;
        case 'setToMaintenance':
            maintenanceSection.style.display = 'block';
            break;
        case 'transferPatient':
            transferSection.style.display = 'block';
            loadAvailableRooms(); // This will load available rooms
            break;
    }
  }

  function loadAvailableBeds() {
    const roomId = document.getElementById('transferRoom').value;
    const bedSelect = document.getElementById('transferBed');
    
    if (!roomId) {
        bedSelect.innerHTML = '<option value="">Choose a bed...</option>';
        bedSelect.disabled = true;
        return;
    }
    
    console.log('Loading beds for room:', roomId); // Debug log

    fetch(`/api/rooms/${roomId}/available-beds`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        const data = await response.json();
        console.log('Beds response:', data); // Debug log
        
        if (!response.ok) {
            throw new Error(data.message || 'Failed to load beds');
        }
        
        bedSelect.innerHTML = '<option value="">Choose a bed...</option>';
        
        if (data.beds && Array.isArray(data.beds)) {
            data.beds.forEach(bed => {
                bedSelect.innerHTML += `
                    <option value="${bed.id}">Bed ${bed.bed_number}</option>
                `;
            });
        }
        
        bedSelect.disabled = false;
    })
    .catch(error => {
        console.error('Error loading beds:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to load available beds'
        });
    });
  }

  // Add this at the top of your script section
  console.log('Current User Role:', '{{ auth()->user()->role }}');
  console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').content);
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

  #transferSection {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 10px;
  }

  #transferSection .form-select {
    background-color: white;
  }

  #transferSection .alert {
    margin-bottom: 0;
    margin-top: 15px;
  }

  .action-buttons {
    margin-top: 20px;
  }

  .form-label.fw-medium {
    font-weight: 500;
  }
</style>