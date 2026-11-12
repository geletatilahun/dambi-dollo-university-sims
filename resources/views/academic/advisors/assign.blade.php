@extends('layouts.app')
@section('title', 'Assign Academic Advisors')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Academic Advisor Assignment Matrix</h4>
            <p class="text-muted mb-0 small">Pair faculty members with students to monitor progression and provide counseling.</p>
        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Current Advisor</th>
                    <th class="text-end">Assign / Reassign Advisor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $st)
                    <tr>
                        <td><strong>{{ $st->student_id_number }}</strong></td>
                        <td>{{ $st->first_name }} {{ $st->last_name }}</td>
                        <td>{{ $st->program->name }}</td>
                        <td>
                            @if($st->advisor)
                                <span class="badge bg-light text-primary border">
                                    <i class="bi bi-person-badge-fill me-1"></i>{{ $st->advisor->name }}
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">Unassigned</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('academic.advisors.assign.store') }}" method="POST" class="d-inline-flex gap-1 justify-content-end">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $st->id }}">
                                <select name="advisor_user_id" class="form-select form-select-sm" style="max-width: 200px;" required>
                                    <option value="">-- Choose Advisor --</option>
                                    @foreach($advisors as $adv)
                                        <option value="{{ $adv->id }}" {{ $st->academic_advisor_user_id == $adv->id ? 'selected' : '' }}>
                                            {{ $adv->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No registered students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $students->links('pagination::bootstrap-5') }}
</div>
@endsection