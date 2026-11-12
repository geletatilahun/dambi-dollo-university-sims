@extends('layouts.app')
@section('title', 'Advisee Progress Dossier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">
            Advisee Academic File: <span class="text-primary">{{ $student->first_name }} {{ $student->last_name }}</span>
        </h4>
        <p class="text-muted mb-0 small">Student ID: <strong>{{ $student->student_id_number }}</strong> | Program: <strong>{{ $student->program->name }}</strong></p>
    </div>
    <div class="btn-group">
        <a href="{{ route('advisor.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Roster</a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newInterventionModal">
            <i class="bi bi-chat-left-text-fill me-1"></i> Record Advising Session
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Academic Progression History -->
    <div class="col-lg-5">
        <div class="card p-4 shadow-sm bg-white border-0 mb-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Academic Standing Trajectory</h5>
            <ul class="list-group list-group-flush small">
                @forelse($student->semesterResults as $res)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <div>
                            <strong>{{ $res->semester->name }}</strong>
                            <div class="text-muted" style="font-size: 0.78rem;">Standing: {{ $res->academic_standing }}</div>
                        </div>
                        <div class="text-end">
                            <div>SGPA: <strong>{{ number_format($res->sgpa, 2) }}</strong></div>
                            <div>CGPA: <strong class="text-success">{{ number_format($res->cgpa, 2) }}</strong></div>
                        </div>
                    </li>
                @empty
                    <li class="text-muted small py-2">No finalized semester results recorded yet.</li>
                @endforelse
            </ul>
        </div>

        <!-- Currently Enrolled Courses -->
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Current Semester Registrations</h5>
            @php
                $latestReg = $student->registrations->last();
            @endphp
            @if($latestReg)
                <ul class="list-group list-group-flush small">
                    @foreach($latestReg->registrationCourses->where('item_status', 'Registered') as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span><strong>{{ $item->courseOffering->course->code }}</strong> - {{ $item->courseOffering->course->title }}</span>
                            <span class="badge bg-light text-dark border">{{ $item->courseOffering->course->credit_hours }} Cr</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted small">No active course registrations.</p>
            @endif
        </div>
    </div>

    <!-- Advising Session Intervention Logs -->
    <div class="col-lg-7">
        <div class="card p-4 shadow-sm bg-white border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h5 class="fw-bold mb-0">Documented Advising Interventions</h5>
                <span class="badge bg-secondary">{{ $student->advisingNotes->count() }} Sessions</span>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($student->advisingNotes as $note)
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ $note->intervention_type }}</span>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($note->session_date)->format('M d, Y') }}
                            </small>
                        </div>
                        <div class="mb-2">
                            <strong class="small text-secondary text-uppercase d-block">Observations / Academic Concerns:</strong>
                            <p class="small mb-1 text-dark">{{ $note->observations }}</p>
                        </div>
                        <div>
                            <strong class="small text-success text-uppercase d-block">Action Plan & Recommendations:</strong>
                            <p class="small mb-1 text-dark">{{ $note->recommendations }}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top text-muted small" style="font-size: 0.78rem;">
                            <span>Logged by: <strong>{{ $note->advisor->name }}</strong></span>
                            @if($note->is_confidential)
                                <span class="badge bg-dark"><i class="bi bi-shield-lock me-1"></i>Advisor Confidential</span>
                            @else
                                <span class="badge bg-light text-dark border"><i class="bi bi-eye me-1"></i>Visible to Student</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>
                        No counseling interventions logged for this student yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal: Log Advising Session -->
<div class="modal fade" id="newInterventionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('advisor.notes.store', $student->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Log Advising Intervention</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Session Date *</label>
                        <input type="date" name="session_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Intervention Category *</label>
                        <select name="intervention_type" class="form-select" required>
                            <option value="Academic Performance Review">Performance Review</option>
                            <option value="Probation & Warning Counseling">Probation / Warning Counseling</option>
                            <option value="Course Load Adjustment">Course Load Reduction</option>
                            <option value="Study Skills & Tutoring Referral">Tutoring Referral</option>
                            <option value="Career & Personal Guidance">Career Guidance</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Observations & Discussion Summary *</label>
                    <textarea name="observations" class="form-control" rows="3" placeholder="Discussed student struggles in calculus, attendance lapses..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Action Plan & Recommendations *</label>
                    <textarea name="recommendations" class="form-control" rows="3" placeholder="Recommended dropping 1 elective, joining peer tutoring twice weekly..." required></textarea>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_confidential" value="1" id="confCheck">
                    <label class="form-check-label small" for="confCheck">
                        Mark as Confidential (Internal faculty note, hidden from student portal)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Advising Record</button>
            </div>
        </form>
    </div>
</div>
@endsection