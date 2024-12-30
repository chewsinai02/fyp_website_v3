@extends('admin.layout')

@section('content')
<style>
    .role-badge {
        color: black !important;
    }
    .badge-dark, .badge-primary {
    }
</style>
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
        <a href="{{ route('adminDashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-users fa-sm text-white-50"></i> Manage Users
        </a>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body" style="color: black">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Doctors Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body" style="color: black">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Doctors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDoctors ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-md fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Nurses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body" style="color: black">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Nurses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalNurses ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-nurse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Patients Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body" style="color: black">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Patients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPatients ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-procedures fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Recent Users Row -->
    <div class="row">
        <!-- Quick Actions Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('addAdminForm') }}" class="btn btn-dark btn-block">
                            <i class="fas fa-plus-circle"></i> Add New Admin
                        </a>
                        <a href="{{ route('addDoctorForm') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-plus-circle"></i> Add New Doctor
                        </a>
                        <a href="{{ route('addNurseForm') }}" class="btn btn-info btn-block">
                            <i class="fas fa-plus-circle"></i> Add New Nurse
                        </a>
                        <a href="{{ route('addPatientForm') }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus-circle"></i> Add New Patient
                        </a>
                        <a href="{{ route('admin.manageProfile') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-user-cog"></i> Manage Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            title="Filter Options">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400" aria-hidden="true"></i>
                            <span class="sr-only">Filter Options</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter By:</div>
                            <a class="dropdown-item" href="#" data-filter="all" title="Show All Users">All Users</a>
                            <a class="dropdown-item" href="#" data-filter="admin" title="Filter Admins">Admins</a>
                            <a class="dropdown-item" href="#" data-filter="doctor" title="Filter Doctors">Doctors</a>
                            <a class="dropdown-item" href="#" data-filter="nurse_admin" title="Filter Nurse Admins">Nurse Admins</a>
                            <a class="dropdown-item" href="#" data-filter="nurse" title="Filter Nurses">Nurses</a>
                            <a class="dropdown-item" href="#" data-filter="patient" title="Filter Patients">Patients</a>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="color: black">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="recentUsersTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Joined Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers ?? [] as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <span data-role="{{ $user->role }}" 
                                            class="role-badge badge badge-{{ 
                                                $user->role == 'doctor' ? 'success' : 
                                                ($user->role == 'nurse' ? 'info' : 
                                                ($user->role == 'nurse_admin' ? 'primary' : 
                                                ($user->role == 'admin' ? 'dark' : 'warning'))) 
                                            }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admindetailshow', $user->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent users found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#recentUsersTable').DataTable({
        "order": [[ 3, "desc" ]],
        "pageLength": 5,
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    });

    // Filter functionality
    $('.dropdown-item[data-filter]').on('click', function(e) {
        e.preventDefault();
        var role = $(this).data('filter');
        
        $.ajax({
            url: '{{ route("admin.filterUsers") }}',
            type: 'GET',
            data: { role: role },
            success: function(response) {
                table.clear();
                
                response.forEach(function(user) {
                    var badge = getBadgeHtml(user.role);
                    var actionBtn = `<a href="/admin/details/${user.id}/detailshow" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                   </a>`;
                    
                    table.row.add([
                        user.name,
                        badge,
                        user.email,
                        new Date(user.created_at).toLocaleDateString(),
                        actionBtn
                    ]).draw();
                });
            }
        });
    });

    function getBadgeHtml(role) {
        var color = {
            'doctor': 'success',
            'nurse': 'info',
            'nurse_admin': 'primary',
            'admin': 'dark',
            'patient': 'warning'
        }[role] || 'secondary';

        var displayRole = role.replace('_', ' ').replace(/(^\w|\s\w)/g, l => l.toUpperCase());
        
        return `<span data-role="${role}" class="role-badge badge badge-${color}">
                ${displayRole}
                </span>`;
    }
});
</script>
@endpush
@endsection
