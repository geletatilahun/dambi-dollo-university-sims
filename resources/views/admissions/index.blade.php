@extends('layouts.app')
@section('title', 'Admission Queue')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Applicant Admissions Queue</h4>
            <p class="text-muted mb-0">Review submitted admission requests and enroll qualified candidates.</p>
        </div>
        <!-- Status Filter -->
        <div class="btn-group">
            <a href="{{ route('admissions.queue', ['status' => 'All']) }}" class="btn btn-sm {{ $status === 'All' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
            <a href="{{ route('admissions.queue', ['status' => 'Submitted']) }}" class="btn btn-sm {{ $status === 'Submitted' ? 'btn-primary' : 'btn-outline-primary' }}">Pending</a>
            <a href="{{ route('admissions.queue', ['status' => 'Accepted']) }}" class="btn btn-sm {{ $status === 'Accepted' ? 'btn-primary' : 'btn-outline-primary' }}">Accepted</a>
            <a href="{{ route('admissions.queue', ['status' => 'Enrolled']) }}" class="btn btn-sm {{ $status === 'Enrolled' ? 'btn-primary' : 'btn-outline-primary' }}">Enrolled</a>
        </div>
    </div>

    <table class="table table-hover align-middle mt-2">
        <thead class="table-light">
            <tr>
                <th>App Number</th>
                <th>Full Name</th>
                <th>Desired Program</th>
                <th>Study Mode</th>
                <th>Date Applied</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $app)
                <tr>
                    <td><strong>{{ $app->application_number }}</strong></td>
                    <td>{{ $app->first_name }} {{ $app->middle_name }} {{ $app->last_name }}</td>
                    <td>{{ $app->program->name }}</td>
                    <td>{{ $app->study_mode }}</td>
                    <td>{{ $app->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="badge 
                            {{ $app->status === 'Submitted' ? 'bg-warning text-dark' : '' }}
                            {{ $app->status === 'Accepted' ? 'bg-info text-dark' : '' }}
                            {{ $app->status === 'Enrolled' ? 'bg-success' : '' }}
                            {{ $app->status === 'Rejected' ? 'bg-danger' : '' }}
                        ">
                            {{ $app->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admissions.show', $app->id) }}" class="btn btn-sm btn-outline-primary">
                            Review File &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No applications found in this queue.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $applications->links('pagination::bootstrap-5') }}
</div>
@endsection