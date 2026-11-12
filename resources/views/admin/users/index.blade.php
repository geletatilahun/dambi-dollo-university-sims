@extends('layouts.app')
@section('title', 'User Administration')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">User Account Administration</h4>
        <p class="text-muted mb-0">Create and manage accounts for university staff, lecturers, and officers.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="bi bi-person-plus-fill me-1"></i> Add New User
    </button>
</div>

<!-- Role Filter Tabs -->
<div class="card p-3 mb-3 bg-white border-0 shadow-sm">
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.users.index', ['role' => 'All']) }}" class="btn btn-sm {{ $roleFilter === 'All' ? 'btn-primary' : 'btn-outline-secondary' }}">All Users</a>
        @foreach($roles as $r)
            <a href="{{ route('admin.users.index', ['role' => $r->name]) }}" class="btn btn-sm {{ $roleFilter === $r->name ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $r->display_name }}
            </a>
        @endforeach
    </div>
</div>

<div class="card p-4 shadow-sm bg-white border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Assigned Roles</th>
                    <th>Account Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td><strong>{{ $u->name }}</strong></td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @foreach($u->roles as $role)
                                <span class="badge 
                                    {{ $role->name === 'SuperAdmin' ? 'bg-danger' : '' }}
                                    {{ $role->name === 'Instructor' ? 'bg-info text-dark' : '' }}
                                    {{ $role->name === 'Student' ? 'bg-success' : '' }}
                                    {{ !in_array($role->name, ['SuperAdmin', 'Instructor', 'Student']) ? 'bg-secondary' : '' }}
                                    me-1
                                ">
                                    {{ $role->display_name }}
                                </span>
                            @endforeach
                        </td>
                        <td>{{ $u->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-warning text-dark" data-bs-toggle="modal" data-bs-target="#resetModal{{ $u->id }}">
                                <i class="bi bi-key-fill me-1"></i> Reset Password
                            </button>

                            <!-- Password Reset Modal -->
                            <div class="modal fade text-start" id="resetModal{{ $u->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.users.resetPassword', $u->id) }}" method="POST" class="modal-content">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Reset Password: {{ $u->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label small fw-semibold">New Password *</label>
                                            <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-warning">Update Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No users found for this role filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links('pagination::bootstrap-5') }}
</div>

<!-- Modal: Create User -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create University User Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Full Legal Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Taye Belachew" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Institutional Email *</label>
                    <input type="email" name="email" class="form-control" placeholder="name@ddu.edu.et" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Assign System Role *</label>
                    <select name="role_id" class="form-select" required>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->display_name }} ({{ $r->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Initial Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Create User Account</button>
            </div>
        </form>
    </div>
</div>
@endsection