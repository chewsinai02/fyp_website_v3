@extends('admin/layout')
@section('title', 'My Users')
@section('content')
<div class="container-fluid p-0">
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient mb-2">My Users</h2>
            <p class="text-muted-light">Your users are appearing here.</p>
        </div>
        
        <!-- Search Bar -->
        <div class="search-container">
            <form method="GET" action="{{ route('searchUser') }}" class="d-flex align-items-center">
                <div class="input-group">
                    <input type="text" 
                           name="queryUser" 
                           class="form-control search-input" 
                           placeholder="Search for users..." 
                           required
                           style="border-radius: 10px 0 0 10px; border-right: none;">
                    <button type="submit" 
                            class="btn btn-primary" 
                            style="border-radius: 0 10px 10px 0; padding: 0.75rem 1.5rem;">
                        <i class="bi bi-search me-2"></i>
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Contact Number</th>
                            <th class="py-3">Gender</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/profile.png') }}" 
                                             alt="Profile Picture" 
                                             class="rounded-circle me-3" 
                                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--primary-light);">
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge badge-{{ strtolower(str_replace(' ', '_', $user->role)) }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="py-3">{{ $user->email }}</td>
                                <td class="py-3">{{ $user->contact_number }}</td>
                                <td class="py-3">{{ $user->gender }}</td>
                                <td class="py-3 text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @if($user->role === 'patient')
                                            <!-- Family Members button -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#familyModal{{ $user->id }}"
                                                    title="Manage Family Members">
                                                <i class="bi bi-people"></i>
                                            </button>
                                        @endif
                                        
                                        <!-- View button -->
                                        <a href="{{ route('admindetailshow', $user->id) }}" 
                                           class="btn btn-sm btn-light" 
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($user->role !== 'admin')
                                            <!-- Edit button -->
                                            <a href="{{ route('details.edit', $user->id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Edit User">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endif
                                        
                                        <!-- Delete button -->
                                        <form action="{{ route('users.destroy', $user->id) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Delete User">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom styles for this page */
    .search-input:focus {
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
        border-color: var(--primary);
    }

    .badge {
        padding: 0.5em 1em;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
    }

    .btn-light {
        background: var(--background);
        border: none;
    }

    .btn-light:hover {
        background: #e2e8f0;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 0.5rem;
        border-bottom-color: #f1f5f9;
    }

    .table > thead {
        border-bottom: 2px solid #e2e8f0;
    }

    .input-group {
        min-width: 400px;
    }

    .input-group .form-control:focus {
        border-color: var(--primary);
        box-shadow: none;
    }

    .input-group .form-control {
        border-color: #e2e8f0;
    }

    .input-group .btn {
        border: 1px solid var(--primary);
    }

    /* Role Badge Colors */
    .badge-admin {
        background-color: #818CF8 !important;   /* Indigo */
        color: #312E81 !important;             /* Dark Indigo */
    }

    .badge-doctor {
        background-color: #34D399 !important;   /* Emerald */
        color: #065F46 !important;             /* Dark Emerald */
    }

    .badge-nurse {
        background-color: #60A5FA !important;   /* Blue */
        color: #1E40AF !important;             /* Dark Blue */
    }

    .badge-nurse_admin {
        background-color: #FDA4AF !important;   /* Rose */
        color: #9D174D !important;             /* Dark Rose */
    }

    .badge-patient {
        background-color: #FCD34D !important;   /* Amber */
        color: #92400E !important;             /* Dark Amber */
    }
</style>
<style>
    .input-group {
        min-width: 400px;
    }

    .input-group .form-control:focus {
        border-color: var(--primary);
        box-shadow: none;
    }

    .input-group .form-control {
        border-color: #e2e8f0;
    }

    .input-group .btn {
        border: 1px solid var(--primary);
    }

    .badge-patient {
        background-color: #60A5FA !important;   /* Blue */
        color: #1E40AF !important; 
        padding: 0.5em 1em;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
    }

    .btn-light {
        background: var(--background);
        border: none;
    }

    .btn-light:hover {
        background: #e2e8f0;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 0.5rem;
        border-bottom-color: #f1f5f9;
    }

    .table > thead {
        border-bottom: 2px solid #e2e8f0;
    }

    /* Add Patient Admink7i68  Button */
    .btn-primary {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
    }

    /* Enhanced styles */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
                    0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 
                    0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .text-gradient {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .badge-patient {
        background-color: #FCD34D !important;   /* Amber */
        color: #92400E !important;             /* Dark Amber */
        padding: 0.5em 1em;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .search-input {
        min-width: 300px;
        border-radius: 10px 0 0 10px !important;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
    }

    .search-input:focus {
        border-color: var(--primary);
        box-shadow: none;
    }

    /* Table enhancements */
    .table th {
        font-weight: 600;
        color: var(--text);
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .table td {
        vertical-align: middle;
        color: var(--text-light);
    }

    .table tr:hover {
        background-color: #f1f5f9;
    }

    /* Action buttons */
    .btn-sm {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-sm:hover {
        transform: translateY(-2px);
    }

    .btn-light {
        background: var(--background);
        border: none;
    }

    .btn-light:hover {
        background: #e2e8f0;
    }

    /* Family Members Modal Styles */
    .modal-content {
        border: none;
        border-radius: 15px;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 15px 15px 0 0;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .btn-info {
        background-color: #60A5FA;
        border: none;
        color: white;
    }

    .btn-info:hover {
        background-color: #3B82F6;
        color: white;
    }

    .modal-header {
        border-bottom: 0;
    }

    .modal-content {
        border: none;
        border-radius: 15px;
    }

    .card {
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,.125);
    }

    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
        background-color: #f8f9fa;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }

    .form-select {
        border-color: #dee2e6;
    }

    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
    }

    .btn-warning {
        color: #fff;
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-warning:hover {
        color: #fff;
        background-color: #ffca2c;
        border-color: #ffc720;
    }

    .bg-primary-subtle {
        background-color: rgba(13,110,253,.1);
    }

    .input-group-sm .form-select {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group-sm .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-select-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        height: 31px;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        height: 31px;
        width: 31px;
    }

    .table td {
        padding: 0.75rem !important;
    }

    .gap-1 {
        gap: 0.25rem !important;
    }
</style>

<!-- Family Members Modals -->
@foreach($users as $user)
    @if($user->role === 'patient')
        <div class="modal fade" id="familyModal{{ $user->id }}" tabindex="-1" aria-labelledby="familyModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="familyModalLabel{{ $user->id }}">
                            <i class="bi bi-people-fill me-2"></i>
                            Manage Family Members - {{ $user->name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add Family Member Form -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Family Member</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('family-members.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Select Family Member</label>
                                            <select name="relationship" class="form-select select2" required 
                                                    onchange="showMemberDetails(this, {{ $user->id }})">
                                                <option value="">Choose a person</option>
                                                @foreach($users as $potentialMember)
                                                    @if($potentialMember->id != $user->id)
                                                        <option value="{{ $potentialMember->id }}" 
                                                                data-email="{{ $potentialMember->email }}"
                                                                data-contact="{{ $potentialMember->contact_number }}"
                                                                data-gender="{{ $potentialMember->gender }}"
                                                                data-role="{{ $potentialMember->role }}">
                                                            {{ $potentialMember->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Relation Type</label>
                                            <select name="relation" class="form-select" required>
                                                <option value="">Select type</option>
                                                <option value="Spouse">Spouse</option>
                                                <option value="Parent">Parent</option>
                                                <option value="Child">Child</option>
                                                <option value="Sibling">Sibling</option>
                                                <option value="Guardian">Guardian</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-plus-lg me-1"></i> Add
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Selected Member Details Card -->
                                    <div id="selectedPatientDetails{{ $user->id }}" class="card mt-3" style="display: none;">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3 text-muted">Selected Member Details</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-2">
                                                        <i class="bi bi-envelope me-2 text-primary"></i>
                                                        <strong>Email:</strong> 
                                                        <span class="patient-email"></span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="bi bi-telephone me-2 text-primary"></i>
                                                        <strong>Contact:</strong> 
                                                        <span class="patient-contact"></span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-2">
                                                        <i class="bi bi-gender-ambiguous me-2 text-primary"></i>
                                                        <strong>Gender:</strong> 
                                                        <span class="patient-gender"></span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="bi bi-person-badge me-2 text-primary"></i>
                                                        <strong>Role:</strong> 
                                                        <span class="patient-role text-capitalize"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Family Members List -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Current Family Members</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 25%">Name</th>
                                            <th style="width: 25%">Email</th>
                                            <th style="width: 20%">Contact</th>
                                            <th style="width: 20%">Relation</th>
                                            <th style="width: 10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $familyMember = \App\Models\FamilyMember::where('user_id', $user->id)->first();
                                            $familyMembers = collect();
                                            
                                            if ($familyMember && !empty($familyMember->relationship)) {
                                                $relationshipIds = array_filter(explode(',', $familyMember->relationship));
                                                $relations = array_filter(explode(',', $familyMember->relation ?: ''));
                                                
                                                foreach ($relationshipIds as $index => $id) {
                                                    $member = \App\Models\User::find($id);
                                                    if ($member) {
                                                        $familyMembers->push([
                                                            'id' => $member->id,
                                                            'name' => $member->name,
                                                            'email' => $member->email,
                                                            'contact_number' => $member->contact_number,
                                                            'profile_picture' => $member->profile_picture,
                                                            'relation' => isset($relations[$index]) ? $relations[$index] : 'Unknown'
                                                        ]);
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if($familyMembers->isNotEmpty())
                                            @foreach($familyMembers as $index => $member)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ asset($member['profile_picture'] ?: 'images/profile.png') }}" 
                                                                 alt="Profile Picture" 
                                                                 class="rounded-circle me-2" 
                                                                 style="width: 32px; height: 32px; object-fit: cover;">
                                                            <span>{{ $member['name'] }}</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ $member['email'] }}</td>
                                                    <td>{{ $member['contact_number'] }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-1">
                                                            <select name="relation" class="form-select form-select-sm" style="width: 100px;" 
                                                                    onchange="updateRelation(this, {{ $familyMember->id }}, {{ $index }})">
                                                                <option value="">Type</option>
                                                                <option value="Spouse" {{ $member['relation'] === 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                                                <option value="Parent" {{ $member['relation'] === 'Parent' ? 'selected' : '' }}>Parent</option>
                                                                <option value="Child" {{ $member['relation'] === 'Child' ? 'selected' : '' }}>Child</option>
                                                                <option value="Sibling" {{ $member['relation'] === 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                                                <option value="Guardian" {{ $member['relation'] === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                                                                <option value="Other" {{ $member['relation'] === 'Other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                            <button type="button" class="btn btn-warning btn-sm" onclick="submitRelationForm(this)">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('family-members.updateRelation') }}" method="POST" class="d-none relation-form">
                                                            @csrf
                                                            <input type="hidden" name="family_member_id" value="{{ $familyMember->id }}">
                                                            <input type="hidden" name="index" value="{{ $index }}">
                                                            <input type="hidden" name="relation" value="{{ $member['relation'] }}">
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('family-members.destroy', $familyMember->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                            <input type="hidden" name="relationship_id" value="{{ $member['id'] }}">
                                                            <input type="hidden" name="index" value="{{ $index }}">
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this family member?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="bi bi-people display-4 d-block mb-2"></i>
                                                    No family members added yet
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
function showMemberDetails(selectElement, userId) {
    const detailsDiv = document.querySelector(`#selectedPatientDetails${userId}`);
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectElement.value) {
        detailsDiv.querySelector('.patient-email').textContent = selectedOption.dataset.email;
        detailsDiv.querySelector('.patient-contact').textContent = selectedOption.dataset.contact;
        detailsDiv.querySelector('.patient-gender').textContent = selectedOption.dataset.gender;
        detailsDiv.querySelector('.patient-role').textContent = selectedOption.dataset.role;
        detailsDiv.style.display = 'block';
    } else {
        detailsDiv.style.display = 'none';
    }
}

function updateRelation(select, familyMemberId, index) {
    const form = select.closest('td').querySelector('.relation-form');
    form.querySelector('input[name="relation"]').value = select.value;
}

function submitRelationForm(button) {
    const form = button.closest('td').querySelector('.relation-form');
    form.submit();
}
</script>
@endsection
