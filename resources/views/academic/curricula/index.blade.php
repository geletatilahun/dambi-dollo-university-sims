@extends('layouts.app')
@section('title', 'Curriculum Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Accredited Program Curricula</h4>
        <p class="text-muted mb-0">Manage degree requirements, course breakdowns, and prerequisite sequences.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCurriculumModal">
        <i class="bi bi-plus-circle me-1"></i> New Curriculum Version
    </button>
</div>

<div class="card p-4 shadow-sm bg-white border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Program Code</th>
                    <th>Degree Program</th>
                    <th>Version</th>
                    <th>Curriculum Title</th>
                    <th>Credits Required</th>
                    <th>Courses Mapped</th>
                    <th class="text-end">Roadmap</th>
                </tr>
            </thead>
            <tbody>
                @forelse($curricula as $c)
                    <tr>
                        <td><strong class="text-primary">{{ $c->program->code }}</strong></td>
                        <td>{{ $c->program->name }}</td>
                        <td><span class="badge bg-secondary">{{ $c->version }}</span></td>
                        <td>{{ $c->title }}</td>
                        <td>{{ $c->total_credits_required }} Cr.Hr</td>
                        <td><span class="badge bg-info text-dark">{{ $c->curriculumCourses->count() }} Courses</span></td>
                        <td class="text-end">
                            <a href="{{ route('academic.curricula.show', $c->id) }}" class="btn btn-sm btn-outline-primary">
                                View Roadmap &rarr;
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No curricula registered yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Create Curriculum -->
<div class="modal fade" id="createCurriculumModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('academic.curricula.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create Program Curriculum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Target Degree Program *</label>
                    <select name="program_id" class="form-select" required>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Version Identifier *</label>
                        <input type="text" name="version" class="form-control" placeholder="e.g. 2026-Harmonized" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Total Required Credits *</label>
                        <input type="number" step="0.5" name="total_credits_required" class="form-control" value="142" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Curriculum Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. CS Harmonized Modular Curriculum" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Curriculum</button>
            </div>
        </form>
    </div>
</div>
@endsection