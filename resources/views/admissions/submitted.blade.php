<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Received - DDU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #0f2b48; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .receipt-card { width: 100%; max-width: 550px; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="container p-3">
        <div class="card receipt-card mx-auto p-4 p-md-5 text-center shadow">
            <div class="mb-3 text-success">
                <i class="bi bi-check-circle-fill" style="font-size: 3.5rem;"></i>
            </div>
            <h4 class="fw-bold mb-1">Application Submitted!</h4>
            <p class="text-muted small">Your application to Dambi Dollo University has been registered.</p>

            <div class="bg-light p-3 rounded my-3 text-start">
                <div class="text-muted small">Application Tracking Number:</div>
                <div class="fs-4 fw-bold text-primary">{{ $application->application_number }}</div>
                <hr class="my-2">
                <div><strong>Full Name:</strong> {{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</div>
                <div><strong>Program:</strong> {{ $application->program->name }}</div>
                <div><strong>Study Mode:</strong> {{ $application->study_mode }}</div>
                <div><strong>Current Status:</strong> <span class="badge bg-warning text-dark">{{ $application->status }}</span></div>
            </div>

            <p class="text-muted small">Please preserve your Application Number for tracking inquiries with the Registrar.</p>

            <div class="mt-3">
                <a href="{{ route('admissions.apply') }}" class="btn btn-outline-secondary btn-sm me-2">Submit Another</a>
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Go to Portal Login</a>
            </div>
        </div>
    </div>
</body>
</html>