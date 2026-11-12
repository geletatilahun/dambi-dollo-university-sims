@extends('layouts.app')
@section('title', 'Database Backups')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Database Backup & Disaster Recovery</h4>
        <p class="text-muted mb-0 small">Generate full SQL snapshots of all university student records, marks, and settings.</p>
    </div>
    <form action="{{ route('admin.backups.store') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm fw-semibold" onclick="return confirm('Generate an immediate full database snapshot?')">
            <i class="bi bi-cloud-arrow-down-fill me-1"></i> Generate New Backup
        </button>
    </form>
</div>

@if($errors->has('backup'))
    <div class="alert alert-danger py-2 small">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first('backup') }}
    </div>
@endif

<div class="card p-4 shadow-sm bg-white border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Backup Snapshot Filename</th>
                    <th>File Size</th>
                    <th>Generated Timestamp</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $b)
                    <tr>
                        <td>
                            <i class="bi bi-filetype-sql text-primary fs-5 me-2"></i>
                            <strong>{{ $b['filename'] }}</strong>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $b['size'] }}</span></td>
                        <td><small class="text-muted">{{ $b['created_at'] }}</small></td>
                        <td class="text-end">
                            <a href="{{ route('admin.backups.download', $b['filename']) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download me-1"></i> Download SQL
                            </a>
                            <form action="{{ route('admin.backups.destroy', $b['filename']) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Permanently delete this backup snapshot?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-archive fs-2 d-block mb-2"></i>
                            No backup snapshots generated yet. Click "Generate New Backup" above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection