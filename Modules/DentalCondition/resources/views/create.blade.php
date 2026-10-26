@extends('core::layouts.app')

@section('title', 'New Dental Condition')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Add Dental Condition</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Create a condition for tooth findings and legend</p>
    </div>
    <a href="{{ route('dental-conditions.index') }}" class="btn btn-outline">Back to Conditions</a>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form method="POST" action="{{ route('dental-conditions.store') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="name">Condition Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Caries / decay" required>
            </div>
            <div class="form-group">
                <label for="code">Unique Code *</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code') }}" placeholder="e.g. caries" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="default_color">Default Marking Color *</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="color" id="default_color" name="default_color" value="{{ old('default_color', '#d83c43') }}" style="width: 50px; height: 38px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; padding: 2px;">
                    <span style="font-size: 0.8125rem; color: var(--text-muted);">Hex color used on dental chart polygons</span>
                </div>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="finding" {{ old('category') === 'finding' ? 'selected' : '' }}>Finding / Pathology</option>
                    <option value="treatment" {{ old('category') === 'treatment' ? 'selected' : '' }}>Treatment / Restoration</option>
                    <option value="planned" {{ old('category') === 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="2" placeholder="Clinical description or instructions...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <span>Active (show in interactive chart modal & color guide)</span>
            </label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 1.5rem;">
            <a href="{{ route('dental-conditions.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Condition</button>
        </div>
    </form>
</div>
@endsection

