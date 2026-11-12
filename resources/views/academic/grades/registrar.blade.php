@extends('layouts.app')
@section('title', 'Official Grade Publication')

@section('content')
<div class="card p-4">
    <h4 class="fw-bold mb-1">Registrar Result Publication</h4>
    <p class="text-muted">Publishing grades locks the records and calculates SGPA and CGPA for all students.</p>

    <table class="table table-hover align-middle mt-3">
        <thead class="table-light">
            <tr>
                <th>Course</th>
                <th>Section</th>
                <th>Semester</th>
                <th>Current Status</th>
                <th>Publication Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gradeSheets as $sheet)
                <tr>
                    <td><strong>{{ $sheet->courseOffering->course->code }}</strong> - {{ $sheet->courseOffering->course->title }}</td>
                    <td><span class="badge bg-secondary">{{ $sheet->courseOffering->section }}</span></td>
                    <td>{{ $sheet->courseOffering->semester->name }}</td>
                    <td>
                        <span class="badge {{ $sheet->status === 'Published' ? 'bg-success' : 'bg-info text-dark' }}">
                            {{ $sheet->status }}
                        </span>
                    </td>
                    <td>
                        @if($sheet->status === 'DepartmentEndorsed')
                            <form action="{{ route('academic.grades.publish', $sheet->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-megaphone-fill me-1"></i> Publish Officially
                                </button>
                            </form>
                        @else
                            <span class="badge bg-light text-success border border-success"><i class="bi bi-shield-lock-fill"></i> Published</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted text-center py-4">No endorsed grade sheets awaiting publication.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection