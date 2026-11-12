@extends('layouts.app')
@section('title', 'Semester Course Offerings')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Semester Course Offerings</h4>
            <p class="text-muted mb-0">
                Active Term: <span class="badge bg-success">{{ $activeSemester?->name ?? 'No active semester' }}</span>
            </p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createOfferingModal">
            <i class="bi bi-plus-circle me-1"></i> Open New Section
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <table class="table table-hover align-middle mt-2">
        <thead class="table-light">
            <tr>
                <th>Course</th>
                <th>Section</th>
                <th>Capacity</th>
                <th>Assigned Instructor</th>
                <th>Semester Term</th>
            </tr>
        </thead>
        <tbody>
            @forelse($offerings as $o)
                <tr>
                    <td><strong>{{ $o->course->code }}</strong> - {{ $o->course->title }}</td>
                    <td><span class="badge bg-secondary">{{ $o->section }}</span></td>
                    <td>{{ $o->capacity }} Students</td>
                    <td>
                        @if($o->instructors->count() > 0)
                            <span class="text-success"><i class="bi bi-person-check-fill me-1"></i>{{ $o->instructors->first()->name }}</span>
                        @else
                            <span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Unassigned</span>
                        @endif
                    </td>
                    <td>{{ $o->semester->name }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No course offerings scheduled for this term.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $offerings->links('pagination::bootstrap-5') }}
</div>

<!-- Modal: Create Offering -->
<div class="modal fade" id="createOfferingModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('academic.catalog.offerings.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Schedule Course Offering & Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Select Course from Catalog *</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->title }} ({{ $c->credit_hours }} Cr.Hr)</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Academic Semester *</label>
                    <select name="semester_id" class="form-select" required>
                        @foreach($semesters as $s)
                            <option value="{{ $s->id }}" {{ $s->is_active ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->is_active ? 'Active' : 'Inactive' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Section Name *</label>
                        <input type="text" name="section" class="form-control" placeholder="e.g. Sec-B or R-2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Max Student Capacity *</label>
                        <input type="number" name="capacity" class="form-control" value="50" min="5" max="200" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Assign Instructor (Lecturer)</label>
                    <select name="instructor_id" class="form-select">
                        <option value="">-- Assign Later --</option>
                        @foreach($instructors as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }} ({{ $inst->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Schedule Offering</button>
            </div>
        </form>
    </div>
</div>
@endsection