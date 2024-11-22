<!-- Beds Modal -->
<div class="modal fade" id="bedsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Room Beds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Search Box -->
                <div class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               id="roomPatientSearch" 
                               class="form-control"
                               placeholder="Search patients in this room...">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Bed Number</th>
                                <th>Status</th>
                                <th>Patient</th>
                                <th>Patient Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bedsTableBody">
                            <!-- Beds will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('nurseAdmin.editBeds')

<script>
let currentRoomId = null;
let allBedsData = []; // Store all beds data
let patientCache = new Map(); // Store patient data
let currentBedId = null;
let currentBedStatus = null;

function showBeds(roomId) {
    currentRoomId = roomId;
    
    fetch(`/nurseadmin/rooms/${roomId}/beds`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            allBedsData = data.beds; // Store the beds data
            updateBedsTable(allBedsData); // Show all beds initially
            $('#bedsModal').modal('show'); // Show the modal
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to fetch beds'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to fetch beds'
        });
    });
}

function updateBedsTable(beds) {
    const tableBody = document.getElementById('bedsTableBody');
    tableBody.innerHTML = '';
    
    if (beds.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">No matching patients found</td>
            </tr>
        `;
        return;
    }
    
    beds.forEach(bed => {
        const row = document.createElement('tr');
        
        const statusClass = {
            'available': 'bg-success',
            'occupied': 'bg-warning',
            'maintenance': 'bg-danger'
        }[bed.status] || 'bg-secondary';
        
        // Check cache first
        if (bed.patient_id && patientCache.has(bed.patient_id)) {
            const patientData = patientCache.get(bed.patient_id);
            updateRowWithPatientData(row, bed, patientData, statusClass);
            tableBody.appendChild(row);
        } else if (bed.patient_id) {
            // Fetch and cache patient details if not in cache
            fetch(`/api/patients/${bed.patient_id}`)
                .then(response => response.json())
                .then(patientData => {
                    if (patientData) {
                        patientCache.set(bed.patient_id, patientData);
                    }
                    updateRowWithPatientData(row, bed, patientData, statusClass);
                })
                .catch(error => {
                    console.error('Error fetching patient details:', error);
                    updateRowWithError(row, bed, statusClass);
                });
            tableBody.appendChild(row);
        } else {
            updateRowWithPatientData(row, bed, null, statusClass);
            tableBody.appendChild(row);
        }
    });
}

// Helper function to update row with patient data
function updateRowWithPatientData(row, bed, patientData, statusClass) {
    row.innerHTML = `
        <td>Bed ${bed.bed_number}</td>
        <td>
            <span class="badge ${statusClass}">${bed.status}</span>
        </td>
        <td>
            ${(() => {
                if (bed.status !== 'occupied') {
                    return '<span class="text-muted">No patient </span>';
                }
                return patientData ? 
                    patientData.name : 
                    '<span class="text-warning">Loading patient data...</span>';
            })()}
        </td>
        <td>
            ${bed.status === 'occupied' ? 
                (patientData ? `
                    <small>
                        <strong>IC:</strong> ${patientData.ic_number || 'N/A'}<br>
                        <strong>Contact:</strong> ${patientData.contact_number || 'N/A'}<br>
                        <strong>Blood Type:</strong> ${patientData.blood_type || 'N/A'}
                    </small>
                ` : '-')
                : '-'
            }
        </td>
        <td>
            <button class="btn btn-sm btn-outline-primary me-1" 
                    onclick="editBedStatus(${bed.id}, '${bed.status}')"
                    ${bed.status === 'occupied'}>
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" 
                    onclick="deleteBed(${bed.id})"
                    ${bed.status === 'occupied'}>
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
}

// Helper function to update row with error message
function updateRowWithError(row, bed, statusClass) {
    row.innerHTML = `
        <td>Bed ${bed.bed_number}</td>
        <td>
            <span class="badge ${statusClass}">${bed.status}</span>
        </td>
        <td colspan="2">
            <span class="text-danger">Error loading patient details</span>
        </td>
        <td>
            <button class="btn btn-sm btn-outline-primary me-1" 
                    onclick="editBedStatus(${bed.id}, '${bed.status}')"
                    ${bed.status === 'occupied'}>
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" 
                    onclick="deleteBed(${bed.id})"
                    ${bed.status === 'occupied'}>
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
}

// Search using cached data
document.getElementById('roomPatientSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        updateBedsTable(allBedsData);
        return;
    }
    
    const filteredBeds = allBedsData.filter(bed => {
        if (!bed.patient_id || bed.status !== 'occupied') return false;
        
        const patientData = patientCache.get(bed.patient_id);
        if (!patientData) return false;
        
        return (
            String('Bed ' + bed.bed_number)?.toLowerCase().includes(searchTerm) ||
            patientData.name?.toLowerCase().includes(searchTerm) ||
            patientData.staff_id?.toLowerCase().includes(searchTerm) ||
            patientData.gender?.toLowerCase().includes(searchTerm) ||
            patientData.email?.toLowerCase().includes(searchTerm) ||
            patientData.ic_number?.toLowerCase().includes(searchTerm) ||
            patientData.address?.toLowerCase().includes(searchTerm) ||
            patientData.blood_type?.toLowerCase().includes(searchTerm) ||
            patientData.contact_number?.toLowerCase().includes(searchTerm) ||
            patientData.emergency_contact?.toLowerCase().includes(searchTerm)
        );
    });
    
    updateBedsTable(filteredBeds);
});

// Clear search when modal is hidden
$('#bedsModal').on('hidden.bs.modal', function () {
    document.getElementById('roomPatientSearch').value = '';
    updateBedsTable(allBedsData);
});

function editBedStatus(bedId, status) {
    currentBedId = bedId;
    currentBedStatus = status;
    
    const editModal = new bootstrap.Modal(document.getElementById('editBedModal'));
    
    // Reset all sections
    $('#availableBedOptions').hide();
    $('#occupiedBedOptions').hide();
    $('#maintenanceBedOptions').hide();

    // Show/hide appropriate sections based on bed status
    if (status === 'available') {
        $('#availableBedOptions').show();
        loadUnassignedPatients(); // Load patients for available beds
    } else if (status === 'occupied') {
        $('#occupiedBedOptions').show();
        loadAvailableRooms(); // Load rooms for occupied beds
    } else if (status === 'maintenance') {
        $('#maintenanceBedOptions').show();
        loadUnassignedPatients(); // Load patients for maintenance
        // Optionally load all beds or specific beds if needed
    }

    editModal.show();
}

function loadUnassignedPatients() {
    fetch('/api/patients/unassigned')
        .then(response => response.json())
        .then(patients => {
            const select = document.getElementById('patientSelect');
            select.innerHTML = patients.map(patient => 
                `<option value="${patient.id}">${patient.name} (${patient.ic_number})</option>`
            ).join('');
        });
}

function loadAvailableRooms() {
    fetch('/api/rooms/available')
        .then(response => response.json())
        .then(rooms => {
            const select = document.getElementById('roomSelect');
            select.innerHTML = rooms.map(room => 
                `<option value="${room.id}">Room ${room.room_number}</option>`
            ).join('');
        });
}

// Load available beds when room is selected
document.getElementById('roomSelect')?.addEventListener('change', function(e) {
    const roomId = e.target.value;
    const bedSelect = document.getElementById('bedSelect');
    
    if (!roomId) {
        bedSelect.disabled = true;
        bedSelect.innerHTML = '<option value="">Select room first</option>';
        return;
    }
    
    fetch(`/api/rooms/${roomId}/available-beds`)
        .then(response => response.json())
        .then(beds => {
            bedSelect.innerHTML = beds.map(bed => 
                `<option value="${bed.id}">Bed ${bed.bed_number}</option>`
            ).join('');
            bedSelect.disabled = false;
        });
});

function saveBedChanges() {
    const action = currentBedStatus === 'available' 
        ? document.getElementById('availableActionSelect').value
        : document.getElementById('occupiedActionSelect').value;
    
    let data = {
        bed_id: currentBedId,
        action: action
    };
    
    if (action === 'assign') {
        data.patient_id = document.getElementById('patientSelect').value;
    } else if (action === 'transfer') {
        data.new_bed_id = document.getElementById('bedSelect').value;
    }
    
    fetch('/api/beds/manage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message
            });
            $('#editBedModal').modal('hide');
            showBeds(currentRoomId); // Refresh the beds table
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message
            });
        }
    });
}

function deleteBed(bedId) {
    Swal.fire({
        title: 'Discharge Patient?',
        text: 'This will discharge the patient and set the bed to maintenance for cleaning.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, discharge'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/beds/${bedId}/discharge`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message
                    });
                    showBeds(currentRoomId); // Refresh the beds table
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message
                    });
                }
            });
        }
    });
}

function changeBedStatus(bedId, newStatus, notes) {
    const data = {
        bed_id: bedId,
        status: newStatus,
        notes: notes // Optional notes for the status change
    };

    fetch('/api/beds/change-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Bed status updated to available successfully!'
            });
            showBeds(currentRoomId); // Refresh the beds table
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message
            });
        }
    })
    .catch(error => {
        console.error('Error updating bed status:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to update bed status: ' + error.message
        });
    });
}
</script>