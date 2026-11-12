@extends('layouts.app')
@section('title', 'Timetables & Lecture Halls')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">University Timetable & Classroom Scheduling</h4>
        <p class="text-muted mb-0">Schedule lecture sessions with automated room and instructor clash detection.</p>
    </div>
    <div class="btn-group">
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClassroomModal">
            <i class="bi bi-building-add me-1"></i> Add Classroom / Hall
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
            <i class="bi bi-calendar-plus me-1"></i> Schedule Class Session
        </button>
    </div>
</div>

@if($errors->has('schedule'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <strong>Scheduling Blocked:</strong> {{ $errors->first('schedule') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Master Scheduled Timetable -->
    <div class="col-lg-8">
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3">Master Lecture Timetable (Active Term)</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Day</th>
                            <th>Time Interval</th>
                            <th>Course & Section</th>
                            <th>Classroom / Hall</th>
                            <th>Assigned Lecturer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timetables as $t)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $t->day_of_week }}</span></td>
                                <td><strong>{{ \Carbon\Carbon::parse($t->start_time)->format('h:i A') }} &rarr; {{ \Carbon\Carbon::parse($t->end_time)->format('h:i A') }}</strong></td>
                                <td>
                                    <strong>{{ $t->courseOffering->course->code }}</strong>
                                    <div class="small text-muted">{{ $t->courseOffering->section }}</div>
                                </td>
                                <td>
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    {{ $t->classroom->building }} ({{ $t->classroom->room_number }})
                                </td>
                                <td>
                                    <small>{{ $t->courseOffering->instructors->first()?->name ?? 'Unassigned' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No lecture sessions scheduled yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Classroom Directory -->
    <div class="col-lg-4">
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3">Campus Lecture Halls</h5>
            <ul class="list-group list-group-flush">
                @forelse($classrooms as $c)
                    <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $c->building }}</strong>
                            <div class="small text-muted">Room: {{ $c->room_number }}</div>
                        </div>
                        <span class="badge bg-info text-dark">Seats: {{ $c->capacity }}</span>
                    </li>
                @empty
                    <li class="text-muted small py-3 text-center">No classrooms added yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Modal 1: Add Classroom -->
<div class="modal fade" id="addClassroomModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('academic.timetables.classroom.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Classroom / Lecture Hall</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Building Block *</label>
                    <input type="text" name="building" class="form-control" placeholder="e.g. Technology Block A or Main Hall" required>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Room Number *</label>
                        <input type="text" name="room_number" class="form-control" placeholder="e.g. R-102 or Lab 3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Seat Capacity *</label>
                        <input type="number" name="capacity" class="form-control" value="60" min="10" max="500" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Classroom</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Schedule Class Session -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('academic.timetables.schedule.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Schedule Lecture Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Course Offering & Section *</label>
                    <select name="course_offering_id" class="form-select" required>
                        @foreach($offerings as $o)
                            <option value="{{ $o->id }}">
                                {{ $o->course->code }} - {{ $o->course->title }} ({{ $o->section }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Classroom / Lecture Hall *</label>
                    <select name="classroom_id" class="form-select" required>
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->building }} - {{ $c->room_number }} (Capacity: {{ $c->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Day of the Week *</label>
                    <select name="day_of_week" class="form-select" required>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Lecture Start Time *</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Lecture End Time *</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Schedule & Check Conflicts</button>
            </div>
        </form>
    </div>
</div>
@endsection