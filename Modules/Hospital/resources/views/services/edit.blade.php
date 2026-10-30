@extends('core::layouts.app')

@section('title', 'Edit Service — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Service</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Modify service #{{ $service->code }} — {{ $service->name }}</p>
    </div>
    <div>
        <a href="{{ route('hospital.services.index') }}" class="btn btn-outline">← Back to Services</a>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="{{ route('hospital.services.update', $service->id) }}">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Service Code (Read-only)
                </label>
                <input type="text" class="form-control" value="{{ $service->code }}" readonly style="background: #f8fafc; color: #64748b; font-weight: 600;">
                <span style="font-size: 0.75rem; color: #64748b;">Code cannot be changed</span>
            </div>

            <div>
                <label for="name" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Service Name <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name) }}" required>
                @error('name')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="category_id" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Service Category <span style="color: #dc2626;">*</span>
            </label>
            <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                <option value="">-- Select Service Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} (Code: {{ $cat->code }})
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Description
            </label>
            <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $service->description) }}</textarea>
            @error('description')
                <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label for="price" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Price ($)
                </label>
                <input type="number" step="0.01" min="0" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $service->price) }}">
                @error('price')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="cost" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Cost ($)
                </label>
                <input type="number" step="0.01" min="0" id="cost" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost', $service->cost) }}">
                @error('cost')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="discount" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Discount ($)
                </label>
                <input type="number" step="0.01" min="0" id="discount" name="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', $service->discount) }}">
                @error('discount')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <label for="commission" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Commission
                </label>
                <input type="number" step="0.01" min="0" id="commission" name="commission" class="form-control @error('commission') is-invalid @enderror" value="{{ old('commission', $service->commission) }}">
                @error('commission')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="commission_type" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Commission Type
                </label>
                <select id="commission_type" name="commission_type" class="form-control">
                    <option value="fixed" {{ old('commission_type', $service->commission_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    <option value="percentage" {{ old('commission_type', $service->commission_type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                </select>
            </div>

            <div>
                <label for="status" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Status
                </label>
                <select id="status" name="status" class="form-control">
                    <option value="active" {{ old('status', $service->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $service->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Update Service</button>
            <a href="{{ route('hospital.services.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection

