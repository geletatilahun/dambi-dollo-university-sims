@extends('layouts.app')
@section('title', 'Academic Calendar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Academic Calendar & Term Management</h4>
        <p class="text-muted mb-0">Define academic years, registration windows, and active semesters.</p>
    </div>
    <div class="btn-group">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addYearModal">
            <i class="bi bi-plus-circle me-1"></i> Add Academic Year
        </button>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSemModal">
            <i class="bi bi-calendar-plus me-1"></i> Add Semester Term
        </button>
    </div>
</div>

<div class="row g-4">
    @forelse($years as $year)
        <div class="col-12">
            <div class="card p-4 shadow-sm bg-white border-0">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-primary mb-0">
                        <i class="bi bi-calendar-event me-2"></i>Academic Year {{ $year->code }}
                    </h5>
                    <span class="small text-muted">{{ $year->start_date }} to {{ $year->end_date }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Semester Term</th>
                                <th>Duration</th>
                                <th>Registration Window</th>
                                <th>Grade Deadline</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($year->semesters as $sem)
                                <tr>
                                    <td><strong>{{ $sem->name }}</strong></td>
                                    <td>{{ $sem->start_date }} &rarr; {{ $sem->end_date }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $sem->registration_start }} to {{ $sem->registration_deadline }}</span></td>
                                    <td>{{ $sem->grade_submission_deadline }}</td>
                                    <td>
                                        <span class="badge {{ $sem->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $sem->is_active ? 'Active Term' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if(!$sem->is_active)
                                            <form action="{{ route('admin.calendar.semester.activate', $sem->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    Activate Term
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Current</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-3">No semesters configured for this academic year.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card p-4 text-center text-muted">No academic years found.</div></div>
    @endforelse
</div>

<!-- Modal 1: Add Year -->
<div class="modal fade" id="addYearModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.calendar.year.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Academic Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Year Code *</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. 2027/2028" required>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Start Date *</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">End Date *</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Year</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Add Semester -->
<div class="modal fade" id="addSemModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.calendar.semester.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Semester Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Parent Academic Year *</label>
                    <select name="academic_year_id" class="form-select" required>
                        @foreach($years as $y)
                            <option value="{{ $y->id }}">{{ $y->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Semester Term Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Semester 2 or Kiremt" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Term Start *</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Term End *</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Registration Starts *</label>
                        <input type="date" name="registration_start" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Registration Deadline *</label>
                        <input type="date" name="registration_deadline" class="form-control" required>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Add/Drop Deadline *</label>
                        <input type="date" name="add_drop_deadline" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Grade Submission Deadline *</label>
                        <input type="date" name="grade_submission_deadline" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Semester Term</button>
            </div>
        </form>
    </div>
</div>
@endsection