@extends('core::layouts.app')

@section('title', 'Edit Role: ' . $role->label)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Role: {{ $role->label }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Modify role properties and adjust granted permissions</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-outline">Back to Roles</a>
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

<form method="POST" action="{{ route('roles.update', $role) }}">
    @csrf
    @method('PUT')

    <div class="card" style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: #0f172a;">Role Details</h2>

        <div class="form-row">
            <div class="form-group">
                <label for="label">Role Title (Display Name) *</label>
                <input type="text" id="label" name="label" class="form-control" value="{{ old('label', $role->label) }}" required>
            </div>

            <div class="form-group">
                <label for="name">Identifier Code *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $role->name) }}" {{ $role->name === 'admin' ? 'readonly' : 'required' }}>
                @if($role->name === 'admin')
                    <small style="color: var(--text-muted); font-size: 0.75rem;">System role code cannot be modified.</small>
                @endif
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="description">Role Description</label>
            <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
        </div>
    </div>

    <!-- Permissions Matrix -->
    <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0;">Permission Matrix</h2>
            <p style="color: var(--text-muted); font-size: 0.8125rem; margin-top: 2px;">
                Permissions grouped by module and controller. Friendly column headers indicate permitted actions.
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn btn-outline btn-sm" onclick="toggleAllMatrix(true)">Select All</button>
            <button type="button" class="btn btn-outline btn-sm" onclick="toggleAllMatrix(false)">Deselect All</button>
        </div>
    </div>

    @php
        $rolePermNames = old('permissions', $role->permissions->pluck('name')->toArray());
    @endphp

    @foreach($groupedPermissions as $moduleKey => $moduleData)
        <div class="card" style="margin-bottom: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 1.1rem;">📦</span>
                    <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $moduleData['label'] }}</h3>
                </div>
                <button type="button" class="btn btn-outline btn-sm" style="font-size: 0.75rem; padding: 2px 8px;" onclick="toggleModuleCheckboxes('{{ $moduleKey }}')">
                    Toggle Module
                </button>
            </div>

            <div class="table-wrap">
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Permission</th>
                            @foreach($actions as $actionKey => $actionLabel)
                                <th style="text-align: center; width: 85px;">{{ $actionLabel }}</th>
                            @endforeach
                            <th style="text-align: center; width: 70px;">All</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($moduleData['controllers'] as $controllerKey => $controllerData)
                            @php
                                $rowId = "row-{$moduleKey}-{$controllerKey}";
                            @endphp
                            <tr>
                                <td>
                                    <strong style="color: #0f172a; font-size: 0.875rem;">{{ $controllerData['label'] }}</strong>
                                </td>
                                @foreach($actions as $actionKey => $actionLabel)
                                    @php
                                        $perm = $controllerData['permissions']->firstWhere('action', $actionKey);
                                        $isChecked = $perm && in_array($perm->name, $rolePermNames, true);
                                    @endphp
                                    <td style="text-align: center;">
                                        @if($perm)
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $perm->name }}"
                                                   class="perm-check mod-{{ $moduleKey }} {{ $rowId }}"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   title="{{ $perm->action_label }}"
                                                   style="cursor: pointer; width: 16px; height: 16px;">
                                        @else
                                            <span style="color: #cbd5e1; font-size: 0.875rem;">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td style="text-align: center;">
                                    <input type="checkbox"
                                           class="toggle-row-box"
                                           onclick="toggleRowCheckboxes('{{ $rowId }}', this.checked)"
                                           title="Select all actions for {{ $controllerData['label'] }}"
                                           style="cursor: pointer; width: 16px; height: 16px;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 1.5rem; margin-bottom: 2rem;">
        <a href="{{ route('roles.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-size: 0.95rem;">Update Role</button>
    </div>
</form>

<script>
function toggleAllMatrix(state) {
    document.querySelectorAll('.perm-check').forEach(function(cb) {
        cb.checked = state;
    });
    document.querySelectorAll('.toggle-row-box').forEach(function(cb) {
        cb.checked = state;
    });
}

function toggleModuleCheckboxes(moduleKey) {
    var cbs = document.querySelectorAll('.mod-' + moduleKey);
    var anyUnchecked = false;
    cbs.forEach(function(cb) {
        if (!cb.checked) anyUnchecked = true;
    });
    cbs.forEach(function(cb) {
        cb.checked = anyUnchecked;
    });
}

function toggleRowCheckboxes(rowClass, state) {
    document.querySelectorAll('.' + rowClass).forEach(function(cb) {
        cb.checked = state;
    });
}
</script>
@endsection
