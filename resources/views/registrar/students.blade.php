@extends('layouts.app')
@section('title', 'Student Registry')

@section('content')
<div class="card p-4">
    <h4 class="fw-bold mb-3">Official Student Registry</h4>
    <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Program</th>
                <th>Study Mode</th>
                <th>Status</th>
                <th>Status</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $st)
                <tr>
                    <td><strong>{{ $st->student_id_number }}</strong></td>
                    <td>{{ $st->first_name }} {{ $st->middle_name }} {{ $st->last_name }}</td>
                    <td>{{ $st->program->name }}</td>
                    <td>{{ $st->study_mode }}</td>
                    <td><span class="badge bg-success">{{ $st->status }}</span></td>
                    <td><span class="badge bg-success">{{ $st->status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('registrar.students.show', $st->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-person-lines-fill me-1"></i> Manage Dossier
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $students->links('pagination::bootstrap-5') }}
</div>
@endsection