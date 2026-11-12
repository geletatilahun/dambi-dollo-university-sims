@extends('layouts.app')
@section('title', 'Attendance Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Attendance: {{ $offering->course->code }} - {{ $offering->course->title }}</h4>
        <p class="text-muted mb-0">
            Section: <span class="badge bg-secondary">{{ $offering->section }}</span> | Total Lectures Conducted: <strong>{{ $totalSessions }}</strong>
        </p>
    </div>
    <div class="btn-group">
        <a href="{{ route('instructor.offerings.show', $offering->id) }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Marks</a>
        <a href="{{ route('instructor.attendance.create', $offering->id) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i> Take Attendance for Today
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Student Summary Table -->
    <div class="col-lg-8">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Student Cumulative Attendance Status</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th class="text-center">Attended</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Rate (%)</th>
                            <th>Exam Eligibility</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrolledStudents as $item)
                            <tr>
                                <td><strong>{{ $item['student']->student_id_number }}</strong></td>
                                <td>{{ $item['student']->first_name }} {{ $item['student']->last_name }}</td>
                                <td class="text-center text-success fw-bold">{{ $item['present_count'] }}</td>
                                <td class="text-center text-danger fw-bold">{{ $item['absent_count'] }}</td>
                                <td class="text-center">
                                    <span class="fw-bold {{ $item['percentage'] < 80 ? 'text-danger' : 'text-dark' }}">
                                        {{ $item['percentage'] }}%
                                    </span>
                                </td>
                                <td>
                                    @if($item['percentage'] >= 80)
                                        <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Eligible</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Low Attendance (<80%)</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No enrolled students in this section.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Past Lecture Sessions Sidebar -->
    <div class="col-lg-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Conducted Lectures</h5>
            <ul class="list-group list-group-flush">
                @forelse($sessions as $session)
                    <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</strong>
                            <div class="small text-muted">{{ $session->topic }}</div>
                        </div>
                        <span class="badge bg-light text-dark border">
                            {{ $session->records->where('status', 'Present')->count() }} Present
                        </span>
                    </li>
                @empty
                    <li class="text-muted small py-2">No attendance sessions recorded yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection