@extends('layouts.app')
@section('title', 'Add/Drop Approvals')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Add/Drop Course Approval Queue</h4>
            <p class="text-muted mb-0">Departmental review queue for student course addition and withdrawal requests.</p>
        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Action Type</th>
                    <th>Course</th>
                    <th>Student Reason</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $r)
                    <tr>
                        <td><strong>{{ $r->student->student_id_number }}</strong></td>
                        <td>{{ $r->student->first_name }} {{ $r->student->last_name }}</td>
                        <td><small>{{ $r->student->program->code }}</small></td>
                        <td>
                            <span class="badge {{ $r->request_type === 'Add' ? 'bg-success' : 'bg-danger' }}">
                                {{ $r->request_type }}
                            </span>
                        </td>
                        <td><strong>{{ $r->courseOffering->course->code }}</strong> ({{ $r->courseOffering->section }})</td>
                        <td><small class="text-muted">{{ Str::limit($r->reason, 40) }}</small></td>
                        <td>
                            <span class="badge 
                                {{ $r->status === 'Pending' ? 'bg-warning text-dark' : '' }}
                                {{ $r->status === 'Approved' ? 'bg-success' : '' }}
                                {{ $r->status === 'Rejected' ? 'bg-danger' : '' }}
                            ">
                                {{ $r->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($r->status === 'Pending')
                                <form action="{{ route('academic.adddrop.approve', $r->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this {{ $r->request_type }} request?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $r->id }}">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>

                                <!-- Reject Modal -->
                                <div class="modal fade text-start" id="rejectModal{{ $r->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('academic.adddrop.reject', $r->id) }}" method="POST" class="modal-content">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Reject Add/Drop Request</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label small fw-semibold">Reason for Rejection</label>
                                                <textarea name="remarks" class="form-control" rows="3" placeholder="e.g. Schedule clash with required core course"></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger btn-sm">Confirm Rejection</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <small class="text-muted"><i class="bi bi-check2-all"></i> Resolved</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No Add/Drop requests pending review.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $requests->links('pagination::bootstrap-5') }}
</div>
@endsection