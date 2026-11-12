@extends('layouts.app')
@section('title', 'Advisee Dossier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Advisee: {{ $student->first_name }} {{ $student->last_name }}</h4>
        <p class="text-muted mb-0">Student ID: <strong>{{ $student->student_id_number }}</strong> | Program: <strong>{{ $student->program->name }}</strong></p>
    </div>
    <a href="{{ route('academic.advising.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Advisees</a>
</div>

<div class="row g-4">
    <!-- Log Intervention Form -->
    <div class="col-lg-5">
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2 text-primary"></i>Record Advising Intervention</h5>
            
            <form action="{{ route('academic.advising.note.store', $student->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Intervention Type *</label>
                    <select name="intervention_type" class="form-select" required>
                        <option value="Academic Warning Counseling">Academic Warning Counseling</option>
                        <option value="Course Load Reduction Recommendation">Course Load Reduction Recommendation</option>
                        <option value="Career & Graduate Guidance">Career & Graduate Guidance</option>
                        <option value="Personal / Welfare Support">Personal / Welfare Support</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Confidential Advising Notes *</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Detail the student discussion, recommendations, and action plan..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i> Save Advising Record
                </button>
            </form>
        </div>
    </div>

    <!-- Intervention History -->
    <div class="col-lg-7">
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2 text-info"></i>Counseling & Intervention History</h5>
            <div class="list-group list-group-flush">
                @forelse($student->advisingNotes as $note)
                    <div class="list-group-item px-0 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-secondary">{{ $note->intervention_type }}</span>
                            <small class="text-muted">{{ $note->created_at->format('M d, Y - H:i') }}</small>
                        </div>
                        <p class="mb-1 text-dark small" style="white-space: pre-line;">{{ $note->notes }}</p>
                        <small class="text-muted">Recorded by Advisor: <strong>{{ $note->advisor->name }}</strong></small>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">No advising interventions recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection