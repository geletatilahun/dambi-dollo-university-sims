<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DDU SIMS') - Dambi Dollo University</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --ddu-navy: #0b1f33;
            --ddu-navy-hover: #163657;
            --sidebar-width: 260px;
        }
        body { 
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; 
            background-color: #f4f6f9; 
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: var(--ddu-navy);
            color: #fff;
            z-index: 1000;
            overflow-y: auto;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 24px;
            min-height: 100vh;
        }
        .nav-link { 
            color: rgba(255, 255, 255, 0.8); 
            border-radius: 6px; 
            padding: 9px 14px; 
            font-size: 0.92rem; 
            display: flex; 
            align-items: center; 
        }
        .nav-link:hover { 
            color: #fff; 
            background-color: var(--ddu-navy-hover); 
        }
        .nav-link.active { 
            color: #fff; 
            background-color: #0d6efd; 
            font-weight: 600; 
        }
        .nav-link i.main-icon { 
            margin-right: 10px; 
            font-size: 1.1rem; 
        }
        
        /* Dropdown button styling */
        .dropdown-toggle-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            width: 100%;
            text-align: left;
            padding: 9px 14px;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            border-radius: 6px;
            cursor: pointer;
        }
        .dropdown-toggle-btn:hover { 
            color: #fff; 
            background-color: var(--ddu-navy-hover); 
        }
        .dropdown-toggle-btn .chevron { 
            transition: transform 0.2s ease-in-out; 
            margin-left: auto; 
        }
        .dropdown-toggle-btn[aria-expanded="true"] { 
            color: #fff; 
            background-color: rgba(255, 255, 255, 0.08); 
        }
        .dropdown-toggle-btn[aria-expanded="true"] .chevron { 
            transform: rotate(180deg); 
        }
        
        /* Submenu lists inside dropdowns */
        .submenu-list { 
            list-style: none; 
            padding-left: 18px; 
            margin-top: 3px; 
        }
        .submenu-list .nav-link { 
            font-size: 0.86rem; 
            padding: 6px 12px; 
        }
        
        .topbar { 
            background: #fff; 
            border-bottom: 1px solid #e3e6f0; 
            border-radius: 8px; 
            padding: 12px 20px; 
        }
        .card { 
            border: none; 
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); 
            border-radius: 8px; 
        }
    </style>
</head>
<body>

    <!-- ================= SIDEBAR NAVIGATION ================= -->
    <div class="sidebar d-flex flex-column justify-content-between p-3">
        <div>
            <!-- University Brand -->
            <div class="d-flex align-items-center mb-3 px-2">
                <i class="bi bi-mortarboard-fill fs-3 text-warning me-2"></i>
                <h5 class="mb-0 fw-bold tracking-tight">DDU SIMS</h5>
            </div>

            <ul class="nav flex-column mb-auto gap-1">
                <!-- Direct Link: Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 main-icon"></i> Dashboard
                    </a>
                </li>

                <!-- ================= 1. STUDENT ACADEMIC PORTAL ================= -->
                @if(Auth::user()->hasRole('Student'))
                    <li class="nav-item mt-2">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('student.*', 'schedule.my') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#studentMenu" 
                                aria-expanded="{{ request()->routeIs('student.*', 'schedule.my') ? 'true' : 'false' }}">
                            <i class="bi bi-mortarboard main-icon text-info"></i>
                            <span>Academic Portal</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('student.*', 'schedule.my') ? 'show' : '' }}" id="studentMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('student.registration') }}" class="nav-link {{ request()->routeIs('student.registration') ? 'active' : '' }}">
                                        <i class="bi bi-journal-plus me-2"></i> Course Registration
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('student.adddrop.index') }}" class="nav-link {{ request()->routeIs('student.adddrop.*') ? 'active' : '' }}">
                                        <i class="bi bi-arrow-left-right me-2"></i> Course Add / Drop
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('schedule.my') }}" class="nav-link {{ request()->routeIs('schedule.my') ? 'active' : '' }}">
                                        <i class="bi bi-calendar-week me-2"></i> Weekly Timetable
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('student.grades') }}" class="nav-link {{ request()->routeIs('student.grades') ? 'active' : '' }}">
                                        <i class="bi bi-file-earmark-text me-2"></i> Grade Report
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('student.transcript') }}" class="nav-link" target="_blank">
                                        <i class="bi bi-printer me-2"></i> Official Transcript
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('student.services.index') }}" class="nav-link {{ request()->routeIs('student.services.*') ? 'active' : '' }}">
                                        <i class="bi bi-file-earmark-check me-2"></i> Document & Clearance
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('student.advisor') }}" class="nav-link {{ request()->routeIs('student.advisor*') ? 'active' : '' }}">
                                        <i class="bi bi-person-badge me-2"></i> My Academic Advisor
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ================= 2. INSTRUCTOR TEACHING PORTAL ================= -->
                @if(Auth::user()->hasRole('Instructor'))
                    <li class="nav-item mt-2">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('instructor.*', 'schedule.my', 'advisor.*') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#instructorMenu" 
                                aria-expanded="{{ request()->routeIs('instructor.*', 'schedule.my', 'advisor.*') ? 'true' : 'false' }}">
                            <i class="bi bi-briefcase main-icon text-warning"></i>
                            <span>Teaching & Marks</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('instructor.*', 'schedule.my', 'advisor.*') ? 'show' : '' }}" id="instructorMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('dashboard') }}" class="nav-link">
                                        <i class="bi bi-card-checklist me-2"></i> My Mark Sheets
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('schedule.my') }}" class="nav-link {{ request()->routeIs('schedule.my') ? 'active' : '' }}">
                                        <i class="bi bi-calendar-week me-2"></i> Teaching Timetable
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('advisor.index') }}" class="nav-link {{ request()->routeIs('advisor.*') ? 'active' : '' }}">
                                        <i class="bi bi-people-gear me-2"></i> My Advisees Roster
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ================= 3. COLLEGE DEAN PORTAL ================= -->
                @if(Auth::user()->hasRole('Dean'))
                    <li class="nav-item mt-2">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('admin.structure.*', 'admin.reports.*', 'academic.grades.*') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#deanMenu" 
                                aria-expanded="{{ request()->routeIs('admin.structure.*', 'admin.reports.*', 'academic.grades.*') ? 'true' : 'false' }}">
                            <i class="bi bi-award-fill main-icon text-info"></i>
                            <span>College Supervision</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('admin.structure.*', 'admin.reports.*', 'academic.grades.*') ? 'show' : '' }}" id="deanMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('admin.structure.index') }}" class="nav-link {{ request()->routeIs('admin.structure.*') ? 'active' : '' }}">
                                        <i class="bi bi-building me-2"></i> College Departments
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.grades.department') }}" class="nav-link {{ request()->routeIs('academic.grades.department') ? 'active' : '' }}">
                                        <i class="bi bi-check2-circle me-2"></i> Grade Endorsements
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.grades.changes.index') }}" class="nav-link {{ request()->routeIs('academic.grades.changes.*') ? 'active' : '' }}">
                                        <i class="bi bi-arrow-repeat me-2"></i> Grade Changes
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                        <i class="bi bi-bar-chart-line-fill me-2"></i> College Analytics
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ================= 4. DEPARTMENT HEAD MANAGEMENT ================= -->
                @if(Auth::user()->hasRole(['DepartmentHead', 'SuperAdmin']))
                    <li class="nav-item mt-2">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('academic.catalog.*', 'academic.adddrop.*', 'academic.advisors.*', 'academic.timetables.*', 'academic.curricula.*') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#deptMgmtMenu" 
                                aria-expanded="{{ request()->routeIs('academic.catalog.*', 'academic.adddrop.*', 'academic.advisors.*', 'academic.timetables.*', 'academic.curricula.*') ? 'true' : 'false' }}">
                            <i class="bi bi-diagram-3 main-icon text-success"></i>
                            <span>Department Setup</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('academic.catalog.*', 'academic.adddrop.*', 'academic.advisors.*', 'academic.timetables.*', 'academic.curricula.*') ? 'show' : '' }}" id="deptMgmtMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('academic.catalog.offerings') }}" class="nav-link {{ request()->routeIs('academic.catalog.offerings*') ? 'active' : '' }}">
                                        <i class="bi bi-calendar3 me-2"></i> Course Offerings
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.adddrop.index') }}" class="nav-link {{ request()->routeIs('academic.adddrop.*') ? 'active' : '' }}">
                                        <i class="bi bi-arrow-left-right me-2"></i> Add/Drop Requests
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.advisors.assign') }}" class="nav-link {{ request()->routeIs('academic.advisors.*') ? 'active' : '' }}">
                                        <i class="bi bi-person-gear me-2"></i> Assign Advisors
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.curricula.index') }}" class="nav-link {{ request()->routeIs('academic.curricula.*') ? 'active' : '' }}">
                                        <i class="bi bi-mortarboard me-2"></i> Curricula Roadmap
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.timetables.index') }}" class="nav-link {{ request()->routeIs('academic.timetables.*') ? 'active' : '' }}">
                                        <i class="bi bi-clock-history me-2"></i> Lecture Timetables
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('academic.grades.*') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#deptReviewMenu" 
                                aria-expanded="{{ request()->routeIs('academic.grades.*') ? 'true' : 'false' }}">
                            <i class="bi bi-shield-check main-icon text-warning"></i>
                            <span>Department Review</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('academic.grades.*') ? 'show' : '' }}" id="deptReviewMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('academic.grades.department') }}" class="nav-link {{ request()->routeIs('academic.grades.department') ? 'active' : '' }}">
                                        <i class="bi bi-check2-circle me-2"></i> Grade Endorsements
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.grades.changes.index') }}" class="nav-link {{ request()->routeIs('academic.grades.changes.*') ? 'active' : '' }}">
                                        <i class="bi bi-arrow-repeat me-2"></i> Grade Changes
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ================= 5. REGISTRAR & SUPERADMIN ================= -->
                @if(Auth::user()->hasRole(['Registrar', 'SuperAdmin']))
                    <!-- Dropdown: Registrar Office -->
                    <li class="nav-item mt-2">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('registrar.students.*', 'registrar.import.*', 'admissions.*', 'academic.grades.registrar') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#registrarMenu" 
                                aria-expanded="{{ request()->routeIs('registrar.students.*', 'registrar.import.*', 'admissions.*', 'academic.grades.registrar') ? 'true' : 'false' }}">
                            <i class="bi bi-people main-icon text-primary"></i>
                            <span>Registrar Office</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('registrar.students.*', 'registrar.import.*', 'admissions.*', 'academic.grades.registrar') ? 'show' : '' }}" id="registrarMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('registrar.students.index') }}" class="nav-link {{ request()->routeIs('registrar.students.*') ? 'active' : '' }}">
                                        <i class="bi bi-person-lines-fill me-2"></i> Student Registry
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('registrar.import.index') }}" class="nav-link {{ request()->routeIs('registrar.import.*') ? 'active' : '' }}">
                                        <i class="bi bi-file-earmark-arrow-up me-2"></i> Batch Student Import
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admissions.queue') }}" class="nav-link {{ request()->routeIs('admissions.*') ? 'active' : '' }}">
                                        <i class="bi bi-inbox-fill me-2"></i> Admissions Queue
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.grades.registrar') }}" class="nav-link {{ request()->routeIs('academic.grades.registrar') ? 'active' : '' }}">
                                        <i class="bi bi-journal-check me-2"></i> Publish Grades
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Dropdown: Records & Clearance -->
                    <li class="nav-item">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('registrar.documents.*', 'registrar.clearance.*', 'admin.reports.*') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#clearanceMenu" 
                                aria-expanded="{{ request()->routeIs('registrar.documents.*', 'registrar.clearance.*', 'admin.reports.*') ? 'true' : 'false' }}">
                            <i class="bi bi-award main-icon text-warning"></i>
                            <span>Clearance & Records</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('registrar.documents.*', 'registrar.clearance.*', 'admin.reports.*') ? 'show' : '' }}" id="clearanceMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('registrar.documents.index') }}" class="nav-link {{ request()->routeIs('registrar.documents.*') ? 'active' : '' }}">
                                        <i class="bi bi-file-earmark-arrow-down me-2"></i> Document Requests
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('registrar.clearance.index') }}" class="nav-link {{ request()->routeIs('registrar.clearance.*') ? 'active' : '' }}">
                                        <i class="bi bi-mortarboard me-2"></i> Graduation Clearance
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                        <i class="bi bi-bar-chart-line-fill me-2"></i> Analytics & Reports
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Dropdown: Academic Setup -->
                    <li class="nav-item">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('admin.structure.*', 'academic.catalog.*', 'admin.calendar.*', 'academic.timetables.*') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#setupMenu" 
                                aria-expanded="{{ request()->routeIs('admin.structure.*', 'academic.catalog.*', 'admin.calendar.*', 'academic.timetables.*') ? 'true' : 'false' }}">
                            <i class="bi bi-diagram-3 main-icon text-info"></i>
                            <span>Academic Setup</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('admin.structure.*', 'academic.catalog.*', 'admin.calendar.*', 'academic.timetables.*') ? 'show' : '' }}" id="setupMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('admin.structure.index') }}" class="nav-link {{ request()->routeIs('admin.structure.*') ? 'active' : '' }}">
                                        <i class="bi bi-building me-2"></i> University Structure
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.catalog.courses') }}" class="nav-link {{ request()->routeIs('academic.catalog.courses*') ? 'active' : '' }}">
                                        <i class="bi bi-book-half me-2"></i> Course Catalog
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.catalog.offerings') }}" class="nav-link {{ request()->routeIs('academic.catalog.offerings*') ? 'active' : '' }}">
                                        <i class="bi bi-calendar3 me-2"></i> Semester Offerings
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('academic.timetables.index') }}" class="nav-link {{ request()->routeIs('academic.timetables.*') ? 'active' : '' }}">
                                        <i class="bi bi-clock-history me-2"></i> Lecture Timetables
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.calendar.index') }}" class="nav-link {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                                        <i class="bi bi-calendar-event me-2"></i> Academic Calendar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Dropdown: Administration & Noticeboard -->
                    <li class="nav-item">
                        <button class="dropdown-toggle-btn {{ request()->routeIs('admin.users.*', 'admin.announcements.*', 'admin.audit.*', 'admin.backups.*') ? '' : 'collapsed' }}" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#adminMenu" 
                                aria-expanded="{{ request()->routeIs('admin.users.*', 'admin.announcements.*', 'admin.audit.*', 'admin.backups.*') ? 'true' : 'false' }}">
                            <i class="bi bi-gear-fill main-icon text-danger"></i>
                            <span>Administration</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('admin.users.*', 'admin.announcements.*', 'admin.audit.*', 'admin.backups.*') ? 'show' : '' }}" id="adminMenu">
                            <ul class="submenu-list">
                                <li>
                                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                        <i class="bi bi-person-gear me-2"></i> User Accounts
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                                        <i class="bi bi-megaphone me-2"></i> Noticeboard
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.audit.index') }}" class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                                        <i class="bi bi-shield-lock me-2"></i> Audit Logs
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.backups.index') }}" class="nav-link {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
                                        <i class="bi bi-database-down me-2"></i> Database Backups
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>

            <!-- ================= UNIVERSAL HANDBOOKS & PDF MANUALS ================= -->
            <div class="border-top pt-2 mt-2">
                <button class="dropdown-toggle-btn collapsed" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#docsMenu" 
                        aria-expanded="false">
                    <i class="bi bi-file-earmark-pdf main-icon text-danger"></i>
                    <span>System Handbooks (PDF)</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </button>
                <div class="collapse" id="docsMenu">
                    <ul class="submenu-list">
                        <li>
                            <a href="{{ route('docs.user_manual') }}" class="nav-link" target="_blank">
                                <i class="bi bi-person-badge me-2"></i> User Manual
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('docs.guidelines') }}" class="nav-link" target="_blank">
                                <i class="bi bi-shield-check me-2"></i> Policy Guidelines (SOP)
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('docs.technical_manual') }}" class="nav-link" target="_blank">
                                <i class="bi bi-cpu me-2"></i> Technical Architecture
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- User Profile & Sign Out Footer -->
        <div class="border-top pt-3 mt-3">
            <div class="px-2 mb-2 text-white-50 small">
                Logged in as:<br>
                <strong class="text-white">{{ Auth::user()->name }}</strong>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                </button>
            </form>
        </div>
    </div>

    <!-- ================= MAIN CONTENT AREA ================= -->
    <div class="main-content">
        <!-- TOP NAVBAR WITH LIVE NOTIFICATION BELL -->
        <div class="topbar d-flex justify-content-between align-items-center mb-4 shadow-sm bg-white">
            <span class="navbar-brand text-muted fs-6 mb-0">
                <i class="bi bi-mortarboard text-primary me-2"></i>Dambi Dollo University Academic Portal
            </span>
            <div class="d-flex align-items-center gap-3">
                <!-- NOTIFICATION BELL DROPDOWN -->
                <div class="dropdown">
                    <button class="btn btn-light position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Announcements & Notifications">
                        <i class="bi bi-bell-fill fs-5 text-secondary"></i>
                        @php
                            $userId = Auth::id();
                            $target = Auth::user()->hasRole('Student') ? ['All', 'Students'] : ['All', 'Instructors', 'Students'];
                            
                            $readIds = \DB::table('announcement_reads')
                                ->where('user_id', $userId)
                                ->pluck('announcement_id')
                                ->toArray();

                            $unreadCount = \App\Models\Announcement::where('is_active', true)
                                ->whereIn('target_audience', $target)
                                ->whereNotIn('id', $readIds)
                                ->count();
                        @endphp

                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="width: 320px;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center border-bottom pb-2">
                            <span class="fw-bold text-dark">
                                <i class="bi bi-megaphone me-1 text-primary"></i> University Notices
                            </span>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-primary text-decoration-none small" style="font-size: 0.75rem;">
                                        Mark all as read
                                    </button>
                                </form>
                            @endif
                        </li>

                        @php
                            $notices = \App\Models\Announcement::where('is_active', true)
                                ->whereIn('target_audience', $target)
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        @forelse($notices as $n)
                            @php
                                $isRead = in_array($n->id, $readIds);
                            @endphp
                            <li class="py-2 border-bottom {{ $isRead ? 'opacity-75' : 'bg-light bg-opacity-50' }}">
                                <a class="dropdown-item px-2 py-1" href="{{ route('dashboard') }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-semibold text-truncate small text-dark" style="max-width: 220px;">
                                            @if(!$isRead)
                                                <span class="badge bg-primary p-1 me-1">New</span>
                                            @endif
                                            {{ $n->title }}
                                        </div>
                                    </div>
                                    <div class="text-muted small text-truncate" style="font-size: 0.78rem;">
                                        {{ Str::limit($n->content, 55) }}
                                    </div>
                                    <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.7rem;">
                                        {{ $n->created_at->diffForHumans() }}
                                    </span>
                                </a>
                            </li>
                        @empty
                            <li class="text-center text-muted small py-3">No notices available.</li>
                        @endforelse
                    </ul>
                </div>

                <span class="badge bg-primary px-3 py-2">{{ Auth::user()->roles->first()?->display_name }}</span>
            </div>
        </div>

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Notice:</strong> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>