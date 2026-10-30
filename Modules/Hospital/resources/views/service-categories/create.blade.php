@extends('core::layouts.app')

@section('title', 'New Service Category — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">New Service Category</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Create a new service category for hospital treatments and procedures</p>
    </div>
    <div>
        <a href="{{ route('hospital.service-categories.index') }}" class="btn btn-outline">← Back to Service Categories</a>
    </div>
</div>

<div class="card" style="max-width: 680px;">
    <form method="POST" action="{{ route('hospital.service-categories.store') }}">
        @csrf

        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Category Code
            </label>
            <input type="text" class="form-control" value="Auto-generated upon save (Sequential)" disabled style="background: #f8fafc; color: #64748b;">
            <span style="font-size: 0.75rem; color: #64748b;">The code will be automatically assigned sequentially for Service categories.</span>
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="name" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Category Name <span style="color: #dc2626;">*</span>
            </label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Dental Consultation, Oral Surgery" required>
            @error('name')
                <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Description
            </label>
            <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Optional details about this category">{{ old('description') }}</textarea>
            @error('description')
                <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.875rem;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                <span>Active (Available for service assignments)</span>
            </label>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Save Category</button>
            <a href="{{ route('hospital.service-categories.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection

