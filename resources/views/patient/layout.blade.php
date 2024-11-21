<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <title>@yield('title', 'SUC Hospital')</title>
    <style>
        /* Remove margin and padding for navbar */
        .navbar {
            margin: 0;
            padding: 0;
        }
        .logout-link {
            display: flex;
            align-items: center;
            color: white; /* Ensures the logout link is visible */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <!-- Sidebar -->
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <img src="{{ asset('images/logo.png') }}" alt="logo" class="img-fluid" style="width: 100%; max-height: 100px;">
                    <a href="/admin/dashboard" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-5 d-none d-sm-inline">Menu</span>
                    </a>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                        <li class="nav-item">
                            <a href="/admin/dashboard" class="nav-link align-middle px-0">
                                <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">My Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/adminList" class="nav-link align-middle px-0">
                                <i class="fs-4 bi-shield-lock"></i> <span class="ms-1 d-none d-sm-inline">Admin</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/doctorList" class="nav-link align-middle px-0">
                                <i class="fa-duotone fa-solid fa-user-doctor"></i> <span class="ms-1 d-none d-sm-inline">Doctor</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('nurseAdminList') }}" class="nav-link align-middle px-0">
                                <i class="fs-4 bi-person-badge"></i> <span class="ms-1 d-none d-sm-inline">Nurse Admin</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/nurseList" class="nav-link align-middle px-0">
                                <i class="fa-solid fa-user-nurse"></i> <span class="ms-1 d-none d-sm-inline">Nurse</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/patientList" class="nav-link align-middle px-0">
                                <i class="fa-duotone fa-solid fa-bed-pulse"></i> <span class="ms-1 d-none d-sm-inline">Patient</span>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown pb-2">
                        <a href="{{ route('admin.manageProfile') }}" class="d-flex align-items-center text-white text-decoration-none" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : asset('images/profile.png') }}" 
                                alt="Profile Picture" 
                                class="rounded-circle" 
                                style="width: 30px; height: 30px;">
                            &nbsp;{{ auth()->user()->name }}
                        </a>
                    </div>
                    <!-- Logout link with confirmation alert -->
                    <a class="logout-link" href="#" onclick="confirmLogout(event)">
                        <h5><i class="bi bi-box-arrow-left"></i> &nbsp; <span class="ms-1 d-none d-sm-inline">Logout</span></h5>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>

            <div class="col">
                <div class="row">
                    <!-- Header -->
                    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3" style="width: 100%">
                        <div class="container-fluid d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <button class="btn" onclick="window.history.back();">
                                    <i class="bi bi-arrow-left"></i> <!-- Go Back Icon -->
                                </button>

                                <a href="/admin/dashboard" class="mb-3">
                                    <img src="{{ asset('images/logo.png') }}" alt="logo" class="img-fluid" style="height: 40px; margin: 0 15px;">
                                </a>
                                <a class="navbar-brand" href="{{ route('adminDashboard') }}">SUC Hospital</a>
                            </div>

                            <div class="d-flex align-items-center">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="btn btn-sm btn-success" aria-current="page" href="{{ route('adminDashboard') }}" role="button">Home</a> &nbsp;&nbsp;
                                    </li>
                                </ul>
                                
                                <div class="dropdown">
                                    <a class="nav-brand" href="{{ route('admin.manageProfile') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : asset('images/profile.png') }}"
                                            alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px;">
                                        {{ auth()->user()->name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- Main Content Area -->
                <div class="row">
                    <div class="col py-3">
                        <div class="content">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"></script>
    <!-- JavaScript for confirmation alert -->
    <script>
        function confirmLogout(event) {
            event.preventDefault();
            if (confirm("Are you sure you want to log out?")) {
                document.getElementById('logout-form').submit();
            }
        }
    </script>
</body>
</html>
