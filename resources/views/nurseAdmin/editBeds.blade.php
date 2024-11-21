<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.min.js"></script>

<div class="modal fade" id="editBedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Bed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="availableBedOptions">
                    <div class="mb-3">
                        <label class="form-label">Select Action</label>
                        <select class="form-select" id="availableActionSelect">
                            <option value="assign">Assign Patient</option>
                            <option value="maintenance">Set to Maintenance</option>
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
                                            data-email="{{ $patient->email }}"
                                            data-gender="{{ $patient->gender }}"
                                            data-address="{{ $patient->address }}"
                                            data-blood="{{ $patient->blood_type }}"
                                            data-contact="{{ $patient->contact_number }}"
                                            data-emergency="{{ $patient->emergency_contact }}">
                                        <div class="patient-card p-2">
                                            <!-- Patient Name & ID Section -->
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="fs-5 fw-semibold text-primary">
                                                    {{ $patient->name }}
                                                </div>
                                                <div class="badge bg-light text-dark border">
                                                    #{{ $patient->ic_number ?? '---' }}
                                                </div>
                                            </div>
                                            
                                            <!-- Patient Info Tags -->
                                            <div class="d-flex gap-2 mb-2">
                                                @if($patient->gender)
                                                    <span class="badge bg-soft-info px-2 py-1">
                                                        👤 {{ $patient->gender }}
                                                    </span>
                                                @endif
                                                
                                                @if($patient->blood_type)
                                                    <span class="badge bg-soft-danger px-2 py-1">
                                                        🩸 {{ $patient->blood_type }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Contact Info -->
                                            <div class="small text-secondary mb-1">
                                                @if($patient->contact_number)
                                                    <span class="me-3">📱 {{ $patient->contact_number }}</span>
                                                @endif
                                                @if($patient->emergency_contact)
                                                    <span>🆘 {{ $patient->emergency_contact }}</span>
                                                @endif
                                            </div>
                                            
                                            <!-- Address -->
                                            @if($patient->address)
                                                <div class="small text-muted">
                                                    <span class="opacity-75">📍</span> {{ $patient->address }}
                                                </div>
                                            @endif
                                        </div>
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-search me-1"></i> Search by any patient details
                            </small>
                        </div>
                    </div>
                </div>

                <div id="occupiedBedOptions">
                    <div class="mb-3">
                        <label class="form-label">Select Action</label>
                        <select class="form-select" id="occupiedActionSelect">
                            <option value="transfer">Transfer to Another Bed</option>
                            <option value="maintenance">Set to Maintenance</option>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBedChanges()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
function editBedStatus(bedId, status) {
    currentBedId = bedId;
    currentBedStatus = status;
    
    const availableOptions = document.getElementById('availableBedOptions');
    const occupiedOptions = document.getElementById('occupiedBedOptions');
    
    if (status === 'available') {
        availableOptions.style.display = 'block';
        occupiedOptions.style.display = 'none';
        
        // Initialize Select2 with proper matcher
        $('#patientSelect').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#editBedModal'),
            placeholder: "Search for a patient...",
            allowClear: true,
            matcher: function(params, data) {
                // If there are no search terms, return all of the data
                if ($.trim(params.term) === '') {
                    return data;
                }

                // Skip if no dataset
                if (!data.element || !data.element.dataset) {
                    return null;
                }

                var term = params.term.toLowerCase();
                var dataset = data.element.dataset;
                
                // Search across all fields
                if (dataset.name.toLowerCase().includes(term) ||
                    dataset.ic.toLowerCase().includes(term) ||
                    dataset.email.toLowerCase().includes(term) ||
                    dataset.gender.toLowerCase().includes(term) ||
                    dataset.address.toLowerCase().includes(term) ||
                    dataset.blood.toLowerCase().includes(term) ||
                    dataset.contact.toLowerCase().includes(term) ||
                    dataset.emergency.toLowerCase().includes(term)) {
                    return data;
                }

                // Return null if no match
                return null;
            },
            templateResult: formatPatient
        });
    } else if (status === 'occupied') {
        availableOptions.style.display = 'none';
        occupiedOptions.style.display = 'block';
        loadAvailableRooms();
    }
    
    $('#editBedModal').modal('show');
}

$(function () {
    $("#patientSelect").select2({
        dropdownParent: $('#editBedModal')
    });
});

// Clean up Select2 when modal is closed
$('#editBedModal').on('hidden.bs.modal', function () {
    if ($('#patientSelect').data('select2')) {
        $('#patientSelect').select2('destroy');
    }
});

// Add event listener for action select
document.getElementById('availableActionSelect').addEventListener('change', function(e) {
    const assignSection = document.getElementById('assignPatientSection');
    assignSection.style.display = e.target.value === 'assign' ? 'block' : 'none';
});

function formatResult(patient) {
    if (!patient.id) {
        return patient.text;
    }
    return patient.text;
}

function formatSelection(patient) {
    if (!patient.id) {
        // Show the search text when no selection
        let searchText = $('.select2-search__field').val();
        return searchText || 'Type to search...';
    }
    return patient.text;
}

// Update the placeholder text as user types
$(document).on('keyup', '.select2-search__field', function() {
    let searchText = $(this).val();
    $('.select2-selection__rendered').text(searchText || 'Type to search...');
});
</script>

<style>
/* Make the search box more prominent */
.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
    padding: 8px 12px;
    font-size: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    width: 100%;
    margin-bottom: 8px;
}

/* Make dropdown larger */
.select2-dropdown--large {
    min-width: 300px;
}

/* Style the search results */
.select2-results__option {
    padding: 8px 12px;
}

/* Highlight matched text */
.select2-results__option mark {
    background: #ffd700;
    padding: 0;
}

.select2-selection__rendered {
    color: #212529 !important;
}

.search-placeholder {
    color: #6c757d;
}

/* Optional: Style improvements for Select2 */
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
}

.select2-container--bootstrap-5 .select2-selection--single {
    padding: 0.375rem 0.75rem;
}

.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
    padding: 8px;
    border-radius: 4px;
}

.select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
    background-color: #0d6efd;
}

.select2-container--bootstrap-5 .select2-selection {
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.bg-soft-info {
    background-color: #e6f3ff !important;
    color: #0c63e4 !important;
}

.bg-soft-danger {
    background-color: #ffe6e6 !important;
    color: #dc3545 !important;
}

.patient-card {
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.patient-card:hover {
    background-color: #f8f9fa;
}

.select2-results__option {
    margin: 4px 8px;
    border-radius: 8px;
}

.select2-results__option--highlighted[aria-selected] {
    background-color: #f8f9fa !important;
    color: inherit !important;
}

.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
    border-radius: 20px;
    padding: 8px 16px;
    border: 2px solid #e0e0e0;
    transition: all 0.2s ease;
}

.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
}
</style>