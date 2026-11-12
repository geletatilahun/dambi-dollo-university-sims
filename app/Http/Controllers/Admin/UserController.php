<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = $request->get('role', 'All');

        $query = User::with('roles')->latest();

        if ($roleFilter !== 'All') {
            $query->whereHas('roles', fn($q) => $q->where('name', $roleFilter));
        }

        $users = $query->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles', 'roleFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->attach($validated['role_id']);

        $role = Role::find($validated['role_id']);
        AuditLog::record('USER_CREATED', "Created user {$user->name} ({$user->email}) with role {$role->name}");

        return back()->with('success', "User account for {$user->name} created successfully!");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|string|min:6']);

        $user->update(['password' => Hash::make($request->password)]);

        AuditLog::record('PASSWORD_RESET', "Reset password for user {$user->email}");

        return back()->with('success', "Password for {$user->name} updated successfully!");
    }
}