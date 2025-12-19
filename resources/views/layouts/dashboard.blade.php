<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Resource App</title>

    <link rel="shortcut icon" href="{{ asset('template/dist/assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,..." type="image/png"> {{-- Favicon Data URI dipotong biar rapi --}}
    
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/iconly.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/extensions/table-datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/ui-icons-dripicons.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/extensions/@icon/dripicons/dripicons.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    {{-- CSS Kalender --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
</head>

<body>
    <script src="{{ asset('template/dist/assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="{{ url('/') }}"><img src="{{ asset('template/dist/assets/compiled/svg/logo.svg') }}" alt="Logo" srcset=""></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2" opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor" d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z"></path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler  x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR MENU --}}
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        {{-- 1. Dashboard --}}
                        @can('dashboard_view')
                        <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ url('/dashboard') }}" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @endcan

                        {{-- 2. Tasks --}}
                        @can('task_view')
                        <li class="sidebar-item {{ request()->is('tasks*') ? 'active' : '' }}">
                            <a href="{{ url('/tasks') }}" class='sidebar-link'>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Tasks</span>
                            </a>
                        </li>
                        @endcan

                        {{-- 3. Grup Manajemen HR (Hanya muncul jika punya izin terkait) --}}
                        @canany(['employee_manage', 'department_manage', 'position_manage', 'permission_manage'])
                        <li class="sidebar-item has-sub {{ request()->is('employees*') || request()->is('departments*') || request()->is('positions*') || request()->is('manage-permissions*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Manajemen HR</span> 
                            </a>
                            
                            <ul class="submenu {{ request()->is('employees*') || request()->is('departments*') || request()->is('positions*') || request()->is('manage-permissions*') ? 'active' : '' }}">
                                
                                @can('employee_manage')
                                <li class="submenu-item {{ request()->is('employees*') ? 'active' : '' }}">
                                    <a href="{{ url('/employees') }}" class="submenu-link">
                                        <i class="bi bi-file-person-fill"></i>
                                        <span>Employees</span>
                                    </a>
                                </li>
                                @endcan
                                
                                @can('department_manage')
                                <li class="submenu-item {{ request()->is('departments*') ? 'active' : '' }}">
                                    <a href="{{ url('/departments') }}" class="submenu-link">
                                        <i class="bi bi-building"></i>
                                        <span>Departments</span></a>
                                </li>
                                @endcan
                                
                                @can('position_manage')
                                <li class="submenu-item {{ request()->is('positions*') ? 'active' : '' }}">
                                    <a href="{{ url('/positions') }}" class="submenu-link">
                                        <i class="bi bi-person-fill"></i>
                                        <span>Positions</span>
                                    </a>
                                </li>
                                @endcan

                                @can('permission_manage')
                                <li class="submenu-item {{ request()->is('manage-permissions*') ? 'active' : '' }}">
                                    <a href="{{ route('permissions.index') }}" class="submenu-link">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        <span>Permissions</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanany
                        
                        {{-- 4. Grup Kehadiran (Semua user bisa akses) --}}
                        <li class="sidebar-item has-sub {{ request()->is('presences*') || request()->is('leave-requests*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-calendar3"></i>
                                <span>Kehadiran</span>
                            </a>
                            <ul class="submenu {{ request()->is('presences*') || request()->is('leave-requests*') ? 'active' : '' }}">
                                
                                {{-- Link Presences --}}
                                <li class="submenu-item {{ request()->is('presences*') ? 'active' : '' }}">
                                    <a href="{{ route('presences.index') }}" class="submenu-link">
                                        <i class="bi bi-calendar2-check"></i>
                                        <span>Presences</span></a>
                                </li>
                                
                                {{-- Link Leave Requests --}}
                                @can('leave_manage')
                                <li class="submenu-item {{ request()->is('leave-requests*') ? 'active' : '' }}">
                                    <a href="{{ route('leave-requests.index') }}" class="submenu-link">
                                        <i class="bi bi-file-earmark-arrow-up"></i>
                                        <span>Leave Requests</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>

                        {{-- 5. Salaries --}}
                        <li class="sidebar-item {{ request()->is('salaries*') ? 'active' : '' }}">
                            <a href="{{ route('salaries.index') }}" class='sidebar-link'>
                                <i class="bi bi-currency-dollar"></i>
                                <span>Salaries</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Akun</li>

                        {{-- 6. Ganti Password (MENU BARU) --}}
                        <li class="sidebar-item {{ request()->routeIs('password.edit') ? 'active' : '' }}">
                            <a href="{{ route('password.edit') }}" class='sidebar-link'>
                                <i class="bi bi-key-fill"></i>
                                <span>Ganti Password</span>
                            </a>
                        </li>

                        {{-- 7. Logout --}}
                        <li class="sidebar-item">
                            <a href="{{ route('logout') }}" class='sidebar-link' 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main">
            @yield('content')

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2023 &copy; Mazer</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                            by <a href="https://saugi.me">Saugi</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <script src="{{ asset('template/dist/assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('template/dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('template/dist/assets/compiled/js/app.js') }}"></script>

    {{-- Script Kalender --}}
    <script src="{{ asset('interactive-bs-event-calenda/dist/bs-calendar.min.js') }}"></script> 

    {{-- Apexcharts --}}
    <script src="{{ asset('template/dist/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>

    {{-- Datatables --}}
    <script src="{{ asset('template/dist/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('template/dist/assets/static/js/pages/simple-datatables.js') }}"></script>

    {{-- ChartJS --}}
    <script src="{{ asset('template/dist/assets/extensions/chart.js/chart.umd.js') }}"></script>

    {{-- Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    @stack('scripts') 

</body>

</html>