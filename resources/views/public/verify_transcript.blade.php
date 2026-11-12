<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Document Verification - Dambi Dollo University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .hero-banner { background: #0b1f33; color: #fff; padding: 35px 0; border-bottom: 4px solid #c59b27; }
        .card-verify { border-radius: 12px; border: none; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.08); }
        .watermark-badge { border: 2px dashed #198754; background-color: #e8f5e9; }
    </style>
</head>
<body>
    <div class="hero-banner text-center">
        <i class="bi bi-mortarboard-fill text-warning fs-1 mb-2"></i>
        <h3 class="fw-bold mb-1">DAMBI DOLLO UNIVERSITY</h3>
        <p class="mb-0 text-white-50 small">Public Academic Document Verification Registry</p>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Search Box -->
                <div class="card card-verify p-4 bg-white mb-4">
                    <form action="{{ route('public.verify.lookup') }}" method="POST">
                        @csrf
                        <label class="form-label small fw-semibold text-secondary text-uppercase">Enter Verification Reference Number</label>
                        <div class="input-group">
                            <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. DDU-VER-89A4B2C1" value="{{ $code ?? '' }}" required>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-search me-1"></i> Verify Record
                            </button>
                        </div>
                    </form>
                </div>

                @if(isset($code))
                    @if($verification)
                        @php
                            $st = $verification->student;
                            $latestResult = $st->semesterResults->last();
                        @endphp
                        <!-- Authenticated Record Found -->
                        <div class="card card-verify p-4 p-md-5 bg-white border-top border-4 border-success">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <div>
                                    <span class="badge bg-success p-2 fs-6">
                                        <i class="bi bi-shield-check me-1"></i> Official Document Verified Genuine
                                    </span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Issued by Office of Registrar</small>
                                    <span class="font-monospace small fw-bold text-dark">{{ $verification->verification_code }}</span>
                                </div>
                            </div>

                            <table class="table table-bordered align-middle">
                                <tbody>
                                    <tr>
                                        <th width="35%" class="table-light">Full Legal Name</th>
                                        <td><strong class="fs-5">{{ $st->first_name }} {{ $st->middle_name }} {{ $st->last_name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light">University Student ID</th>
                                        <td><strong class="text-primary font-monospace">{{ $st->student_id_number }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light">College / Faculty</th>
                                        <td>{{ $st->program->department->college->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="table-light">Academic Degree Program</th>
                                        <td>{{ $st->program->name }} ({{ $st->study_mode }})</td>
                                    </tr>
                                    <tr>
                                        <th class="table-light">Cumulative GPA (CGPA)</th>
                                        <td><strong class="text-success fs-5">{{ number_format($latestResult?->cgpa ?? 0.00, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light">Official Academic Status</th>
                                        <td>
                                            <span class="badge {{ $st->status === 'Graduated' ? 'bg-primary' : 'bg-success' }} px-3 py-1">
                                                {{ $st->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-light">Verification Timestamp</th>
                                        <td>{{ $verification->issued_at }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="watermark-badge p-3 rounded text-center mt-3 text-success">
                                <i class="bi bi-patch-check-fill fs-3 d-block mb-1"></i>
                                <strong>Authentic University Record</strong>
                                <div class="small text-muted">This digital record directly matches the encrypted student ledger at Dambi Dollo University.</div>
                            </div>
                        </div>
                    @else
                        <!-- No Record Found -->
                        <div class="card card-verify p-5 bg-white text-center border-top border-4 border-danger">
                            <i class="bi bi-x-circle-fill text-danger fs-1 mb-2"></i>
                            <h4 class="fw-bold text-danger">Invalid Verification Reference</h4>
                            <p class="text-muted small mx-auto" style="max-width: 480px;">
                                No academic credential matches reference code <code>{{ $code }}</code>. The document presented may be altered or expired. Please contact the Registrar's Office.
                            </p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</body>
</html>