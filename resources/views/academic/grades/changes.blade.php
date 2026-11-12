@extends('layouts.app')
@section('title', 'Grade Change Queue')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Audited Grade Change Requests</h4>
            <p class="text-muted mb-0">Traceable grade correction requests with required Department and Registrar approvals.</p>
        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Original</th>
                    <th>Proposed</th>
                    <th>Justification Reason</th>
                    <th>Status</th>
                    <th class="text-end">Approval Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    @php
                        $st = $req->registrationCourse->courseRegistration->student;
                        $crs = $req->registrationCourse->courseOffering->course;
                    @endphp
                    <tr>
                        <td><strong>{{ $st->student_id_number }}</strong></td>
                        <td>{{ $st->first_name }} {{ $st->last_name }}</td>
                        <td>{{ $crs->code }}</td>
                        <td><span class="badge bg-secondary">{{ $req->previous_score }} ({{ $req->previous_grade }})</span></td>
                        <td><span class="badge bg-primary fs-6">{{ $req->proposed_score }} ({{ $req->proposed_grade }})</span></td>
                        <td><small>{{ $req->reason }}</small></td>
                        <td>
                            <span class="badge 
                                {{ $req->status === 'Pending' ? 'bg-warning text-dark' : '' }}
                                {{ $req->status === 'DeptEndorsed' ? 'bg-info text-dark' : '' }}
                                {{ $req->status === 'Approved' ? 'bg-success' : '' }}
                                {{ $req->status === 'Rejected' ? 'bg-danger' : '' }}
                            ">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                @if($req->status === 'Pending')
                                    <form action="{{ route('academic.grades.changes.endorse', $req->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Endorse (Dept)</button>
                                    </form>
                                @endif
                                @if(in_array($req->status, ['Pending', 'DeptEndorsed']))
                                    <form action="{{ route('academic.grades.changes.approve', $req->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve & Recalculate</button>
                                    </form>
                                    <form action="{{ route('academic.grades.changes.reject', $req->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No grade change requests submitted.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $requests->links('pagination::bootstrap-5') }}
</div>
@endsection