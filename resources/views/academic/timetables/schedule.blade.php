@extends('layouts.app')
@section('title', 'My Weekly Schedule')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">My Weekly Lecture Timetable</h4>
        <p class="text-muted mb-0">Active Term: <strong class="text-primary">{{ $activeSemester?->name ?? 'Current Term' }}</strong></p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-printer me-1"></i> Print Timetable
    </button>
</div>

<div class="row g-3">
    @foreach($days as $day)
        <div class="col-md-4 col-lg-2">
            <div class="card h-100 shadow-sm border-0 bg-white">
                <div class="card-header bg-light border-0 py-2 text-center">
                    <h6 class="fw-bold mb-0 text-dark">{{ $day }}</h6>
                </div>
                <div class="card-body p-2 d-flex flex-column gap-2">
                    @forelse($schedules->get($day, []) as $slot)
                        <div class="p-2 rounded border border-primary border-opacity-25 bg-primary bg-opacity-10">
                            <div class="small fw-bold text-primary">{{ $slot->courseOffering->course->code }}</div>
                            <div class="text-muted" style="font-size: 0.76rem;">{{ $slot->courseOffering->course->title }}</div>
                            <hr class="my-1 border-primary border-opacity-25">
                            <div class="small text-dark fw-semibold" style="font-size: 0.78rem;">
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                            </div>
                            <div class="small text-danger fw-semibold" style="font-size: 0.75rem;">
                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $slot->classroom->room_number }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted small py-4 my-auto">
                            <em>No classes</em>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection