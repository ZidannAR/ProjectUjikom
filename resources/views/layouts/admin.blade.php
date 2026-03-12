<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Sistem Absensi</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- FontAwesome 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- SB Admin 2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Absensi <sup>Admin</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Master Data
            </div>

            <!-- Nav Item - Karyawan -->
            <li class="nav-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.employees.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Karyawan</span>
                </a>
            </li>

            <!-- Nav Item - Department -->
            <li class="nav-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.departments.index') }}">
                    <i class="fas fa-fw fa-building"></i>
                    <span>Department</span>
                </a>
            </li>

            <!-- Nav Item - Lokasi Kantor -->
            <li class="nav-item {{ request()->routeIs('admin.office-locations.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.office-locations.index') }}">
                    <i class="fas fa-fw fa-map-marker-alt"></i>
                    <span>Lokasi Kantor</span>
                </a>
            </li>

            <!-- Nav Item - Shift -->
            <li class="nav-item {{ request()->routeIs('admin.shifts.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.shifts.index') }}">
                    <i class="fas fa-fw fa-clock"></i>
                    <span>Shift</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Absensi & Cuti
            </div>

            <!-- Nav Item - Laporan Absensi -->
            <li class="nav-item {{ request()->routeIs('admin.attendance-report.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.attendance-report.index') }}">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Laporan Absensi</span>
                </a>
            </li>

            <!-- Nav Item - Manajemen Cuti -->
            <li class="nav-item {{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.leave-requests.index') }}">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>Manajemen Cuti</span>
                </a>
            </li>

            <!-- Nav Item - Hari Libur -->
             
            <!-- <li class="nav-item {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.holidays.index') }}">
                    <i class="fas fa-fw fa-umbrella-beach"></i>
                    <span>Hari Libur</span>
                </a>
            </li> -->

            <!-- Nav Item - Log Absensi -->
            <li class="nav-item {{ request()->routeIs('admin.attendance-logs.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.attendance-logs.index') }}">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Log Absensi</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Penilaian Karyawan
            </div>

            <!-- Indikator Penilaian -->
            <li class="nav-item {{ request()->routeIs('admin.assessment-categories.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.assessment-categories.index') }}">
                    <i class="fas fa-fw fa-tags"></i>
                    <span>Indikator Penilaian</span>
                </a>
            </li>

            <!-- Input Penilaian -->
            <li class="nav-item {{ request()->routeIs('admin.assessments.*') && !request()->routeIs('admin.assessments.report') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.assessments.index') }}">
                    <i class="fas fa-fw fa-star"></i>
                    <span>Input Penilaian</span>
                </a>
            </li>

            <!-- Laporan Penilaian -->
            <li class="nav-item {{ request()->routeIs('admin.assessments.report') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.assessments.report') }}">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Laporan Penilaian</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Pengaturan
            </div>

            <!-- Nav Item - Manajemen Akun -->
            <li class="nav-item {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.accounts.index') }}">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Manajemen Akun</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Cari..." aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Administrator</span>
                                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=Admin&background=4e73df&color=fff&size=60" alt="Admin">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Sistem Absensi {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery Easing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <!-- SB Admin 2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Init DataTables globally
        $(document).ready(function() {
            if ($('.dataTable').length && !$.fn.DataTable.isDataTable('.dataTable')) {
                $('.dataTable').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                    }
                });
            }
        });

        // SweetAlert delete confirmation
        function confirmDelete(formId) {
            Swal.fire({
                title: 'Yakin hapus data ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // Flash messages with SweetAlert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session("error") }}',
            });
        @endif
    </script>

    @stack('scripts')
</body>
</html>
