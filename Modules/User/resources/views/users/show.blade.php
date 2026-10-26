@extends('core::layouts.app')

@section('title', 'User: ' . $user->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $user->name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">User profile, assigned roles, and effective permission grants</p>
    </div>
    <div style="display: flex; gap: 8px;">
        @can('users:users:edit')
            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit User</a>
        @endcan
        <a href="{{ route('users.index') }}" class="btn btn-outline">Back to Users</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">
    <!-- Profile Card -->
    <div class="card">
        <div style="text-align: center; margin-bottom: 1.25rem;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: #e0f2fe; color: #0284c7; font-size: 1.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: #0f172a;">{{ $user->name }}</h2>
            @if($user->username)
                <div style="color: #0369a1; font-weight: 600; font-size: 0.875rem; margin-top: 2px;">{{ '@' . $user->username }}</div>
            @endif
            <div style="color: var(--text-muted); font-size: 0.8125rem; margin-top: 2px;">{{ $user->email }}</div>
            @if($user->is_super_addmin)
                <div style="margin-top: 8px;">
                    <span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 0.75rem; font-weight: 700; padding: 3px 8px;">
                        ★ Super Administrator (is_super_addmin: true)
                    </span>
                </div>
            @endif
        </div>

        <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem; margin-top: 0.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.5rem;">
                Assigned Roles
            </div>
            <div style="display: flex; flex-direction: column; gap: 6px;">
                @forelse($user->roles as $role)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                        <span style="font-weight: 600; font-size: 0.875rem; color: #1e293b;">{{ $role->label }}</span>
                        <a href="{{ route('roles.show', $role) }}" style="font-size: 0.75rem; color: var(--primary); text-decoration: none;">View Role &rarr;</a>
                    </div>
                @empty
                    @if($user->is_super_addmin)
                        <div style="background: #fefce8; border: 1px solid #fef08a; padding: 8px 10px; border-radius: 6px; font-size: 0.8125rem; color: #854d0e; line-height: 1.4;">
                            ★ <strong>No roles required.</strong> Unrestricted global permissions are active via <code>is_super_addmin: true</code>.
                        </div>
                    @else
                        <span style="color: var(--text-muted); font-size: 0.875rem;">No roles assigned</span>
                    @endif
                @endforelse
            </div>
        </div>

        <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem; margin-top: 1rem; font-size: 0.8125rem; color: var(--text-muted);">
            <div><strong>Username:</strong> {{ $user->username ? '@' . $user->username : '—' }}</div>
            <div style="margin-top: 4px;"><strong>is_super_addmin:</strong> <span class="badge" style="background: {{ $user->is_super_addmin ? '#ecfdf5' : '#f1f5f9' }}; color: {{ $user->is_super_addmin ? '#065f46' : '#64748b' }}; border: 1px solid {{ $user->is_super_addmin ? '#a7f3d0' : '#e2e8f0' }}; font-size: 0.75rem;">{{ $user->is_super_addmin ? 'true' : 'false' }}</span></div>
            <div style="margin-top: 4px;"><strong>Created:</strong> {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}</div>
            <div style="margin-top: 4px;"><strong>Updated:</strong> {{ $user->updated_at ? $user->updated_at->format('M d, Y') : '—' }}</div>
        </div>
    </div>

    <!-- Effective Permissions Breakdown -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0; color: #0f172a;">Effective Permissions</h2>
                <p style="color: var(--text-muted); font-size: 0.8125rem; margin-top: 2px;">
                    Active permissions granted through assigned roles (displayed with friendly labels).
                </p>
            </div>
            @if($user->is_super_addmin || $user->hasRole('admin'))
                <span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                    Super Administrator (All Access)
                </span>
            @endif
        </div>

        @if($effectivePermissions->isEmpty() && ! $user->is_super_addmin && ! $user->hasRole('admin'))
            <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                This user currently has no granted permissions.
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @foreach($effectivePermissions as $moduleKey => $permissions)
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <div style="background: #f8fafc; padding: 10px 14px; font-weight: 700; font-size: 0.875rem; color: #1e293b; border-bottom: 1px solid #e2e8f0;">
                            {{ \Modules\User\Support\PermissionRegistry::moduleLabel($moduleKey) }}
                        </div>
                        <div style="padding: 12px 14px; display: flex; flex-wrap: wrap; gap: 8px;">
                            @foreach($permissions as $perm)
                                <span class="badge" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.8125rem; padding: 4px 10px;">
                                    <strong>{{ $perm->controller_label }}:</strong> {{ $perm->action_label }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

