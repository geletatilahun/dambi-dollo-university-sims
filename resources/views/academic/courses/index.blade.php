@extends('layouts.app')
@section('title', 'Course Catalog')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">University Course Catalog</h4>
            <p class="text-muted mb-0">Master catalog of accredited courses, credit hours, and ECTS.</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Course
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
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Department</th>
                <th>Credit Hours</th>
                <th>ECTS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $c)
                <tr>
                    <td><strong class="text-primary">{{ $c->code }}</strong></td>
                    <td>{{ $c->title }}</td>
                    <td>{{ $c->department->name }}</td>
                    <td>{{ $c->credit_hours }} Cr.Hr</td>
                    <td>{{ $c->ects }} ECTS</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No courses registered in catalog yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $courses->links('pagination::bootstrap-5') }}
</div>

<!-- Modal: Add Course -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('academic.catalog.courses.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Accredited Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Managing Department *</label>
                    <select name="department_id" class="form-select" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Course Code *</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. CoSc2041" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Credit Hours *</label>
                        <input type="number" step="0.5" name="credit_hours" class="form-control" placeholder="3.0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Course Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Computer Networks and Security" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">ECTS Equivalent *</label>
                    <input type="number" step="0.5" name="ects" class="form-control" placeholder="5.0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save to Catalog</button>
            </div>
        </form>
    </div>
</div>
@endsection