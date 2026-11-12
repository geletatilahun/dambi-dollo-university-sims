@extends('layouts.app')
@section('title', 'My Assigned Advisees')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Academic Advising: Student Cohort</h4>
            <p class="text-muted mb-0 small">Monitor cumulative progress, detect students on academic warning, and log interventions.</p>
        </div>
        <div>
            <span class="badge bg-light text-dark border px-3 py-2">
                Total Advisees: <strong>{{ $advisees->count() }} Students</strong>
            </span>
        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Program</th>
                    <th>Semester GPA</th>
                    <th>Cumulative GPA</th>
                    <th>Academic Standing</th>
                    <th>Risk Status</th>
                    <th class="text-end">Dossier</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advisees as $item)
                    <tr class="{{ $item['is_at_risk'] ? 'table-warning table-opacity-25' : '' }}">
                        <td><strong>{{ $item['student']->student_id_number }}</strong></td>
                        <td>{{ $item['student']->first_name }} {{ $item['student']->middle_name }} {{ $item['student']->last_name }}</td>
                        <td><small>{{ $item['student']->program->code }}</small></td>
                        <td>{{ number_format($item['sgpa'], 2) }}</td>
                        <td>
                            <strong class="{{ $item['cgpa'] < 2.0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($item['cgpa'], 2) }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge 
                                {{ in_array($item['standing'], ['Distinction', 'Very Great Distinction']) ? 'bg-success' : '' }}
                                {{ $item['standing'] === 'Good Standing' ? 'bg-primary' : '' }}
                                {{ in_array($item['standing'], ['Academic Warning', 'Probation']) ? 'bg-warning text-dark' : '' }}
                                {{ $item['standing'] === 'Academic Dismissal' ? 'bg-danger' : '' }}
                            ">
                                {{ $item['standing'] }}
                            </span>
                        </td>
                        <td>
                            @if($item['is_at_risk'])
                                <span class="badge bg-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> At-Risk
                                </span>
                            @else
                                <span class="badge bg-light text-success border">
                                    <i class="bi bi-check-circle me-1"></i> Satisfactory
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('advisor.show', $item['student']->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-folder2-open me-1"></i> Advise & Log
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-people fs-3 d-block mb-1"></i>
                            No advisees currently assigned to your advisor profile. Contact your Department Head.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection