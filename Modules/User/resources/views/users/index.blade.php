@extends('core::layouts.app')

@section('title', 'User Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Users</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage system user accounts, roles, and login access</p>
    </div>
    @can('users:users:create')
        <a href="{{ route('users.create') }}" class="btn btn-primary">+ Create User</a>
    @endcan
</div>

@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <form method="GET" action="{{ route('users.index') }}" style="display: flex; gap: 8px; margin-bottom: 1rem; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, username or email..." class="form-control" style="max-width: 320px;">
        
        <select name="role_id" class="form-control" style="max-width: 200px;">
            <option value="">All Roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                    {{ $role->label }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-outline">Filter</button>
        @if(request('search') || request('role_id'))
            <a href="{{ route('users.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Assigned Roles</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 34px; height: 34px; border-radius: 50%; background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('users.show', $user) }}" style="font-weight: 600; color: #0f172a; text-decoration: none;">
                                        {{ $user->name }}
                                    </a>
                                    @if($user->username)
                                        <div style="font-size: 0.75rem; color: #0369a1; font-weight: 500;">
                                            {{ '@' . $user->username }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 0.875rem;">{{ $user->email }}</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                @forelse($user->roles as $role)
                                    <span class="badge" style="background: #f1f5f9; color: #334155; font-size: 0.75rem; border: 1px solid #cbd5e1;">
                                        {{ $role->label }}
                                    </span>
                                @empty
                                    <span style="color: var(--text-muted); font-size: 0.75rem;">No roles</span>
                                @endforelse
                            </div>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.8125rem;">
                            {{ $user->created_at ? $user->created_at->format('Y-m-d') : '—' }}
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                @can('users:users:view')
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline btn-sm">View</a>
                                @endcan
                                @can('users:users:edit')
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline btn-sm">Edit</a>
                                @endcan
                                @can('users:users:delete')
                                    @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete user \'{{ $user->name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #fca5a5;">Delete</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div style="margin-top: 1rem;">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

