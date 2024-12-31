@php
    $currentUserRole = auth()->user()->role;
@endphp

<!-- Beds Modal -->
<div class="modal fade" id="bedsModal" tabindex="-1" aria-labelledby="bedsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bedsModalLabel">Room Beds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Box -->
                <div class="mb-4">
                    <label for="roomPatientSearch" class="form-label visually-hidden">Search patients</label>
                    <div class="input-group">
                        <input type="text" 
                               id="roomPatientSearch" 
                               name="search"
                               class="form-control"
                               placeholder="Search patients in this room..."
                               aria-label="Search patients in this room"
                               title="Search patients">
                        <span class="input-group-text" title="Search icon">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Bed Info</th>
                                <th scope="col">Status</th>
                                <th scope="col">Patient</th>
                                <th scope="col">Patient Details</th>
                                <th scope="col">Actions</th>
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

<div class="d-none">
    Current User Role: {{ $currentUserRole }}
</div>

@include('nurseAdmin.editBeds')

<script>
console.log('Current User Role:', '{{ $currentUserRole }}');

let currentRoomId = null;
let allBedsData = [];
let currentBedId = null;
let currentBedStatus = null;

function showBeds(roomId) {
    currentRoomId = roomId;
    
    // Show loading state
    const tableBody = document.getElementById('bedsTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;
    
    // Show the modal while loading
    $('#bedsModal').modal('show');
    
    // Fetch beds data with proper headers
    fetch(`/api/rooms/${roomId}/beds`, {
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
        
        if (!response.ok) {
            console.error('Response Error:', {
                status: response.status,
                data: data,
                userRole: '{{ $currentUserRole }}'
            });
            
            if (response.status === 403) {
                throw new Error(`Access Denied. Your role (${data.debug?.user_role}) does not have permission. Required roles: ${data.debug?.required_roles?.join(', ')}`);
            } else if (response.status === 401) {
                throw new Error('Please log in to view bed details');
            }
            throw new Error(data.message || 'Failed to load beds');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            allBedsData = data.beds;
            updateBedsTable(allBedsData);
        } else {
            throw new Error(data.message || 'Failed to fetch beds');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${error.message}
                        ${error.debug ? `<br><small class="text-muted">${JSON.stringify(error.debug)}</small>` : ''}
                    </div>
                </td>
            </tr>
        `;
        
        if (error.message.includes('Please log in')) {
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        }
    });
}

function updateBedsTable(beds) {
    const tableBody = document.getElementById('bedsTableBody');
    tableBody.innerHTML = '';
    
    if (!beds || beds.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        No beds found in this room
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    beds.forEach(bed => {
        const row = document.createElement('tr');
        const patientId = bed.status === 'occupied' && bed.patient ? bed.patient.id : '';
        
        row.innerHTML = `
            <td>
                <div class="d-flex flex-column">
                    <div class="fw-medium mb-1">Bed ${bed.bed_number}</div>
                    ${bed.status === 'occupied' ? 
                        `<span class="badge bg-${getConditionBadgeClass(bed.condition)}-subtle 
                                text-${getConditionBadgeClass(bed.condition)}">
                            ${bed.condition || 'Not Set'}
                        </span>
                        ${bed.notes ? 
                            `<div class="small text-muted mt-1">
                                <i class="bi bi-info-circle"></i> ${bed.notes}
                            </div>` : 
                            ''}` : 
                        ''}
                </div>
            </td>
            <td>
                <span class="badge ${getStatusBadgeClass(bed.status)} text-white" style="font-size: 0.75rem;">
                    ${capitalizeFirst(bed.status)}
                </span>
            </td>
            <td class="patient-name-cell" data-bed-id="${bed.id}" data-patient-id="${patientId}">
                ${bed.status === 'occupied' && bed.patient ? 
                    `<div class="fw-medium">${bed.patient.name}</div>
                     <small class="text-muted">${bed.patient.ic_number || 'No IC'}</small>` : 
                    '<span class="text-muted">No patient</span>'}
            </td>
            <td class="patient-details-cell" data-bed-id="${bed.id}">
                ${bed.status === 'occupied' && bed.patient ? 
                    `<div class="small">
                        <div><strong>Gender:</strong> ${bed.patient.gender || 'N/A'}</div>
                        <div><strong>Blood Type:</strong> ${bed.patient.blood_type || 'N/A'}</div>
                        <div><strong>Contact:</strong> ${bed.patient.contact_number || 'N/A'}</div>
                     </div>` : 
                    '-'}
            </td>
            <td class="actions-cell">
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary me-1" 
                            onclick="editBedStatus(${bed.id}, '${bed.status}')"
                            title="Edit bed status"
                            aria-label="Edit bed status"
                            style="min-width: 38px;">
                        <i class="bi bi-pencil" aria-hidden="true"></i>
                    </button>
                    ${bed.status === 'occupied' ? `
                        <button class="btn btn-sm btn-outline-danger"
                                onclick="dischargeBed(${bed.id})"
                                title="Discharge patient"
                                aria-label="Discharge patient"
                                style="min-width: 38px;">
                            <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}

function fetchPatientDetails(patientId, bedId) {
    fetch(`/api/patients/${patientId}`)
        .then(response => response.json())
        .then(patient => {
            if (patient && !patient.error) {
                updatePatientCells(bedId, patient);
            } else {
                throw new Error(patient.error || 'Patient not found');
            }
        })
        .catch(error => {
            console.error('Error fetching patient details:', error);
            updatePatientCells(bedId, null, true);
        });
}

function updatePatientCells(bedId, patient, error = false) {
    const nameCell = document.querySelector(`.patient-name-cell[data-bed-id="${bedId}"]`);
    const detailsCell = document.querySelector(`.patient-details-cell[data-bed-id="${bedId}"]`);
    
    if (error) {
        nameCell.innerHTML = '<span class="text-danger">Error loading patient</span>';
        detailsCell.innerHTML = '<span class="text-danger">Error loading details</span>';
        return;
    }
    
    if (!patient) {
        nameCell.innerHTML = '<span class="text-muted">No patient data</span>';
        detailsCell.innerHTML = '-';
        return;
    }
    
    // Update name cell
    nameCell.innerHTML = `
        <div class="fw-medium">${patient.name}</div>
        <small class="text-muted">${patient.ic_number || 'No IC'}</small>
    `;
    
    // Update details cell
    detailsCell.innerHTML = `
        <div class="small">
            <div><strong>Gender:</strong> ${patient.gender || 'N/A'}</div>
            <div><strong>Blood Type:</strong> ${patient.blood_type || 'N/A'}</div>
            <div><strong>Contact:</strong> ${patient.contact_number || 'N/A'}</div>
        </div>
    `;
}

function getStatusBadgeClass(status) {
    const classes = {
        'available': 'bg-success',
        'occupied': 'bg-warning',
        'maintenance': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

function capitalizeFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Search functionality
document.getElementById('roomPatientSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    
    if (!searchTerm) {
        updateBedsTable(allBedsData);
        return;
    }
    
    const filteredBeds = allBedsData.filter(bed => {
        // Include bed number in search
        if ((`bed ${bed.bed_number}`).toLowerCase().includes(searchTerm)) {
            return true;
        }
        
        // If bed is occupied, search through patient details
        if (bed.status === 'occupied' && bed.patient) {
            const patient = bed.patient;
            return (
                patient.name?.toLowerCase().includes(searchTerm) ||
                patient.ic_number?.toLowerCase().includes(searchTerm) ||
                patient.gender?.toLowerCase().includes(searchTerm) ||
                patient.blood_type?.toLowerCase().includes(searchTerm) ||
                patient.contact_number?.toLowerCase().includes(searchTerm)
            );
        }
        
        return false;
    });
    
    updateBedsTable(filteredBeds);
});

// Clear search when modal is hidden
$('#bedsModal').on('hidden.bs.modal', function () {
    document.getElementById('roomPatientSearch').value = '';
    updateBedsTable(allBedsData);
});

function dischargeBed(bedId) {
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
                    throw new Error(result.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to discharge patient'
                });
            });
        }
    });
}

function editBedStatus(bedId, status) {
    currentBedId = bedId;
    currentBedStatus = status;
    $('#editBedModal').modal('show');
}

function getConditionBadgeClass(condition) {
    const classes = {
        'Critical': 'danger',
        'Serious': 'warning',
        'Fair': 'info',
        'Good': 'success',
        'Stable': 'primary'
    };
    return classes[condition] || 'secondary';
}
</script>

<style>
.badge {
    font-size: 0.75rem;
    padding: 0.4em 0.8em;
    font-weight: 500;
}

.patient-name-cell {
    min-width: 150px;
    max-width: 200px;
}

.patient-details-cell {
    min-width: 150px;
    max-width: 200px;
}

.actions-cell {
    width: 100px;
}

.actions-cell .d-flex {
    justify-content: flex-start;
    align-items: center;
}

.actions-cell .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.actions-cell .btn i {
    font-size: 1.25rem;
}

/* Button hover effects */
.actions-cell .btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s;
}

.actions-cell .btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
}

.actions-cell .btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}

/* Status badge colors */
.bg-success {
    background-color: #198754 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
}

/* Condition badge styles */
.badge.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.badge.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.badge.bg-info-subtle {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.badge.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.badge.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.badge.text-danger {
    color: #dc3545 !important;
}

.badge.text-warning {
    color: #ffc107 !important;
}

.badge.text-info {
    color: #0dcaf0 !important;
}

.badge.text-success {
    color: #198754 !important;
}

.badge.text-primary {
    color: #0d6efd !important;
}

/* Notes styling */
.small.text-muted {
    font-size: 0.75rem;
}

.small.text-muted i {
    font-size: 0.7rem;
}

/* Bed info column styling */
.bed-info {
    min-width: 120px;
}

.bed-number {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

/* Update badge margins */
.badge {
    margin-bottom: 0.25rem;
}

/* Make the condition badge slightly smaller than the status badge */
.badge.condition-badge {
    font-size: 0.7rem;
}

/* Add some spacing between bed number and condition */
.bed-info .badge {
    margin-top: 0.25rem;
}

/* Make notes more compact */
.small.text-muted {
    font-size: 0.7rem;
    line-height: 1.2;
    margin-top: 0.25rem;
}
</style>