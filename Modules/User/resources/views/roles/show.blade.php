@extends('core::layouts.app')

@section('title', 'Role: ' . $role->label)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $role->label }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Role definition, assigned users, and granted permissions</p>
    </div>
    <div style="display: flex; gap: 8px;">
        @can('users:roles:edit')
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">Edit Role</a>
        @endcan
        <a href="{{ route('roles.index') }}" class="btn btn-outline">Back to Roles</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">
    <!-- Role Summary Card -->
    <div class="card">
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.75rem; color: #0f172a;">{{ $role->label }}</h2>
        <div style="margin-bottom: 0.75rem;">
            <code style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 0.8125rem; color: #334155;">{{ $role->name }}</code>
            @if($role->name === 'admin')
                <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.7rem; margin-left: 4px;">System</span>
            @endif
        </div>

        @if($role->description)
            <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">
                {{ $role->description }}
            </p>
        @endif

        <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem; margin-top: 1rem;">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.5rem;">
                Assigned Users ({{ $role->users->count() }})
            </div>
            <div style="display: flex; flex-direction: column; gap: 6px; max-height: 240px; overflow-y: auto;">
                @forelse($role->users as $user)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 8px; background: #f8fafc; border-radius: 6px; border: 1px solid #f1f5f9;">
                        <span style="font-size: 0.875rem; color: #1e293b; font-weight: 500;">{{ $user->name }}</span>
                        <a href="{{ route('users.show', $user) }}" style="font-size: 0.75rem; color: var(--primary); text-decoration: none;">View</a>
                    </div>
                @empty
                    <span style="color: var(--text-muted); font-size: 0.875rem;">No users assigned to this role</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Granted Permissions Matrix -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: #0f172a;">Granted Permissions</h2>
                <p style="color: var(--text-muted); font-size: 0.8125rem; margin-top: 2px;">
                    Active permissions for this role grouped by module and controller.
                </p>
            </div>
            @if($role->name === 'admin')
                <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                    All Permissions Granted
                </span>
            @else
                <span class="badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
                    {{ $role->permissions->count() }} active permissions
                </span>
            @endif
        </div>

        @foreach($groupedPermissions as $moduleKey => $moduleData)
            <div style="margin-bottom: 1.25rem; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                <div style="background: #f8fafc; padding: 8px 14px; font-weight: 700; font-size: 0.875rem; color: #1e293b; border-bottom: 1px solid #e2e8f0;">
                    {{ $moduleData['label'] }}
                </div>
                <div class="table-wrap">
                    <table style="width: 100%; margin: 0;">
                        <thead>
                            <tr>
                                <th style="min-width: 160px;">Permission</th>
                                @foreach($actions as $actionKey => $actionLabel)
                                    <th style="text-align: center; width: 85px;">{{ $actionLabel }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moduleData['controllers'] as $controllerKey => $controllerData)
                                <tr>
                                    <td>
                                        <strong style="color: #0f172a; font-size: 0.875rem;">{{ $controllerData['label'] }}</strong>
                                    </td>
                                    @foreach($actions as $actionKey => $actionLabel)
                                        @php
                                            $perm = $controllerData['permissions']->firstWhere('action', $actionKey);
                                            $isGranted = $role->name === 'admin' || ($perm && $role->hasPermission($perm->name));
                                        @endphp
                                        <td style="text-align: center;">
                                            @if($perm)
                                                @if($isGranted)
                                                    <span style="display: inline-block; width: 22px; height: 22px; line-height: 22px; border-radius: 50%; background: #dcfce7; color: #166534; font-weight: 700; font-size: 0.75rem;" title="{{ $perm->action_label }}: Granted">✓</span>
                                                @else
                                                    <span style="display: inline-block; width: 22px; height: 22px; line-height: 22px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; font-size: 0.75rem;" title="{{ $perm->action_label }}: Denied">✕</span>
                                                @endif
                                            @else
                                                <span style="color: #cbd5e1;">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

