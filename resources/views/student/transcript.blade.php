<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $hasPendingGrades ? 'Unofficial Progress Report' : 'Official Academic Transcript' }} - {{ $student->student_id_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #fff; color: #000; font-family: 'Times New Roman', serif; }
        .transcript-header { border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 20px; text-align: center; }
        .table-transcript th, .table-transcript td { border: 1px solid #000 !important; padding: 5px 8px; font-size: 14px; }
        .watermark-notice { border: 2px dashed #dc3545; background-color: #fff8f8; padding: 10px; margin-bottom: 15px; font-family: sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-4">
    <div class="no-print mb-4 d-flex justify-content-between">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">&larr; Return to Dashboard</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer"></i> Print Document
        </button>
    </div>

    <!-- IN-PROGRESS WARNING (Displayed when some courses have no grades) -->
    @if($hasPendingGrades)
        <div class="watermark-notice text-danger text-center small">
            <strong>NOTICE: UNOFFICIAL STUDENT ACADEMIC PROGRESS REPORT</strong><br>
            One or more registered courses are currently in progress or awaiting grade publication. Final semester GPA and official verification seals are withheld until all course grades are finalized.
        </div>
    @endif

    <!-- Official University Header -->
    <div class="transcript-header">
        <h3 class="fw-bold mb-1">DAMBI DOLLO UNIVERSITY</h3>
        <h5 class="mb-1 text-uppercase">Office of the Registrar</h5>
        <h6 class="text-uppercase fw-bold text-decoration-underline">
            {{ $hasPendingGrades ? 'Unofficial Student Academic Progress Report' : 'Official Student Academic Record' }}
        </h6>
    </div>

    <!-- Student Demographic Metadata -->
    <div class="row mb-4" style="font-size: 14px;">
        <div class="col-6">
            <div><strong>Student Name:</strong> {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</div>
            <div><strong>Student ID Number:</strong> {{ $student->student_id_number }}</div>
            <div><strong>Gender:</strong> {{ $student->gender }}</div>
        </div>
        <div class="col-6">
            <div><strong>College:</strong> {{ $student->program->department->college->name }}</div>
            <div><strong>Department:</strong> {{ $student->program->department->name }}</div>
            <div><strong>Degree Program:</strong> {{ $student->program->name }} ({{ $student->study_mode }})</div>
        </div>
    </div>

    <!-- Semester Course Registrations & Results -->
    @foreach($registrations as $reg)
        @php
            $result = $semesterResults->get($reg->semester_id);
            $termHasPending = $reg->registrationCourses->where('item_status', 'Registered')->whereNull('grade_letter')->isNotEmpty();
        @endphp
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="fw-bold text-uppercase mb-0" style="font-size: 14px;">{{ $reg->semester->name }}</h6>
                @if($termHasPending)
                    <span class="badge bg-secondary text-white small" style="font-family: sans-serif;">TERM IN PROGRESS</span>
                @endif
            </div>

            <table class="table table-transcript w-100 mb-2">
                <thead>
                    <tr class="table-light">
                        <th width="15%">Course Code</th>
                        <th width="50%">Course Title</th>
                        <th width="10%" class="text-center">Cr.Hr</th>
                        <th width="10%" class="text-center">Grade</th>
                        <th width="15%" class="text-center">Grade Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reg->registrationCourses as $item)
                        <tr>
                            <td>{{ $item->courseOffering->course->code }}</td>
                            <td>{{ $item->courseOffering->course->title }}</td>
                            <td class="text-center">{{ $item->courseOffering->course->credit_hours }}</td>
                            <td class="text-center fw-bold">
                                @if($item->grade_letter)
                                    {{ $item->grade_letter }}
                                @else
                                    <span class="text-muted fst-italic">In Progress</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $item->grade_points !== null ? number_format($item->grade_points * $item->courseOffering->course->credit_hours, 2) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Semester Summary Row -->
            <div class="d-flex justify-content-between p-2 border border-dark bg-light" style="font-size: 13px;">
                @if($termHasPending)
                    <span><strong>SGPA:</strong> <em>Pending (Courses In Progress)</em></span>
                    <span><strong>CGPA:</strong> {{ $result && $result->cgpa > 0 ? number_format($result->cgpa, 2) : 'Pending' }}</span>
                    <span><strong>Status:</strong> Term In Progress</span>
                @else
                    <span><strong>SGPA:</strong> {{ $result ? number_format($result->sgpa, 2) : '0.00' }}</span>
                    <span><strong>CGPA:</strong> {{ $result ? number_format($result->cgpa, 2) : '0.00' }}</span>
                    <span><strong>Academic Status:</strong> {{ $result ? $result->academic_standing : 'Good Standing' }}</span>
                @endif
            </div>
        </div>
    @endforeach

    <!-- OFFICIAL SEAL & VERIFICATION (Only shown when all grades are published) -->
    @if(!$hasPendingGrades && $verification)
        <div class="row mt-5 pt-4 text-center" style="font-size: 14px;">
            <div class="col-4 border-top border-dark mx-auto">
                Registrar's Signature & University Seal
            </div>
        </div>

        <div class="row mt-4 pt-3 border-top border-dark align-items-center" style="font-size: 13px;">
            <div class="col-8">
                <div><strong>Official Verification Reference:</strong> <code class="fs-6 text-dark fw-bold">{{ $verification->verification_code }}</code></div>
                <div class="text-muted small">
                    Verify this authenticated record online at:<br>
                    <strong>{{ url('/verify/transcript/' . $verification->verification_code) }}</strong>
                </div>
            </div>
            <div class="col-4 text-end">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=95x95&data={{ urlencode(url('/verify/transcript/' . $verification->verification_code)) }}" 
                     alt="Scan to Verify" 
                     class="border p-1">
                <div class="text-muted" style="font-size: 10px;">Scan to Verify Authenticity</div>
            </div>
        </div>
    @else
        <!-- In-Progress Notice: Official Seal Withheld -->
        <div class="text-center text-muted small mt-5 pt-4 border-top">
            <em>Official Registrar Seal and Digital QR Verification are withheld until all course assessments are submitted and officially published.</em>
        </div>
    @endif
</body>
</html>