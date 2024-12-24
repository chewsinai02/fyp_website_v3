@extends('admin.layout')
@section('title', 'Search Patient')
@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <h2 class="text-gradient mb-0">Search Patient</h2>
            </div>
            <p class="text-muted-light">Found {{ $users->count() }} patient(s) matching your search</p>
        </div>
        
        <div class="d-flex gap-3 align-items-center">
            <!-- Add Patient Button -->
            <a href="{{ route('addNewPatient') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Add Patient
            </a>

            <!-- Search Bar -->
            <div class="search-container">
                <form method="GET" action="{{ route('searchPatient') }}" class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="text" 
                               name="queryPatient" 
                               class="form-control search-input" 
                               placeholder="Search for patient..." 
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

    <!-- Results Section -->
    @if($users->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-search text-muted-light" style="font-size: 3rem;"></i>
                <h4 class="mt-3">No patient found</h4>
                <p class="text-muted-light">Try adjusting your search criteria</p>
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
                                <th class="py-3">Email</th>
                                <th class="py-3">Contact Number</th>
                                <th class="py-3">Gender</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @if($user->role === 'patient')
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/profile.png') }}" 
                                                     alt="Profile Picture" 
                                                     class="rounded-circle me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--primary-light);">
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <span class="badge badge-patient">Patient</span>
                                                </div>
                                            </div>
                                        </td>
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
                                                   title="Edit Patient">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                
                                                <!-- Delete button -->
                                                <form action="{{ route('users.destroy', $user->id) }}" 
                                                      method="POST" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this patient?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Delete Patient">
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

    .badge-patient {
        background-color: #FCD34D !important;   /* Amber */
        color: #92400E !important;             /* Dark Amber */
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
</style>
@endsection
