@extends('layouts.app')
@section('title', 'Graduation Clearance')

@section('content')
<div class="card p-4 shadow-sm bg-white border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Graduation Clearance Matrix</h4>
            <p class="text-muted mb-0">Multi-office approval grid across Department, Library, Student Affairs, and Finance.</p>
        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th class="text-center">Dept</th>
                    <th class="text-center">Library</th>
                    <th class="text-center">Services</th>
                    <th class="text-center">Finance</th>
                    <th class="text-center">Overall Clearance</th>
                    <th class="text-end">Final Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidates as $c)
                    <tr>
                        <td><strong>{{ $c->student->student_id_number }}</strong></td>
                        <td>{{ $c->student->first_name }} {{ $c->student->last_name }}</td>
                        <td>{{ $c->student->program->code }}</td>
                        
                        <!-- 1. Dept Check -->
                        <td class="text-center">
                            <form action="{{ route('registrar.clearance.signoff', $c->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="office" value="status_department">
                                <input type="hidden" name="action" value="{{ $c->status_department === 'Cleared' ? 'Pending' : 'Cleared' }}">
                                <button type="submit" class="btn btn-xs btn-{{ $c->status_department === 'Cleared' ? 'success' : 'outline-secondary' }} btn-sm py-0">
                                    {{ $c->status_department }}
                                </button>
                            </form>
                        </td>

                        <!-- 2. Library Check -->
                        <td class="text-center">
                            <form action="{{ route('registrar.clearance.signoff', $c->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="office" value="status_library">
                                <input type="hidden" name="action" value="{{ $c->status_library === 'Cleared' ? 'Pending' : 'Cleared' }}">
                                <button type="submit" class="btn btn-xs btn-{{ $c->status_library === 'Cleared' ? 'success' : 'outline-secondary' }} btn-sm py-0">
                                    {{ $c->status_library }}
                                </button>
                            </form>
                        </td>

                        <!-- 3. Student Affairs Check -->
                        <td class="text-center">
                            <form action="{{ route('registrar.clearance.signoff', $c->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="office" value="status_services">
                                <input type="hidden" name="action" value="{{ $c->status_services === 'Cleared' ? 'Pending' : 'Cleared' }}">
                                <button type="submit" class="btn btn-xs btn-{{ $c->status_services === 'Cleared' ? 'success' : 'outline-secondary' }} btn-sm py-0">
                                    {{ $c->status_services }}
                                </button>
                            </form>
                        </td>

                        <!-- 4. Finance Check -->
                        <td class="text-center">
                            <form action="{{ route('registrar.clearance.signoff', $c->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="office" value="status_finance">
                                <input type="hidden" name="action" value="{{ $c->status_finance === 'Cleared' ? 'Pending' : 'Cleared' }}">
                                <button type="submit" class="btn btn-xs btn-{{ $c->status_finance === 'Cleared' ? 'success' : 'outline-secondary' }} btn-sm py-0">
                                    {{ $c->status_finance }}
                                </button>
                            </form>
                        </td>

                        <!-- Overall Status -->
                        <td class="text-center">
                            <span class="badge 
                                {{ $c->overall_status === 'InClearance' ? 'bg-warning text-dark' : '' }}
                                {{ $c->overall_status === 'ClearedForGraduation' ? 'bg-info text-dark' : '' }}
                                {{ $c->overall_status === 'Graduated' ? 'bg-success' : '' }}
                            ">
                                {{ $c->overall_status }}
                            </span>
                        </td>

                        <!-- Final Graduation Action -->
                        <td class="text-end">
                            @if($c->overall_status === 'ClearedForGraduation')
                                <form action="{{ route('registrar.clearance.graduate', $c->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success fw-semibold" onclick="return confirm('Confer degree and graduate student?')">
                                        <i class="bi bi-award-fill me-1"></i> Graduate
                                    </button>
                                </form>
                            @elseif($c->overall_status === 'Graduated')
                                <span class="text-success small fw-bold"><i class="bi bi-mortarboard-fill"></i> Graduated</span>
                            @else
                                <span class="text-muted small">Pending clearances</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No graduation clearance applications recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $candidates->links('pagination::bootstrap-5') }}
</div>
@endsection