@extends('layouts.app')
@section('title', 'Institutional Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dambi Dollo University Academic Analytics</h4>
        <p class="text-muted mb-0">Demographic breakdowns, degree distributions, and student academic standing.</p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-printer me-1"></i> Print Report
    </button>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 border-start border-4 border-primary shadow-sm bg-white">
            <div class="text-muted small fw-semibold text-uppercase">Total Enrolled Cohort</div>
            <div class="fs-3 fw-bold text-dark mt-1">{{ $stats['total_students'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-start border-4 border-success shadow-sm bg-white">
            <div class="text-muted small fw-semibold text-uppercase">Active Enrolled Students</div>
            <div class="fs-3 fw-bold text-success mt-1">{{ $stats['active_students'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-start border-4 border-info shadow-sm bg-white">
            <div class="text-muted small fw-semibold text-uppercase">University Alumni (Graduated)</div>
            <div class="fs-3 fw-bold text-info mt-1">{{ $stats['graduated_students'] }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Demographic Enrollment Table -->
    <div class="col-lg-8">
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3">Enrollment Breakdown by Academic Program & Gender</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Program Code</th>
                            <th>Degree Program Name</th>
                            <th class="text-center">Male</th>
                            <th class="text-center">Female</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollmentByProgram as $prog)
                            <tr>
                                <td><strong>{{ $prog->code }}</strong></td>
                                <td>{{ $prog->name }}</td>
                                <td class="text-center">{{ $prog->male_students }}</td>
                                <td class="text-center">{{ $prog->female_students }}</td>
                                <td class="text-center fw-bold">{{ $prog->total_students }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No program data recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Academic Standing Distribution -->
    <div class="col-lg-4">
        <div class="card p-4 shadow-sm bg-white border-0">
            <h5 class="fw-bold mb-3">Academic Standing Overview</h5>
            <ul class="list-group list-group-flush">
                @php
                    $standings = ['Distinction', 'Very Great Distinction', 'Good Standing', 'Academic Warning', 'Academic Dismissal'];
                @endphp
                @foreach($standings as $stand)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span>{{ $stand }}</span>
                        <span class="badge bg-secondary rounded-pill">
                            {{ $standingSummary[$stand] ?? 0 }} Students
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection