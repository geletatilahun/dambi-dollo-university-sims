@extends('layouts.app')
@section('title', 'My Academic Advisor')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <h4 class="fw-bold mb-1">My Academic Advisor & Guidance</h4>
        <p class="text-muted mb-0 small">Your designated faculty mentor for academic progression, course selection, and university support.</p>
    </div>

    <!-- Advisor Profile Card -->
    <div class="col-lg-4">
        <div class="card p-4 shadow-sm bg-white border-0 text-center">
            @if($student->advisor)
                <div class="mb-3">
                    <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $student->advisor->name }}</h5>
                <p class="text-muted small mb-3">Academic Advisor</p>
                <div class="bg-light p-3 rounded text-start small">
                    <div class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i><strong>Email:</strong> {{ $student->advisor->email }}</div>
                    <div><i class="bi bi-building me-2 text-primary"></i><strong>Department:</strong> {{ $student->program->department->name }}</div>
                </div>
            @else
                <div class="py-4">
                    <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                    <h6 class="fw-bold text-muted mt-2">No Advisor Assigned Yet</h6>
                    <p class="small text-muted">Your Department Head will assign your academic advisor shortly.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recommendations & Counseling History -->
    <div class="col-lg-8">
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Advisor Recommendations & Action Plans</h5>
            <div class="d-flex flex-column gap-3">
                @forelse($student->advisingNotes as $note)
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary">{{ $note->intervention_type }}</span>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($note->session_date)->format('M d, Y') }}</small>
                        </div>
                        <div class="mt-2">
                            <strong class="small text-success text-uppercase d-block">Recommended Action Plan:</strong>
                            <p class="small mb-0 text-dark">{{ $note->recommendations }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-clipboard-check fs-2 d-block mb-2"></i>
                        No advising notes recorded yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection