@extends('layouts.app')
@section('title', 'University Structure')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">University Organizational Structure</h4>
        <p class="text-muted mb-0">Manage colleges, academic departments, and degree programs.</p>
    </div>
    <div class="btn-group">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCollegeModal">
            <i class="bi bi-plus-circle me-1"></i> Add College
        </button>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDeptModal">
            <i class="bi bi-plus-circle me-1"></i> Add Department
        </button>
        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProgramModal">
            <i class="bi bi-plus-circle me-1"></i> Add Degree Program
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    @forelse($colleges as $college)
        <div class="col-12">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-primary mb-0">
                        <i class="bi bi-building me-2"></i>{{ $college->name }} ({{ $college->code }})
                    </h5>
                    <span class="badge bg-secondary">{{ $college->departments->count() }} Departments</span>
                </div>

                <div class="row g-3">
                    @forelse($college->departments as $dept)
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold text-dark mb-2">Dept. of {{ $dept->name }} ({{ $dept->code }})</h6>
                                </div>
                                <div class="small text-muted mb-2">Offered Programs:</div>
                                <ul class="list-group list-group-flush rounded">
                                    @forelse($dept->programs as $prog)
                                        <li class="list-group-item bg-white d-flex justify-content-between align-items-center small py-2">
                                            <span><strong>{{ $prog->code }}</strong> - {{ $prog->name }}</span>
                                            <span class="badge bg-info text-dark">{{ $prog->degree_level }} | {{ $prog->duration_years }} Yrs</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item bg-white text-muted small py-2">No degree programs configured yet.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted small">No departments added to this college yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card p-4 text-center text-muted">No colleges found.</div></div>
    @endforelse
</div>

<!-- Modal 1: Add College -->
<div class="modal fade" id="addCollegeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.structure.college.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New College</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">College Code *</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. CBE, COT, CNCS" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">College Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. College of Business and Economics" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save College</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Add Department -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.structure.department.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Parent College *</label>
                    <select name="college_id" class="form-select" required>
                        @foreach($colleges as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Department Code *</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. SE, EE, ACFN" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Department Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Department of Software Engineering" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Department</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Add Degree Program -->
<div class="modal fade" id="addProgramModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.structure.program.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Degree Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Parent Department *</label>
                    <select name="department_id" class="form-select" required>
                        @foreach($colleges as $c)
                            <optgroup label="{{ $c->name }}">
                                @foreach($c->departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Program Code *</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. BSC-SE" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Degree Level *</label>
                        <select name="degree_level" class="form-select" required>
                            <option value="Undergraduate">Undergraduate</option>
                            <option value="Postgraduate">Postgraduate</option>
                            <option value="PhD">PhD</option>
                            <option value="Diploma">Diploma</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Program Full Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. B.Sc. in Software Engineering" required>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Study Mode *</label>
                        <select name="study_mode" class="form-select" required>
                            <option value="Regular">Regular</option>
                            <option value="Extension">Extension</option>
                            <option value="Weekend">Weekend</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Duration (Years) *</label>
                        <input type="number" name="duration_years" class="form-control" value="4" min="1" max="7" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save Degree Program</button>
            </div>
        </form>
    </div>
</div>
@endsection