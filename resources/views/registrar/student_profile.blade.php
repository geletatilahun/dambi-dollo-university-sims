@extends('layouts.app')
@section('title', 'Manage Student Dossier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Student Academic Dossier & Lifecycle</h4>
        <p class="text-muted mb-0">Student ID: <strong class="text-primary">{{ $student->student_id_number }}</strong></p>
    </div>
    <div class="btn-group">
        <a href="{{ route('registrar.students.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Registry</a>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
            <i class="bi bi-person-gear me-1"></i> Update Status
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transferModal">
            <i class="bi bi-arrow-left-right me-1"></i> Transfer Program
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Student Summary Card -->
    <div class="col-lg-4">
        <div class="card p-4 shadow-sm bg-white border-0">
            <div class="text-center mb-3">
                <i class="bi bi-person-badge-fill text-primary" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold mb-0 mt-1">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</h5>
                <span class="badge 
                    {{ $student->status === 'Active' ? 'bg-success' : '' }}
                    {{ $student->status === 'Suspended' ? 'bg-danger' : '' }}
                    {{ $student->status === 'LeaveOfAbsence' ? 'bg-warning text-dark' : '' }}
                    {{ $student->status === 'Graduated' ? 'bg-info text-dark' : '' }}
                    mt-2 px-3 py-1
                ">
                    Status: {{ $student->status }}
                </span>
            </div>
            <hr>
            <div class="small">
                <div class="mb-2"><strong>Email:</strong> {{ $student->user->email }}</div>
                <div class="mb-2"><strong>College:</strong> {{ $student->program->department->college->name }}</div>
                <div class="mb-2"><strong>Department:</strong> {{ $student->program->department->name }}</div>
                <div class="mb-2"><strong>Program:</strong> {{ $student->program->name }}</div>
                <div class="mb-2"><strong>Study Mode:</strong> {{ $student->study_mode }}</div>
                <div class="mb-2"><strong>Gender:</strong> {{ $student->gender }}</div>
                <div class="mb-2"><strong>Date of Birth:</strong> {{ $student->date_of_birth }}</div>
            </div>
        </div>
    </div>

    <!-- History Tabs (Status Changes & Transfers) -->
    <div class="col-lg-8">
        <div class="card p-4 shadow-sm bg-white border-0 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-warning"></i>Status Change History</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>From &rarr; To</th>
                            <th>Reason</th>
                            <th>Officer</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->statusHistories as $sh)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $sh->previous_status }}</span> &rarr;
                                    <span class="badge bg-primary">{{ $sh->new_status }}</span>
                                </td>
                                <td>{{ $sh->reason }}</td>
                                <td>{{ $sh->actor->name }}</td>
                                <td>{{ $sh->created_at->format('M d, Y - H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No status alterations recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3"><i class="bi bi-shuffle me-2 text-primary"></i>Program Transfer History</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Original Program</th>
                            <th>New Program</th>
                            <th>Reason</th>
                            <th>Approved Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->programTransfers as $pt)
                            <tr>
                                <td>{{ $pt->fromProgram->name }}</td>
                                <td><strong class="text-success">{{ $pt->toProgram->name }}</strong></td>
                                <td>{{ $pt->reason }}</td>
                                <td>{{ $pt->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No program transfers recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: Status Change -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('registrar.students.status.update', $student->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Update Student Academic Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">New Status *</label>
                    <select name="new_status" class="form-select" required>
                        <option value="Active">Active</option>
                        <option value="LeaveOfAbsence">Leave of Absence</option>
                        <option value="Suspended">Suspended (Disciplinary / Academic)</option>
                        <option value="Withdrawn">Withdrawn</option>
                        <option value="Dismissed">Academic Dismissal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Official Justification / Reason *</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Medical leave approved by University Board" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Save Status Change</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Program Transfer -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('registrar.students.transfer', $student->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Transfer Degree Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Destination Academic Program *</label>
                    <select name="to_program_id" class="form-select" required>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }} ({{ $p->degree_level }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Transfer Justification / Approval Ref *</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Approved by Academic Commission circular AC/2026/04" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Authorize Transfer</button>
            </div>
        </form>
    </div>
</div>
@endsection