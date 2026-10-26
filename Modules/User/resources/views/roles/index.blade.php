@extends('core::layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Roles & Permissions</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage user roles, functional boundaries, and permission matrix</p>
    </div>
    @can('users:roles:create')
        <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Create Role</a>
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
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Identifier Code</th>
                    <th>Description</th>
                    <th>Assigned Users</th>
                    <th>Permissions</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>
                            <a href="{{ route('roles.show', $role) }}" style="font-weight: 700; color: #0f172a; text-decoration: none;">
                                {{ $role->label }}
                            </a>
                            @if($role->name === 'admin')
                                <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.7rem; margin-left: 4px;">System</span>
                            @endif
                        </td>
                        <td>
                            <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 0.8125rem; color: #475569;">
                                {{ $role->name }}
                            </code>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.875rem; max-width: 320px;">
                            {{ $role->description ?? '—' }}
                        </td>
                        <td>
                            <span class="badge" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155;">
                                {{ $role->users_count }} users
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;">
                                {{ $role->name === 'admin' ? 'All' : $role->permissions_count }} permissions
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                @can('users:roles:view')
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-outline btn-sm">View</a>
                                @endcan
                                @can('users:roles:edit')
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline btn-sm">Edit</a>
                                @endcan
                                @can('users:roles:delete')
                                    @if($role->name !== 'admin')
                                        <form method="POST" action="{{ route('roles.destroy', $role) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete role \'{{ $role->label }}\'?');">
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
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No roles defined.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

