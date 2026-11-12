@extends('layouts.app')
@section('title', 'Take Attendance')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Record Class Attendance</h4>
            <p class="text-muted mb-0">Course: <strong>{{ $offering->course->code }}</strong> - Section {{ $offering->section }}</p>
        </div>
        <a href="{{ route('instructor.attendance.index', $offering->id) }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Attendance List</a>
    </div>

    <form action="{{ route('instructor.attendance.store', $offering->id) }}" method="POST">
        @csrf
        
        <div class="row g-3 bg-light p-3 rounded mb-4 border">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Lecture Date *</label>
                <input type="date" name="session_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Lecture Topic / Notes</label>
                <input type="text" name="topic" class="form-control" placeholder="e.g. Introduction to Relational Databases">
            </div>
        </div>

        <h5 class="fw-bold mb-2">Class Roll Call</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th class="text-center" width="380">Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        <tr>
                            <td><strong>{{ $st->student_id_number }}</strong></td>
                            <td>{{ $st->first_name }} {{ $st->last_name }}</td>
                            <td>
                                <div class="d-flex justify-content-around">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[{{ $st->id }}]" id="p_{{ $st->id }}" value="Present" checked>
                                        <label class="form-check-label text-success fw-bold" for="p_{{ $st->id }}">Present</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[{{ $st->id }}]" id="a_{{ $st->id }}" value="Absent">
                                        <label class="form-check-label text-danger fw-bold" for="a_{{ $st->id }}">Absent</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[{{ $st->id }}]" id="l_{{ $st->id }}" value="Late">
                                        <label class="form-check-label text-warning fw-bold" for="l_{{ $st->id }}">Late</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[{{ $st->id }}]" id="e_{{ $st->id }}" value="Excused">
                                        <label class="form-check-label text-info fw-bold" for="e_{{ $st->id }}">Excused</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No enrolled students in this section.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->count() > 0)
            <div class="mt-3">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check2-circle me-1"></i> Submit & Save Roll Call
                </button>
            </div>
        @endif
    </form>
</div>
@endsection