@extends('core::layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit User: {{ $user->name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Update user details and assigned roles</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline">Back to Users</a>
</div>

@if ($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        <strong style="display: block; margin-bottom: 0.25rem;">Please correct the following errors:</strong>
        <ul style="margin: 0; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="max-width: 720px; margin: 0 auto;">
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
        </div>

        <div style="background: #f8fafc; padding: 12px 16px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 1.25rem;">
            <div style="font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 4px;">Change Password</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 8px;">Leave blank if you do not want to change the password.</div>

            <div class="form-row">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 6 characters">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Assign Roles</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 10px; margin-top: 0.5rem;">
                @php
                    $assignedRoleIds = old('roles', $user->roles->pluck('id')->toArray());
                @endphp
                @foreach($roles as $role)
                    <label style="display: flex; align-items: flex-start; gap: 8px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; cursor: pointer; background: #ffffff;">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, $assignedRoleIds) ? 'checked' : '' }} style="margin-top: 3px;">
                        <div>
                            <strong style="font-size: 0.875rem; color: #0f172a; display: block;">{{ $role->label }}</strong>
                            @if($role->description)
                                <small style="color: var(--text-muted); font-size: 0.75rem; display: block; line-height: 1.3;">{{ $role->description }}</small>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 1.75rem;">
            <a href="{{ route('users.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>
@endsection

