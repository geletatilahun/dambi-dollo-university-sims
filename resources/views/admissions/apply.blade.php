<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Admission Application - Dambi Dollo University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .hero-banner { background: #0f2b48; color: #fff; padding: 40px 0; border-bottom: 4px solid #c59b27; }
        .card-form { border-radius: 10px; border: none; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="hero-banner text-center">
        <i class="bi bi-mortarboard-fill text-warning fs-1 mb-2"></i>
        <h2 class="fw-bold">DAMBI DOLLO UNIVERSITY</h2>
        <p class="mb-0 text-white-50">Online Student Admission Portal & Application Entry</p>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-form p-4 p-md-5 bg-white">
                    <h4 class="fw-bold text-dark mb-1">New Student Application</h4>
                    <p class="text-muted small mb-4">Please fill in your details accurately according to your Ethiopian National ID or Grade 12 Certificate.</p>

                    @if($errors->any())
                        <div class="alert alert-danger py-2 small">
                            <strong>Validation Error:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admissions.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <!-- Name Fields -->
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">First Name *</label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Father's Name (Middle) *</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Grandfather's Name *</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                            </div>

                            <!-- Demographic info -->
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Gender *</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select...</option>
                                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Date of Birth *</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">National ID / Kebele ID</label>
                                <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}" placeholder="e.g. ET-12345678">
                            </div>

                            <!-- Contacts -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Address *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="yourname@gmail.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Phone Number *</label>
                                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" placeholder="+251 9..." required>
                            </div>

                            <!-- Program & Study Choices -->
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Desired Academic Program *</label>
                                <select name="program_id" class="form-select" required>
                                    <option value="">Select an Academic Program...</option>
                                    @foreach($programs as $prog)
                                        <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>
                                            {{ $prog->name }} ({{ $prog->degree_level }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Study Mode *</label>
                                <select name="study_mode" class="form-select" required>
                                    <option value="Regular" {{ old('study_mode') === 'Regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="Extension" {{ old('study_mode') === 'Extension' ? 'selected' : '' }}>Extension (Evening)</option>
                                    <option value="Weekend" {{ old('study_mode') === 'Weekend' ? 'selected' : '' }}>Weekend</option>
                                    <option value="Summer" {{ old('study_mode') === 'Summer' ? 'selected' : '' }}>Summer (Kiremt)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Entry Route *</label>
                                <select name="entry_type" class="form-select" required>
                                    <option value="Freshman" {{ old('entry_type') === 'Freshman' ? 'selected' : '' }}>Freshman (Grade 12 National Exam)</option>
                                    <option value="AdvancedStanding" {{ old('entry_type') === 'AdvancedStanding' ? 'selected' : '' }}>Advanced Standing (Diploma Upgrade)</option>
                                    <option value="Transfer" {{ old('entry_type') === 'Transfer' ? 'selected' : '' }}>University Transfer</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Already a Student? Sign In</a>
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-send-fill me-1"></i> Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>