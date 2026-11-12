@extends('layouts.app')
@section('title', 'Announcements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">University Announcements & Noticeboard</h4>
        <p class="text-muted mb-0">Publish notices to students, instructors, or the campus community.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newNoticeModal">
        <i class="bi bi-megaphone-fill me-1"></i> Post Announcement
    </button>
</div>

<div class="row g-4">
    @forelse($announcements as $notice)
        <div class="col-12">
            <div class="card p-4 shadow-sm bg-white border-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold text-dark mb-0">{{ $notice->title }}</h5>
                    <span class="badge bg-info text-dark">Audience: {{ $notice->target_audience }}</span>
                </div>
                <p class="text-muted small mb-3">
                    Posted by <strong>{{ $notice->author->name }}</strong> on {{ $notice->created_at->format('M d, Y - h:i A') }}
                </p>
                <div class="bg-light p-3 rounded">
                    {!! nl2br(e($notice->content)) !!}
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card p-4 text-center text-muted">No announcements posted yet.</div></div>
    @endforelse
</div>

<!-- Modal: Post Notice -->
<div class="modal fade" id="newNoticeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.announcements.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New University Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Notice Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Add/Drop Registration Window Deadline" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Target Audience *</label>
                    <select name="target_audience" class="form-select" required>
                        <option value="All">All University Users</option>
                        <option value="Students">Students Only</option>
                        <option value="Instructors">Instructors Only</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Notice Content *</label>
                    <textarea name="content" class="form-control" rows="5" placeholder="Compose announcement details..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Publish to Campus</button>
            </div>
        </form>
    </div>
</div>
@endsection