@extends('layouts.app')
@section('title', 'Audit Logs')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">System Security & Audit Trail</h4>
            <p class="text-muted mb-0">Immutable record of sensitive administrative, academic, and security actions.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i> Print Log
        </button>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>Actor (User)</th>
                    <th>Event Type</th>
                    <th>Activity Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><small>{{ $log->created_at->format('M d, Y - H:i:s') }}</small></td>
                        <td><strong>{{ $log->user?->name ?? 'System/Guest' }}</strong></td>
                        <td><span class="badge bg-dark">{{ $log->action_event }}</span></td>
                        <td>{{ $log->description }}</td>
                        <td><code>{{ $log->ip_address }}</code></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No audit logs recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $logs->links('pagination::bootstrap-5') }}
</div>
@endsection