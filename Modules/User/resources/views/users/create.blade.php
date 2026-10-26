@extends('core::layouts.app')

@section('title', 'Create User')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create User</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Add a new system user and assign roles</p>
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
    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Dr. John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="e.g. user@example.com">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Repeat password">
            </div>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Assign Roles</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 10px; margin-top: 0.5rem;">
                @foreach($roles as $role)
                    <label style="display: flex; align-items: flex-start; gap: 8px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; cursor: pointer; background: #f8fafc;">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }} style="margin-top: 3px;">
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
            <button type="submit" class="btn btn-primary">Create User</button>
        </div>
    </form>
</div>
@endsection
