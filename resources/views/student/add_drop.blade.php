@extends('layouts.app')
@section('title', 'Course Add/Drop')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Course Add & Drop Management</h4>
        <p class="text-muted mb-0">Active Term: <strong>{{ $activeSemester->name }}</strong></p>
    </div>
    <div>
        @if($isWindowOpen)
            <span class="badge bg-success p-2">
                <i class="bi bi-clock-history me-1"></i> Add/Drop Window Open until {{ \Carbon\Carbon::parse($activeSemester->add_drop_deadline)->format('M d, Y') }}
            </span>
        @else
            <span class="badge bg-danger p-2">
                <i class="bi bi-lock-fill me-1"></i> Add/Drop Window Closed
            </span>
        @endif
    </div>
</div>

@if(!$isWindowOpen)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        The official university deadline for Adding or Dropping courses has passed (Deadline: <strong>{{ $activeSemester->add_drop_deadline }}</strong>). No new requests can be submitted.
    </div>
@endif

<div class="row g-4">
    <!-- Active Enrolled Courses (Eligible to DROP) -->
    <div class="col-lg-6">
        <div class="card p-4 shadow-sm bg-white border-0 h-100">
            <h5 class="fw-bold text-danger mb-3">
                <i class="bi bi-dash-circle me-1"></i> Drop an Enrolled Course
            </h5>
            
            @if($registration && $registration->registrationCourses->where('item_status', 'Registered')->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Cr.Hr</th>
                                <th>Section</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registration->registrationCourses->where('item_status', 'Registered') as $item)
                                <tr>
                                    <td><strong>{{ $item->courseOffering->course->code }}</strong> - {{ $item->courseOffering->course->title }}</td>
                                    <td>{{ $item->courseOffering->course->credit_hours }}</td>
                                    <td><span class="badge bg-secondary">{{ $item->courseOffering->section }}</span></td>
                                    <td class="text-end">
                                        @if($isWindowOpen)
                                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#dropModal{{ $item->id }}">
                                                Drop Course
                                            </button>

                                            <!-- Drop Modal -->
                                            <div class="modal fade text-start" id="dropModal{{ $item->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('student.adddrop.store') }}" method="POST" class="modal-content">
                                                        @csrf
                                                        <input type="hidden" name="course_offering_id" value="{{ $item->course_offering_id }}">
                                                        <input type="hidden" name="request_type" value="Drop">
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold">Confirm Course Drop: {{ $item->courseOffering->course->code }}</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-muted small">Provide an academic or personal reason for dropping this course. Requires Department Head approval.</p>
                                                            <label class="form-label small fw-semibold">Reason for Dropping *</label>
                                                            <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Credit overload, timetable conflict with lab" required></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-danger btn-sm">Submit Drop Request</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small">Locked</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small">You have no active enrolled courses in this term.</p>
            @endif
        </div>
    </div>

    <!-- Available Offerings (Eligible to ADD) -->
    <div class="col-lg-6">
        <div class="card p-4 shadow-sm bg-white border-0 h-100">
            <h5 class="fw-bold text-success mb-3">
                <i class="bi bi-plus-circle me-1"></i> Add an Additional Course
            </h5>
            
            @if($isWindowOpen)
                <form action="{{ route('student.adddrop.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="request_type" value="Add">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Course Offering to Add *</label>
                        <select name="course_offering_id" class="form-select" required>
                            <option value="">-- Choose Course Offering --</option>
                            @foreach($availableOfferings as $offering)
                                <option value="{{ $offering->id }}">
                                    {{ $offering->course->code }} - {{ $offering->course->title }} ({{ $offering->section }} | {{ $offering->course->credit_hours }} Cr.Hr)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Reason for Adding *</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Need to fulfill elective requirement, clearing previous deficiency" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i> Submit Add Request
                    </button>
                </form>
            @else
                <p class="text-muted small">Adding courses is disabled because the deadline has passed.</p>
            @endif
        </div>
    </div>

    <!-- Request Status History -->
    <div class="col-12">
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3">Your Add/Drop Request History</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Course</th>
                            <th>Reason</th>
                            <th>Submitted Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myRequests as $req)
                            <tr>
                                <td>
                                    <span class="badge {{ $req->request_type === 'Add' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $req->request_type }}
                                    </span>
                                </td>
                                <td><strong>{{ $req->courseOffering->course->code }}</strong> - {{ $req->courseOffering->course->title }}</td>
                                <td><small>{{ $req->reason }}</small></td>
                                <td>{{ $req->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $req->status === 'Pending' ? 'bg-warning text-dark' : '' }}
                                        {{ $req->status === 'Approved' ? 'bg-success' : '' }}
                                        {{ $req->status === 'Rejected' ? 'bg-danger' : '' }}
                                    ">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $req->reviewer_remarks ?? '-' }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">No Add/Drop requests submitted this term.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection