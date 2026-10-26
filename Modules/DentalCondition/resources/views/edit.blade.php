@extends('core::layouts.app')

@section('title', 'Edit Dental Condition - ' . $dentalCondition->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Condition: {{ $dentalCondition->name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Modify condition settings and color</p>
    </div>
    <a href="{{ route('dental-conditions.index') }}" class="btn btn-outline">Back to Conditions</a>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form method="POST" action="{{ route('dental-conditions.update', $dentalCondition) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="name">Condition Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $dentalCondition->name) }}" required>
            </div>
            <div class="form-group">
                <label for="code">Unique Code *</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', $dentalCondition->code) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="default_color">Default Marking Color *</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="color" id="default_color" name="default_color" value="{{ old('default_color', $dentalCondition->default_color) }}" style="width: 50px; height: 38px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; padding: 2px;">
                    <span style="font-size: 0.8125rem; color: var(--text-muted);">Hex color used on dental chart polygons</span>
                </div>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="finding" {{ old('category', $dentalCondition->category) === 'finding' ? 'selected' : '' }}>Finding / Pathology</option>
                    <option value="treatment" {{ old('category', $dentalCondition->category) === 'treatment' ? 'selected' : '' }}>Treatment / Restoration</option>
                    <option value="planned" {{ old('category', $dentalCondition->category) === 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="other" {{ old('category', $dentalCondition->category) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $dentalCondition->description) }}</textarea>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', (string)$dentalCondition->is_active) == '1' ? 'checked' : '' }}>
                <span>Active (show in interactive chart modal & color guide)</span>
            </label>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
            <button type="button" class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete this condition?')) document.getElementById('delete-condition-form').submit();">Delete Condition</button>

            <div style="display: flex; gap: 8px;">
                <a href="{{ route('dental-conditions.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Condition</button>
            </div>
        </div>
    </form>

    <form id="delete-condition-form" action="{{ route('dental-conditions.destroy', $dentalCondition) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
