@extends('layouts.app')
@section('title', 'Student Services & Academic Records')

@section('content')
<!-- Student Header & Dossier Meta -->
<div class="card p-4 shadow-sm bg-white border-0 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <i class="bi bi-mortarboard-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</h4>
                <div class="text-muted small">
                    ID: <strong class="text-primary">{{ $student->student_id_number }}</strong>
                    <span class="mx-2">•</span>
                    Program: <strong>{{ $student->program->name }}</strong> ({{ $student->study_mode }})
                </div>
            </div>
        </div>
        <div>
            <span class="badge bg-success px-3 py-2">
                <i class="bi bi-check-circle-fill me-1"></i> Academic Status: {{ $student->status }}
            </span>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-pills nav-fill bg-white p-2 rounded shadow-sm mb-4 border" id="servicesTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-panel" type="button" role="tab">
            <i class="bi bi-file-earmark-text-fill me-2"></i> Official Document & Letter Requests
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="clearance-tab" data-bs-toggle="tab" data-bs-target="#clearance-panel" type="button" role="tab">
            <i class="bi bi-award-fill me-2"></i> Graduation Clearance Dossier
        </button>
    </li>
</ul>

<!-- Tab Contents -->
<div class="tab-content" id="servicesTabContent">
    
    <!-- ================= TAB 1: DOCUMENT REQUESTS ================= -->
    <div class="tab-pane fade show active" id="docs-panel" role="tabpanel">
        <div class="row g-4">
            <!-- Request Form (Left Column) -->
            <div class="col-lg-5">
                <div class="card p-4 shadow-sm bg-white border-0 h-100">
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-file-earmark-plus text-primary me-2"></i>New Document Request
                    </h5>
                    <p class="text-muted small mb-3">Request verified official certificates stamped by the Registrar.</p>

                    <form action="{{ route('student.services.documents.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Document Type *</label>
                            <select name="request_type" class="form-select" required>
                                <option value="Official Transcript">Official Academic Transcript</option>
                                <option value="Enrollment Verification Letter">Enrollment Verification Letter</option>
                                <option value="Medium of Instruction Certificate">English Medium of Instruction Certificate</option>
                                <option value="Letter of Recommendation">Academic Recommendation Letter</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Purpose & Recipient Details *</label>
                            <textarea name="purpose" class="form-control" rows="4" placeholder="State recipient organization, scholarship name, or employer..." required></textarea>
                            <div class="form-text small">Explain why you need this document so the records office can verify eligibility.</div>
                        </div>

                        <div class="bg-light p-3 rounded mb-3 small text-muted border">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            <strong>Turnaround Time:</strong> Standard official requests are processed and issued within <strong>2–3 working days</strong>.
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-send-fill me-1"></i> Submit Document Request
                        </button>
                    </form>
                </div>
            </div>

            <!-- Request History (Right Column) -->
            <div class="col-lg-7">
                <div class="card p-4 shadow-sm bg-white border-0 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-clock-history text-secondary me-2"></i>Submitted Requests History
                        </h5>
                        <span class="badge bg-light text-secondary border">
                            Total: {{ $student->documentRequests->count() }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Document Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->documentRequests as $req)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-primary border font-monospace">
                                                {{ $req->tracking_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="d-block">{{ $req->request_type }}</strong>
                                            <small class="text-muted">{{ Str::limit($req->purpose, 35) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                {{ $req->status === 'Pending' ? 'bg-warning text-dark' : '' }}
                                                {{ $req->status === 'Processing' ? 'bg-info text-dark' : '' }}
                                                {{ $req->status === 'Issued' ? 'bg-success' : '' }}
                                                {{ $req->status === 'Rejected' ? 'bg-danger' : '' }}
                                            ">
                                                {{ $req->status }}
                                            </span>
                                        </td>
                                        <td><small class="text-muted">{{ $req->created_at->format('M d, Y') }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                            You have not submitted any document requests yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TAB 2: GRADUATION CLEARANCE ================= -->
    <div class="tab-pane fade" id="clearance-panel" role="tabpanel">
        <div class="card p-4 shadow-sm bg-white border-0">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-mortarboard-fill text-success me-2"></i>University Graduation Clearance
                    </h5>
                    <p class="text-muted small mb-0">Mandatory 4-point institutional sign-off before official degree conferral.</p>
                </div>
                <div>
                    @if($student->graduationClearance)
                        <span class="badge 
                            {{ $student->graduationClearance->overall_status === 'InClearance' ? 'bg-warning text-dark' : '' }}
                            {{ $student->graduationClearance->overall_status === 'ClearedForGraduation' ? 'bg-info text-dark' : '' }}
                            {{ $student->graduationClearance->overall_status === 'Graduated' ? 'bg-success' : '' }}
                            fs-6 px-3 py-2
                        ">
                            Status: {{ $student->graduationClearance->overall_status }}
                        </span>
                    @endif
                </div>
            </div>

            @if($student->graduationClearance)
                <!-- 4-Office Clearance Progress Grid -->
                <div class="row g-3 mb-4">
                    <!-- Office 1: Department -->
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center h-100 bg-light">
                            <i class="bi bi-building fs-3 d-block mb-1 text-primary"></i>
                            <h6 class="fw-bold mb-1">1. Academic Dept.</h6>
                            <p class="small text-muted mb-2">Curriculum & Credits</p>
                            <span class="badge {{ $student->graduationClearance->status_department === 'Cleared' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1">
                                {{ $student->graduationClearance->status_department }}
                            </span>
                        </div>
                    </div>

                    <!-- Office 2: Library -->
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center h-100 bg-light">
                            <i class="bi bi-book fs-3 d-block mb-1 text-info"></i>
                            <h6 class="fw-bold mb-1">2. University Library</h6>
                            <p class="small text-muted mb-2">Books & Borrowed Items</p>
                            <span class="badge {{ $student->graduationClearance->status_library === 'Cleared' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1">
                                {{ $student->graduationClearance->status_library }}
                            </span>
                        </div>
                    </div>

                    <!-- Office 3: Student Affairs / Hostel -->
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center h-100 bg-light">
                            <i class="bi bi-house-door fs-3 d-block mb-1 text-warning"></i>
                            <h6 class="fw-bold mb-1">3. Student Affairs</h6>
                            <p class="small text-muted mb-2">Dormitory & Sports Gear</p>
                            <span class="badge {{ $student->graduationClearance->status_services === 'Cleared' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1">
                                {{ $student->graduationClearance->status_services }}
                            </span>
                        </div>
                    </div>

                    <!-- Office 4: Finance / Cost Sharing -->
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center h-100 bg-light">
                            <i class="bi bi-cash-coin fs-3 d-block mb-1 text-success"></i>
                            <h6 class="fw-bold mb-1">4. Finance Office</h6>
                            <p class="small text-muted mb-2">Cost-Sharing & Dues</p>
                            <span class="badge {{ $student->graduationClearance->status_finance === 'Cleared' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1">
                                {{ $student->graduationClearance->status_finance }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($student->graduationClearance->overall_status === 'Graduated')
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="bi bi-award-fill fs-2 me-3 text-success"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Degree Conferred!</h5>
                            You have completed all institutional requirements and are officially recorded as a graduate of Dambi Dollo University.
                        </div>
                    </div>
                @else
                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Your clearance dossier is actively undergoing review. Each office signs off independently in the Registrar matrix.
                    </div>
                @endif

            @else
                <!-- Graduation Initiation Callout -->
                <div class="text-center py-5">
                    <i class="bi bi-mortarboard text-secondary" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold text-dark mt-3">Ready to Graduate this Academic Term?</h5>
                    <p class="text-muted small mx-auto" style="max-width: 500px;">
                        Opening your graduation clearance file initiates the automated 4-point institutional sign-off across your Academic Department, the University Library, Student Affairs, and the Finance Office.
                    </p>
                    <form action="{{ route('student.services.graduation.apply') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 py-2 fw-semibold" onclick="return confirm('Open graduation clearance dossier for this term?')">
                            <i class="bi bi-award me-1"></i> Initiate Graduation Clearance Dossier
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection