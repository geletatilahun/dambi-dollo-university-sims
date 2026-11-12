@extends('layouts.app')
@section('title', 'Bulk Student Import')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Batch Student Import & Data Migration</h4>
        <p class="text-muted mb-0 small">Import new student cohorts from Ministry of Education (MoE) assignment spreadsheets.</p>
    </div>
    <a href="{{ route('registrar.import.template') }}" class="btn btn-outline-success btn-sm fw-semibold">
        <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Download Official CSV Template
    </a>
</div>

<!-- Import Errors Notice (if any rows were skipped) -->
@if(session('import_errors') && count(session('import_errors')) > 0)
    <div class="alert alert-warning shadow-sm mb-4">
        <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> The following rows were skipped due to errors:</h6>
        <ul class="mb-0 small">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <!-- Upload Form Card -->
    <div class="col-lg-6">
        <div class="card p-4 shadow-sm bg-white border-0 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2">
                <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Upload Student CSV File
            </h5>

            <form action="{{ route('registrar.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Select CSV Spreadsheet *</label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv, .txt" required>
                    <div class="form-text small">File must be a comma-separated <code>.csv</code> file (Max 5MB).</div>
                </div>

                <div class="bg-light p-3 rounded mb-3 small text-muted">
                    <strong class="d-block text-dark mb-1"><i class="bi bi-info-circle me-1"></i>Automated Batch Operations:</strong>
                    <ul class="mb-0 ps-3">
                        <li>Validates program codes against catalog.</li>
                        <li>Blocks duplicate email and national ID entries.</li>
                        <li>Generates unique IDs (e.g. <code>UGR/00101/19</code>).</li>
                        <li>Generates portal accounts with role <code>Student</code>.</li>
                    </ul>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" onclick="return confirm('Begin batch import and user creation?')">
                    <i class="bi bi-arrow-repeat me-1"></i> Process & Ingest Student Batch
                </button>
            </form>
        </div>
    </div>

    <!-- Reference Guide Card: Active Program Codes -->
    <div class="col-lg-6">
        <div class="card p-4 shadow-sm bg-white border-0 h-100">
            <h5 class="fw-bold mb-3 border-bottom pb-2">
                <i class="bi bi-code-square text-secondary me-2"></i>Valid Program Codes in Catalog
            </h5>
            <p class="text-muted small">Your CSV's <code>program_code</code> column must exactly match one of these registered programs:</p>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Program Code</th>
                            <th>Degree Title</th>
                            <th>Study Mode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $p)
                            <tr>
                                <td><code class="fw-bold text-primary">{{ $p->code }}</code></td>
                                <td>{{ $p->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $p->study_mode }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection