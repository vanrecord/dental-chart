@extends('core::layouts.app')

@section('title', 'Edit Product Category — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Product Category</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Modify product category #{{ $category->code }} — {{ $category->name }}</p>
    </div>
    <div>
        <a href="{{ route('hospital.product-categories.index') }}" class="btn btn-outline">← Back to Product Categories</a>
    </div>
</div>

<div class="card" style="max-width: 680px;">
    <form method="POST" action="{{ route('hospital.product-categories.update', $category->id) }}">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Category Code (Read-only)
            </label>
            <input type="text" class="form-control" value="{{ $category->code }}" readonly style="background: #f8fafc; color: #64748b; font-weight: 600;">
            <span style="font-size: 0.75rem; color: #64748b;">Generated category codes cannot be modified.</span>
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="name" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Category Name <span style="color: #dc2626;">*</span>
            </label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
            @error('name')
                <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Description
            </label>
            <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
            @error('description')
                <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.875rem;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                <span>Active (Available for product assignments)</span>
            </label>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Update Category</button>
            <a href="{{ route('hospital.product-categories.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection

