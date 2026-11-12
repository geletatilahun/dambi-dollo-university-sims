@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    <!-- ================= UNIVERSAL WELCOME HEADER ================= -->
    <div class="col-12">
        <div class="card p-4 bg-white border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Welcome back, {{ $user->name }}!</h4>
                    <p class="text-muted mb-0 small">
                        Primary System Role: <span class="badge bg-primary px-2 py-1">{{ $primaryRole }}</span>
                        <span class="mx-2">•</span>
                        Academic Term: <strong>2026/2027 (Semester 1)</strong>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-secondary border px-3 py-2 small">
                        <i class="bi bi-clock me-1"></i> {{ date('l, M d, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 1. COLLEGE DEAN VIEW ================= -->
    @if($primaryRole === 'Dean')
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-primary shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">College Students</div>
                <div class="fs-3 fw-bold text-dark mt-1">{{ $metrics['totalCollegeStudents'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Enrolled across departments</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-info shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Departments</div>
                <div class="fs-3 fw-bold text-info mt-1">{{ count($metrics['departments'] ?? []) }}</div>
                <div class="small text-muted mt-1">Under college supervision</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-success shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Degree Programs</div>
                <div class="fs-3 fw-bold text-success mt-1">{{ $metrics['activeProgramsCount'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Accredited curricula</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-warning shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Pending Grades</div>
                <div class="fs-3 fw-bold text-warning mt-1">{{ $metrics['pendingEndorsements'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Awaiting sign-off</div>
            </div>
        </div>

        <!-- Dean Action Shortcuts -->
        <div class="col-12">
            <div class="card p-4 shadow-sm bg-white border-0">
                <h5 class="fw-bold mb-3">College Dean Supervisory Actions</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.structure.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-building me-1"></i> Manage College Departments
                    </a>
                    <a href="{{ route('academic.grades.department') }}" class="btn btn-outline-warning text-dark">
                        <i class="bi bi-check2-circle me-1"></i> Review Grade Endorsements
                        @if(($metrics['pendingEndorsements'] ?? 0) > 0)
                            <span class="badge bg-danger ms-1">{{ $metrics['pendingEndorsements'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('academic.grades.changes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-repeat me-1"></i> Grade Changes
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-bar-chart-line-fill me-1"></i> College Analytics & Reports
                    </a>
                </div>
            </div>
        </div>

    <!-- ================= 2. DEPARTMENT HEAD VIEW ================= -->
    @elseif($primaryRole === 'DepartmentHead')
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-primary shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Department Majors</div>
                <div class="fs-3 fw-bold text-dark mt-1">{{ $metrics['totalStudents'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Active registered students</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-warning shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Add/Drop Requests</div>
                <div class="fs-3 fw-bold text-warning mt-1">{{ $metrics['pendingAddDrop'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Awaiting head decision</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-danger shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Pending Grade Sheets</div>
                <div class="fs-3 fw-bold text-danger mt-1">{{ $metrics['pendingEndorsements'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Submitted by lecturers</div>
            </div>
        </div>

        <!-- Department Head Quick Actions -->
        <div class="col-12">
            <div class="card p-4 shadow-sm bg-white border-0">
                <h5 class="fw-bold mb-3">Department Academic Management</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('academic.catalog.offerings') }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-plus me-1"></i> Schedule Course Offerings
                    </a>
                    <a href="{{ route('academic.adddrop.index') }}" class="btn btn-outline-warning text-dark">
                        <i class="bi bi-arrow-left-right me-1"></i> Add/Drop Requests
                        @if(($metrics['pendingAddDrop'] ?? 0) > 0)
                            <span class="badge bg-danger ms-1">{{ $metrics['pendingAddDrop'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('academic.grades.department') }}" class="btn btn-outline-success">
                        <i class="bi bi-check2-circle me-1"></i> Endorse Grade Sheets
                        @if(($metrics['pendingEndorsements'] ?? 0) > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $metrics['pendingEndorsements'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('academic.advisors.assign') }}" class="btn btn-outline-info">
                        <i class="bi bi-person-gear me-1"></i> Assign Advisors
                    </a>
                    <a href="{{ route('academic.curricula.index') }}" class="btn btn-outline-dark">
                        <i class="bi bi-mortarboard me-1"></i> Curricula Roadmap
                    </a>
                    <a href="{{ route('academic.timetables.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clock-history me-1"></i> Lecture Timetables
                    </a>
                </div>
            </div>
        </div>

    <!-- ================= 3. STUDENT PORTAL VIEW ================= -->
    @elseif($primaryRole === 'Student')
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-primary shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Student ID Number</div>
                <div class="fs-4 fw-bold text-dark mt-1">{{ $metrics['student']?->student_id_number ?? 'Pending ID' }}</div>
                <div class="small text-muted mt-1">{{ $metrics['student']?->program?->name ?? 'Enrolled Student' }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-success shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Cumulative GPA (CGPA)</div>
                <div class="fs-4 fw-bold text-success mt-1">{{ number_format($metrics['latestResult']?->cgpa ?? 0.00, 2) }}</div>
                <div class="small text-muted mt-1">Semester GPA: <strong>{{ number_format($metrics['latestResult']?->sgpa ?? 0.00, 2) }}</strong></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-info shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Academic Standing</div>
                <div class="fs-4 fw-bold text-info mt-1">{{ $metrics['latestResult']?->academic_standing ?? 'Good Standing' }}</div>
                <div class="small text-muted mt-1">Active Status: <span class="badge bg-success">{{ $metrics['student']?->status ?? 'Active' }}</span></div>
            </div>
        </div>

        <!-- Student Quick Actions -->
        <div class="col-12">
            <div class="card p-4 shadow-sm bg-white border-0">
                <h5 class="fw-bold mb-3">Student Academic Services</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('student.registration') }}" class="btn btn-outline-primary">
                        <i class="bi bi-journal-plus me-1"></i> Course Registration
                    </a>
                    <a href="{{ route('student.adddrop.index') }}" class="btn btn-outline-warning text-dark">
                        <i class="bi bi-arrow-left-right me-1"></i> Course Add / Drop
                    </a>
                    <a href="{{ route('schedule.my') }}" class="btn btn-outline-info">
                        <i class="bi bi-calendar-week me-1"></i> Weekly Timetable
                    </a>
                    <a href="{{ route('student.grades') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark-text me-1"></i> View Semester Grades
                    </a>
                    <a href="{{ route('student.transcript') }}" class="btn btn-outline-dark" target="_blank">
                        <i class="bi bi-printer me-1"></i> Print Official Transcript
                    </a>
                    <a href="{{ route('student.services.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-check me-1"></i> Documents & Clearance
                    </a>
                    <a href="{{ route('student.advisor') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person-badge me-1"></i> My Academic Advisor
                    </a>
                </div>
            </div>
        </div>

    <!-- ================= 4. INSTRUCTOR PORTAL VIEW ================= -->
    @elseif($primaryRole === 'Instructor')
        <div class="col-12">
            <div class="card p-4 shadow-sm bg-white border-0">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">Your Assigned Teaching Course Offerings</h5>
                        <p class="text-muted small mb-0">Record continuous assessments, input grades, and manage attendance.</p>
                    </div>
                    <a href="{{ route('schedule.my') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-calendar-week me-1"></i> View Teaching Timetable
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credit Hours</th>
                                <th>Section</th>
                                <th>Class Capacity</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metrics['offerings'] ?? [] as $offering)
                                <tr>
                                    <td><strong class="text-primary">{{ $offering->course->code }}</strong></td>
                                    <td>{{ $offering->course->title }}</td>
                                    <td>{{ $offering->course->credit_hours }} Cr.Hr</td>
                                    <td><span class="badge bg-secondary">{{ $offering->section }}</span></td>
                                    <td>{{ $offering->capacity }} Students</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('instructor.offerings.show', $offering->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-square me-1"></i> Enter Marks
                                            </a>
                                            <a href="{{ route('instructor.attendance.index', $offering->id) }}" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-calendar-check me-1"></i> Attendance
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                        No courses currently assigned to your instructor profile.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- ================= 5. REGISTRAR / SUPERADMIN VIEW ================= -->
    @elseif(in_array($primaryRole, ['Registrar', 'SuperAdmin', 'AdmissionOfficer']))
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-primary shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Total Registered Students</div>
                <div class="fs-3 fw-bold text-dark mt-1">{{ $metrics['totalStudents'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Cumulative enrolled student records</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-success shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Active Enrolled Cohort</div>
                <div class="fs-3 fw-bold text-success mt-1">{{ $metrics['activeStudents'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Active status in current semester</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-warning shadow-sm bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Grade Sheets Pending Publication</div>
                <div class="fs-3 fw-bold text-warning mt-1">{{ $metrics['pendingGrades'] ?? 0 }}</div>
                <div class="small text-muted mt-1">Awaiting official approval</div>
            </div>
        </div>

        <!-- Administrative Quick Links -->
        <div class="col-12">
            <div class="card p-4 shadow-sm bg-white border-0">
                <h5 class="fw-bold mb-3">Registrar Operations & Quick Actions</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('registrar.students.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-people me-1"></i> Student Registry
                    </a>
                    <a href="{{ route('registrar.import.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Batch Student Import
                    </a>
                    <a href="{{ route('admissions.queue') }}" class="btn btn-outline-info">
                        <i class="bi bi-inbox-fill me-1"></i> Admissions Queue
                    </a>
                    <a href="{{ route('academic.grades.registrar') }}" class="btn btn-outline-warning text-dark">
                        <i class="bi bi-journal-check me-1"></i> Publish Grades
                    </a>
                    <a href="{{ route('registrar.documents.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Document Requests
                    </a>
                    <a href="{{ route('registrar.clearance.index') }}" class="btn btn-outline-dark">
                        <i class="bi bi-mortarboard me-1"></i> Graduation Clearance
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-bar-chart-line-fill me-1"></i> Analytics & Reports
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= OFFICIAL UNIVERSITY ANNOUNCEMENTS & BULLETINS ================= -->
    <div class="col-12">
        <div class="card p-4 shadow-sm bg-white border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-bell-fill text-warning me-2"></i>Official University Bulletins & Notices
                </h5>
                <span class="badge bg-light text-secondary border">Active Bulletins</span>
            </div>

            <div class="row g-3">
                @forelse($announcements ?? [] as $notice)
                    <div class="col-12">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap">
                                <h6 class="fw-bold text-primary mb-0">{{ $notice->title }}</h6>
                                <small class="text-muted">{{ $notice->created_at->format('M d, Y - h:i A') }}</small>
                            </div>
                            <p class="mb-1 text-dark small" style="white-space: pre-line;">{{ $notice->content }}</p>
                            <div class="small text-muted mt-2 pt-1 border-top">
                                <i class="bi bi-person-fill"></i> Issued by: <strong>{{ $notice->author->name }}</strong>
                                <span class="badge bg-secondary ms-2">Audience: {{ $notice->target_audience }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">
                        <i class="bi bi-chat-left-dots fs-3 d-block mb-1 text-secondary"></i>
                        No active announcements posted for your role at this time.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection