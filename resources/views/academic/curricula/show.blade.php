@extends('layouts.app')
@section('title', 'Curriculum Roadmap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Roadmap: {{ $curriculum->title }} ({{ $curriculum->version }})</h4>
        <p class="text-muted mb-0">Program: <strong>{{ $curriculum->program->name }}</strong> | Target Credits: <strong>{{ $curriculum->total_credits_required }} Cr.Hr</strong></p>
    </div>
    <div class="btn-group">
        <a href="{{ route('academic.curricula.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Back</a>
        <button class="btn btn-outline-warning text-dark btn-sm" data-bs-toggle="modal" data-bs-target="#prereqModal">
            <i class="bi bi-diagram-2 me-1"></i> Set Prerequisites
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#attachCourseModal">
            <i class="bi bi-plus-circle me-1"></i> Place Course in Term
        </button>
    </div>
</div>

<div class="row g-4">
    @for($year = 1; $year <= $curriculum->program->duration_years; $year++)
        <div class="col-12">
            <div class="card p-3 shadow-sm bg-white border-0">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-mortarboard me-2 text-primary"></i>Year {{ $year }}
                </h5>

                <div class="row g-3">
                    @for($sem = 1; $sem <= 2; $sem++)
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-primary">Semester {{ $sem }}</h6>
                                    @php
                                        $termCourses = $roadmap[$year][$sem] ?? collect();
                                        $semCredits = $termCourses->sum(fn($cc) => $cc->course->credit_hours);
                                    @endphp
                                    <span class="badge bg-secondary">{{ $semCredits }} Cr.Hr</span>
                                </div>

                                <ul class="list-group list-group-flush rounded">
                                    @forelse($termCourses as $cc)
                                        <li class="list-group-item bg-white py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $cc->course->code }}</strong> - {{ $cc->course->title }}
                                                    @if($cc->course->prerequisites->count() > 0)
                                                        <div class="text-danger small" style="font-size: 0.76rem;">
                                                            <i class="bi bi-arrow-return-right me-1"></i>Prereq: 
                                                            {{ $cc->course->prerequisites->pluck('code')->join(', ') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="badge bg-light text-dark border">{{ $cc->course->credit_hours }} Cr</span>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item bg-white text-muted small py-3 text-center">
                                            No courses placed in this term yet.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endfor
</div>

<!-- Modal 1: Place Course in Year & Semester -->
<div class="modal fade" id="attachCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('academic.curricula.attach', $curriculum->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Place Course in Roadmap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Select Course from Department *</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->title }} ({{ $c->credit_hours }} Cr.Hr)</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Year of Study *</label>
                        <select name="year_level" class="form-select" required>
                            @for($y = 1; $y <= $curriculum->program->duration_years; $y++)
                                <option value="{{ $y }}">Year {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Semester Term *</label>
                        <select name="semester_term" class="form-select" required>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Place in Term</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Set Course Prerequisite Dependency -->
<div class="modal fade" id="prereqModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('academic.curricula.prerequisite.set') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Set Course Prerequisite Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Target Advanced Course *</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Must Have Passed Prerequisite Course *</label>
                    <select name="prerequisite_course_id" class="form-select" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Save Prerequisite Rule</button>
            </div>
        </form>
    </div>
</div>
@endsection