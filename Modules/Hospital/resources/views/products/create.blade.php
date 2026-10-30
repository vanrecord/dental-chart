@extends('core::layouts.app')

@section('title', 'New Product — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">New Product</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Create a hospital consumable, medical item, or dental product</p>
    </div>
    <div>
        <a href="{{ route('hospital.products.index') }}" class="btn btn-outline">← Back to Products</a>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="{{ route('hospital.products.store') }}">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Product Code
                </label>
                <input type="text" class="form-control" value="Auto-generated" disabled style="background: #f8fafc; color: #64748b;">
                <span style="font-size: 0.75rem; color: #64748b;">Assigned sequentially for Products</span>
            </div>

            <div>
                <label for="name" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Product Name <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Dental Implant Screws, Composite Resin, Latex Gloves" required>
                @error('name')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label for="category_id" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                Product Category <span style="color: #dc2626;">*</span>
            </label>
            <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                <option value="">-- Select Active Product Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
            <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Product details, batch specs, or storage notes">{{ old('description') }}</textarea>
            @error('description')
                <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label for="price" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Selling Price ($)
                </label>
                <input type="number" step="0.01" min="0" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', '0.00') }}">
                @error('price')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="cost" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Cost ($)
                </label>
                <input type="number" step="0.01" min="0" id="cost" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost', '0.00') }}">
                @error('cost')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="discount" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Discount ($)
                </label>
                <input type="number" step="0.01" min="0" id="discount" name="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', '0.00') }}">
                @error('discount')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label for="qty" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Stock Quantity
                </label>
                <input type="number" min="0" id="qty" name="qty" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty', '0') }}">
                @error('qty')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="supplier_id" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Supplier ID (Optional)
                </label>
                <input type="number" min="1" id="supplier_id" name="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" value="{{ old('supplier_id') }}" placeholder="e.g., 101">
                @error('supplier_id')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <label for="commission" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Commission
                </label>
                <input type="number" step="0.01" min="0" id="commission" name="commission" class="form-control @error('commission') is-invalid @enderror" value="{{ old('commission', '0.00') }}">
                @error('commission')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="commission_type" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Commission Type
                </label>
                <select id="commission_type" name="commission_type" class="form-control">
                    <option value="fixed" {{ old('commission_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    <option value="percentage" {{ old('commission_type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                </select>
            </div>

            <div>
                <label for="status" style="display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 4px;">
                    Status
                </label>
                <select id="status" name="status" class="form-control">
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Save Product</button>
            <a href="{{ route('hospital.products.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection

