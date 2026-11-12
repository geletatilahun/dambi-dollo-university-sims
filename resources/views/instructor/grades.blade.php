@extends('layouts.app')
@section('title', 'Enter Continuous Marks')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 border-bottom pb-3">
        <div>
            <h4 class="fw-bold mb-1">
                Evaluation Scheme: <span class="text-primary">{{ $offering->course->code }}</span> - {{ $offering->course->title }}
            </h4>
            <p class="text-muted mb-0 small">
                Section: <span class="badge bg-secondary me-2">{{ $offering->section }}</span>
                Credit Hours: <strong>{{ $offering->course->credit_hours }} Cr.Hr</strong>
                <span class="mx-2">•</span>
                Total Assessment Weight: 
                <span class="badge {{ $totalWeight == 100 ? 'bg-success' : 'bg-warning text-dark' }}">
                    {{ $totalWeight }}% / 100%
                </span>
            </p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#configWeightModal">
                <i class="bi bi-sliders me-1"></i> Add Component
            </button>
            <a href="{{ route('instructor.attendance.index', $offering->id) }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-calendar-check me-1"></i> Attendance
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                &larr; Dashboard
            </a>
        </div>
    </div>

    @if($errors->has('weight'))
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first('weight') }}
        </div>
    @endif

    <!-- FORM: Multi-Column Continuous Assessment Input -->
    <form action="{{ route('instructor.marks.update', $offering->id) }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table table-bordered table-hover mt-2 align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th class="text-start" width="14%">Student ID</th>
                        <th class="text-start" width="22%">Student Name</th>
                        @foreach($components as $comp)
                            <th width="{{ round(40 / max(1, $components->count())) }}%">
                                {{ $comp->title }}
                                <div class="badge bg-light text-dark border small mt-1">Max: {{ (int)$comp->max_weight }}%</div>
                            </th>
                        @endforeach
                        <th width="12%">Total (100%)</th>
                        <th width="12%">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offering->registrationCourses as $item)
                        @php
                            $marksMap = $item->assessmentMarks->pluck('score', 'assessment_component_id');
                        @endphp
                        <tr>
                            <td class="text-start"><strong>{{ $item->courseRegistration->student->student_id_number }}</strong></td>
                            <td class="text-start">{{ $item->courseRegistration->student->first_name }} {{ $item->courseRegistration->student->last_name }}</td>
                            
                            <!-- Dynamic Component Score Inputs -->
                            @foreach($components as $comp)
                                <td>
                                    <input 
                                        type="number" 
                                        step="0.1" 
                                        min="0" 
                                        max="{{ $comp->max_weight }}" 
                                        name="scores[{{ $item->id }}][{{ $comp->id }}]" 
                                        class="form-control form-control-sm text-center mx-auto" 
                                        style="max-width: 90px;"
                                        value="{{ $marksMap->get($comp->id) }}"
                                        placeholder="0-{{ (int)$comp->max_weight }}"
                                    >
                                </td>
                            @endforeach

                            <!-- Computed Total -->
                            <td>
                                <strong class="fs-6 {{ ($item->final_score ?? 0) < 50 ? 'text-danger' : 'text-success' }}">
                                    {{ $item->final_score !== null ? number_format($item->final_score, 1) : '-' }}
                                </strong>
                            </td>

                            <!-- Letter Grade -->
                            <td>
                                @if($item->grade_letter)
                                    <span class="badge 
                                        {{ in_array($item->grade_letter, ['A+', 'A', 'A-']) ? 'bg-success' : '' }}
                                        {{ in_array($item->grade_letter, ['B+', 'B', 'B-']) ? 'bg-primary' : '' }}
                                        {{ in_array($item->grade_letter, ['C+', 'C']) ? 'bg-info text-dark' : '' }}
                                        {{ in_array($item->grade_letter, ['C-', 'D']) ? 'bg-warning text-dark' : '' }}
                                        {{ in_array($item->grade_letter, ['F', 'Fx']) ? 'bg-danger' : '' }}
                                        fs-6 px-2 py-1
                                    ">
                                        {{ $item->grade_letter }}
                                    </span>
                                @else
                                    <span class="text-muted small"><em>-</em></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + $components->count() }}" class="text-center text-muted py-4">
                                No registered students in this section.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($offering->registrationCourses->count() > 0)
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-calculator me-1"></i> Save Marks & Calculate Grades
                </button>
            </div>
        @endif
    </form>

    <!-- Submission to Department Head -->
    @if($offering->registrationCourses->count() > 0)
        <div class="card bg-light border p-3 mt-4 rounded">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">Submit Grade Sheet for Department Endorsement</h6>
                    <p class="text-muted small mb-0">
                        Locks marks and forwards the finalized grades to the Department Head and Registrar.
                    </p>
                </div>
                <form action="{{ route('academic.grades.submit', $offering->id) }}" method="POST">
                    @csrf
                    <button 
                        type="submit" 
                        class="btn btn-success fw-semibold" 
                        onclick="return confirm('Submit completed mark sheet to Department Head?')"
                    >
                        <i class="bi bi-send-check me-1"></i> Submit to Department Head
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

<!-- Modal: Add Evaluation Component -->
<div class="modal fade text-start" id="configWeightModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('instructor.components.store', $offering->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Assessment Component</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Component Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Lab Project, Quiz 2, Term Paper" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Maximum Percentage Weight (%) *</label>
                    <input type="number" step="1" name="max_weight" class="form-control" placeholder="e.g. 15" required>
                    <div class="form-text small">Current configured total: {{ $totalWeight }}%</div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="document.getElementById('resetForm').submit();">
                    Reset to 20/30/50 Standard
                </button>
                <button type="submit" class="btn btn-primary btn-sm">Add Component</button>
            </div>
        </form>
        <form id="resetForm" action="{{ route('instructor.components.reset', $offering->id) }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>
@endsection