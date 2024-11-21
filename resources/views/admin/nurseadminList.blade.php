@extends('admin.layout')
@section('title', 'List of Nurse Admin')
@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient mb-2">List of Nurse Admin</h2>
            <p class="text-muted-light">
                <i class="bi bi-people-fill me-2"></i>
                You have {{ $activeNurseAdminsCount }} active nurse admin{{ $activeNurseAdminsCount !== 1 ? 's' : '' }}
            </p>
        </div>
        
        <div class="d-flex gap-3 align-items-center">
            <!-- Add Nurse Admin Button -->
            <a href="addNurseAdmin" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Add Nurse Admin
            </a>

            <!-- Search Bar -->
            <div class="search-container">
                <form method="GET" action="{{ route('searchNurseAdmin') }}" class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="text" 
                               name="queryNurseAdmin" 
                               class="form-control search-input" 
                               placeholder="Search for nurse admins..." 
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
    </div>

    <!-- Nurse Admin Table -->
    @if($users->where('role', 'nurse_admin')->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <img src="{{ asset('images/empty-state.png') }}" 
                     alt="No Nurse Admins" 
                     class="mb-3" 
                     style="max-width: 200px; opacity: 0.7;">
                <h4 class="text-gradient">No Nurse Admins Found</h4>
                <p class="text-muted-light mb-4">Start by adding your first nurse admin to the system</p>
                <a href="addNurseAdmin" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add Your First Nurse Admin
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="py-3">Staff ID</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Contact Number</th>
                                <th class="py-3">Gender</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @if($user->role === 'nurse_admin')
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/profile.png') }}" 
                                                     alt="Profile Picture" 
                                                     class="rounded-circle me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--primary-light);">
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <span class="badge badge-nurseadmin">Nurse Admin</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">{{ $user->staff_id }}</td>
                                        <td class="py-3">{{ $user->email }}</td>
                                        <td class="py-3">{{ $user->contact_number }}</td>
                                        <td class="py-3">{{ $user->gender }}</td>
                                        <td class="py-3 text-end pe-4">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <!-- View button -->
                                                <a href="{{ route('admindetailshow', $user->id) }}" 
                                                   class="btn btn-sm btn-light" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <!-- Edit button -->
                                                <a href="{{ route('details.edit', $user->id) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="Edit Nurse Admin">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                
                                                <!-- Delete button -->
                                                <form action="{{ route('users.destroy', $user->id) }}" 
                                                      method="POST" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this nurse admin?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Delete Nurse Admin">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

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

    .badge-nurseadmin {
        background-color: #FDA4AF !important;
        color: #9D174D !important;
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

    /* Add Nurse Admink7i68  Button */
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

    .badge-nurseadmin {
        background-color: #FDA4AF !important;
        color: #9D174D !important;
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
</style>
@endsection