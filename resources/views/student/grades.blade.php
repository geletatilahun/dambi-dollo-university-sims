@extends('layouts.app')
@section('title', 'My Grade Report')

@section('content')
<div class="card p-4">
    <h4 class="fw-bold mb-3">Academic Grade Report</h4>
    @forelse($registrations as $reg)
        <h5 class="mt-2">{{ $reg->semester->name }}</h5>
        <table class="table table-bordered mb-4">
            <thead class="table-light">
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Credit Hours</th>
                    <th>Final Score</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reg->registrationCourses as $item)
                    <tr>
                        <td><strong>{{ $item->courseOffering->course->code }}</strong></td>
                        <td>{{ $item->courseOffering->course->title }}</td>
                        <td>{{ $item->courseOffering->course->credit_hours }}</td>
                        <td>{{ $item->final_score ?? 'Pending' }}</td>
                        <td><span class="badge bg-info text-dark">{{ $item->grade_letter ?? 'In Progress' }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p class="text-muted">No grade history recorded.</p>
    @endforelse
</div>
@endsection