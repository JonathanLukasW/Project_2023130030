<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Resource App</title>

    <link rel="shortcut icon" href="{{ asset('template/dist/assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="shortcut icon"
        href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgIGV4aWY6UGl4ZWxYRGltZW5zaW9uPSIzMyIKICAgIGV4aWY6UGl4ZWxZRGltZW5zaW9uPSIzNCIKICAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgIHRpZmY6SW1hZ2VXaWR0aD0iMzMiCiAgIHRpZmY6SW1hZ2VMZW5ndGg9IjM0IgogICB0aWZmOlJlc29sdXRpb25Vbml0PSIzIgogICB0aWZmOlhSZXNvbHV0aW9uPSI5Ni4wIgogICB0aWZmOllSZXNvbHV0aW9uPSI5Ni4wIgogICBwaG90b3Nob3A6Q29sb3JNb2RlPSIzIgogICBwaG90b3Nob3A6SUNDUHJvZmlsZT0ic1JHQiBJRUM2MTk2Ni0yLjEiCiAgIHhtcDpNb2RpZnlEYXRlPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIgogICB4bXA6TWV0YWRhdGFEYXRlPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIj4KICAgPHhtcE1NOkhpc3Rvcnk+CiAgICA8cmRmOlNlcT4KICAgICA8cmRmOmxpCiAgICAgICBzdEV2dDphY3Rpb249InByb2R1Y2VkIgogICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICAgc3RFdnQ6d2hlbj0iMjAyMi0wMy0zMVQxMDo1MDo0MiswMjowMCIvPgogICAgIDwvcmRmOlNlcT4KICAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz4Nv5CKAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6AAAUggAARVYAAA6lwAARXlMAGEAAAHpSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC"
        type="image/png">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/iconly.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/extensions/simple-datatables/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/extensions/table-datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/compiled/css/ui-icons-dripicons.css') }}">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/extensions/@icon/dripicons/dripicons.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <script src="{{ asset('template/dist/assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="{{ url('/') }}"><img src="{{ asset('template/dist/assets/compiled/svg/logo.svg') }}"
                                    alt="Logo" srcset=""></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20"
                                height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                        opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20"
                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                </path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler  x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>

                {{-- REVISI UTAMA DIMULAI DARI SINI --}}
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

                        {{-- 3. Grup Kepegawaian (Hanya muncul jika punya SALAH SATU izin di bawah) --}}
                        {{-- (Kita tambahkan 'permission_manage' di sini biar grupnya "aktif") --}}
                        @canany(['employee_manage', 'department_manage', 'position_manage', 'permission_manage'])
                        <li class="sidebar-item has-sub {{ request()->is('employees*') || request()->is('departments*') || request()->is('positions*') || request()->is('manage-permissions*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Manajemen HR</span> {{-- (Judul diubah biar lebih pas) --}}
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
                                
                                {{-- (Ini pakai 'position_manage' dari "bonus" kita kemarin) --}}
                                @can('position_manage')
                                <li class="submenu-item {{ request()->is('positions*') ? 'active' : '' }}">
                                    <a href="{{ url('/positions') }}" class="submenu-link">
                                        <i class="bi bi-person-fill"></i>
                                        <span>Positions</span>
                                    </a>
                                </li>
                                @endcan

                                {{-- --- INI DIA MENU BARU KITA --- --}}
                                @can('permission_manage')
                                <li class="submenu-item {{ request()->is('manage-permissions*') ? 'active' : '' }}">
                                    <a href="{{ route('permissions.index') }}" class="submenu-link">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        <span>Permissions</span>
                                    </a>
                                </li>
                                @endcan
                                {{-- --- AKHIR MENU BARU --- --}}
                            </ul>
                        </li>
                        @endcanany
                        

                        {{-- 4. Grup Kehadiran --}}
                        @canany(['presence_create', 'leave_manage'])
                        <li class="sidebar-item has-sub {{ request()->is('presences*') || request()->is('leave-requests*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-calendar3"></i>
                                <span>Kehadiran</span>
                            </a>
                            <ul class="submenu {{ request()->is('presences*') || request()->is('leave-requests*') ? 'active' : '' }}">
                                @can('presence_create')
                                <li class="submenu-item {{ request()->is('presences*') ? 'active' : '' }}">
                                    <a href="{{ url('/presences') }}" class="submenu-link">
                                        <i class="bi bi-calendar2-check"></i>
                                        <span>Presences</span></a>
                                </li>
                                @endcan
                                @can('leave_manage')
                                <li class="submenu-item {{ request()->is('leave-requests*') ? 'active' : '' }}">
                                    <a href="{{ url('/leave-requests') }}" class="submenu-link">
                                        <i class="bi bi-file-earmark-arrow-up"></i>
                                        <span>Leave Requests</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanany

                        {{-- 5. Salaries --}}
                        @can('salary_view_all')
                        <li class="sidebar-item {{ request()->is('salaries*') ? 'active' : '' }}">
                            <a href="{{ url('/salaries') }}" class='sidebar-link'>
                                <i class="bi bi-currency-dollar"></i>
                                <span>Salaries</span>
                            </a>
                        </li>
                        @endcan

                        {{-- 6. Logout (Selalu tampil) --}}
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
                {{-- REVISI UTAMA SELESAI --}}

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



    <!-- Need: Apexcharts -->
    <script src="{{ asset('template/dist/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('template/dist/assets/static/js/pages/dashboard.js') }}"></script>

    <script src="{{ asset('template/dist/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    {{-- Path asset simple-datatables.js diperbaiki --}}
    <script src="{{ asset('template/dist/assets/static/js/pages/simple-datatables.js') }}"></script>

    <!-- Need: chartJs -->
    <script src="{{ asset('template/dist/assets/extensions/chart.js/chart.umd.js') }}"></script>

    <!-- Need : buat tanggal-->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let date = flatpickr('.date', {
            dateFormat: "Y-m-d",
        });

        let datetime = flatpickr('.datetime', {
            dateFormat: "Y-m-d H:i:s",
            enableTime: true,
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('presence')) {

                var ctxBar = document.getElementById('presence').getContext('2d');
                var myBar = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                        datasets: [{
                            label: 'Total Kehadiran',
                            data: [],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Total Kehadiran Bulanan (2025)'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 40
                            }
                        }
                    }
                });

                function updateData() {
                    fetch('{{ url("/dashboard/presence") }}')
                        .then(response => response.json())
                        .then((output) => {
                            if (output && Array.isArray(output) && output.length === 12) {
                                myBar.data.datasets[0].data = output;
                                myBar.update();
                            } else {
                                console.error("Data kehadiran yang diterima dari server tidak valid:", output);
                                myBar.data.datasets[0].data = Array(12).fill(0);
                                myBar.update();
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                        });
                }
                updateData();
            }
        });
    </script>


</body>

</html>