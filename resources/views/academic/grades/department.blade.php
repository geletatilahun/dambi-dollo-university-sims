@extends('layouts.app')
@section('title', 'Department Grade Endorsement')

@section('content')
<div class="card p-4">
    <h4 class="fw-bold mb-1">Department Grade Approval Queue</h4>
    <p class="text-muted">Review grade sheets submitted by course lecturers.</p>

    <table class="table table-hover align-middle mt-3">
        <thead class="table-light">
            <tr>
                <th>Course</th>
                <th>Section</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gradeSheets as $sheet)
                <tr>
                    <td><strong>{{ $sheet->courseOffering->course->code }}</strong> - {{ $sheet->courseOffering->course->title }}</td>
                    <td><span class="badge bg-secondary">{{ $sheet->courseOffering->section }}</span></td>
                    <td>{{ $sheet->courseOffering->semester->name }}</td>
                    <td>
                        <span class="badge {{ $sheet->status === 'Submitted' ? 'bg-warning text-dark' : 'bg-success' }}">
                            {{ $sheet->status }}
                        </span>
                    </td>
                    <td>
                        @if($sheet->status === 'Submitted')
                            <form action="{{ route('academic.grades.endorse', $sheet->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Endorse & Forward
                                </button>
                            </form>
                        @else
                            <span class="text-muted small"><i class="bi bi-check2-all"></i> Endorsed</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted text-center py-4">No grade sheets submitted for review.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection