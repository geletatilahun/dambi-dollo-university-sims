@extends('layouts.app')
@section('title', 'Document Processing')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Student Document Service Requests</h4>
            <p class="text-muted mb-0">Process official transcripts, letters, and certifications.</p>
        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tracking #</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Document Type</th>
                    <th>Current Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $r)
                    <tr>
                        <td><strong class="text-primary">{{ $r->tracking_number }}</strong></td>
                        <td>{{ $r->student->student_id_number }}</td>
                        <td>{{ $r->student->first_name }} {{ $r->student->last_name }}</td>
                        <td>{{ $r->request_type }}</td>
                        <td>
                            <span class="badge 
                                {{ $r->status === 'Pending' ? 'bg-warning text-dark' : '' }}
                                {{ $r->status === 'Processing' ? 'bg-info text-dark' : '' }}
                                {{ $r->status === 'Issued' ? 'bg-success' : '' }}
                                {{ $r->status === 'Rejected' ? 'bg-danger' : '' }}
                            ">
                                {{ $r->status }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('registrar.documents.update', $r->id) }}" method="POST" class="d-flex gap-1">
                                @csrf
                                <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                    <option value="Processing" {{ $r->status === 'Processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="Issued" {{ $r->status === 'Issued' ? 'selected' : '' }}>Issued</option>
                                    <option value="Rejected" {{ $r->status === 'Rejected' ? 'selected' : '' }}>Reject</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No pending document requests.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $requests->links('pagination::bootstrap-5') }}
</div>
@endsection