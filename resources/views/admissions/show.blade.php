@extends('layouts.app')
@section('title', 'Review Application')

@section('content')
<!-- ADD THIS ALERT BLOCK: -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Error:</strong> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
<!-- END ALERT BLOCK -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Applicant Dossier</h4>
                <a href="{{ route('admissions.queue') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Queue</a>
            </div>

            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <th width="30%" class="table-light">Application Number</th>
                        <td><strong class="text-primary fs-5">{{ $application->application_number }}</strong></td>
                    </tr>
                    <tr>
                        <th class="table-light">Full Legal Name</th>
                        <td>{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Gender & Birth Date</th>
                        <td>{{ $application->gender }} | {{ $application->date_of_birth }}</td>
                    </tr>
                    <tr>
                        <th class="table-light">National ID / Kebele ID</th>
                        <td>{{ $application->national_id ?? 'Not Provided' }}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Email & Contact</th>
                        <td>{{ $application->email }} | {{ $application->phone_number }}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Program Applied</th>
                        <td><strong>{{ $application->program->name }}</strong> ({{ $application->program->degree_level }})</td>
                    </tr>
                    <tr>
                        <th class="table-light">Study Mode & Entry Route</th>
                        <td>{{ $application->study_mode }} ({{ $application->entry_type }})</td>
                    </tr>
                    <tr>
                        <th class="table-light">Review Remarks</th>
                        <td>{{ $application->reviewer_remarks ?? 'None recorded.' }}</td>
                    </tr>
                </tbody>
            </table>

            @if($application->student)
                <div class="alert alert-success d-flex align-items-center">
                    <i class="bi bi-person-check-fill fs-2 me-3"></i>
                    <div>
                        <h5 class="fw-bold mb-0">Enrolled Student Record Active</h5>
                        Official Student ID Number: <strong>{{ $application->student->student_id_number }}</strong>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reviewer Actions Panel -->
    <div class="col-lg-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Admission Decision</h5>
            <div class="mb-3">
                <span class="text-muted small">Current Application Status:</span>
                <div>
                    <span class="badge fs-6 
                        {{ $application->status === 'Submitted' ? 'bg-warning text-dark' : '' }}
                        {{ $application->status === 'Accepted' ? 'bg-info text-dark' : '' }}
                        {{ $application->status === 'Enrolled' ? 'bg-success' : '' }}
                        {{ $application->status === 'Rejected' ? 'bg-danger' : '' }}
                    ">
                        {{ $application->status }}
                    </span>
                </div>
            </div>

            @if($application->status !== 'Enrolled')
                <form action="{{ route('admissions.review', $application->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Reviewer Comments</label>
                        <textarea name="reviewer_remarks" class="form-control form-control-sm" rows="3">{{ $application->reviewer_remarks }}</textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="status" value="Accepted" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-check-lg me-1"></i> Accept Application
                        </button>
                        <button type="submit" name="status" value="Rejected" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-lg me-1"></i> Reject Application
                        </button>
                    </div>
                </form>
            @endif

            <!-- Step: Convert Accepted Applicant into Active Student -->
            @if($application->status === 'Accepted' && !$application->student_id)
                <div class="border-top pt-3">
                    <h6 class="fw-bold text-success mb-2">Ready for University Enrolment</h6>
                    <p class="text-muted small">Generating student record will assign a sequential ID (e.g. <code>UGR/00101/19</code>) and create a user portal login.</p>
                    <form action="{{ route('admissions.enroll', $application->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold" onclick="return confirm('Generate official student ID and user account?')">
                            <i class="bi bi-person-plus-fill me-1"></i> Enroll & Generate Student ID
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection