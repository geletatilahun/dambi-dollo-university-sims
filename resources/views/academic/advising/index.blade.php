@extends('layouts.app')
@section('title', 'Academic Advisees')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Academic Advisor Portal</h4>
            <p class="text-muted mb-0">Monitor your assigned advisees' CGPA, standing, and warnings.</p>
        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Latest CGPA</th>
                    <th>Academic Standing</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advisees as $st)
                    @php
                        $latest = $st->semesterResults->first();
                    @endphp
                    <tr>
                        <td><strong>{{ $st->student_id_number }}</strong></td>
                        <td>{{ $st->first_name }} {{ $st->last_name }}</td>
                        <td>{{ $st->program->code }}</td>
                        <td><strong class="text-success">{{ number_format($latest?->cgpa ?? 0.00, 2) }}</strong></td>
                        <td>
                            <span class="badge 
                                {{ in_array($latest?->academic_standing, ['Academic Warning', 'Academic Dismissal']) ? 'bg-danger' : 'bg-success' }}
                            ">
                                {{ $latest?->academic_standing ?? 'Good Standing' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('academic.advising.show', $st->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-left-dots me-1"></i> Advising Dossier &rarr;
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No advisees currently assigned to your advisor profile.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $advisees->links('pagination::bootstrap-5') }}
</div>
@endsection