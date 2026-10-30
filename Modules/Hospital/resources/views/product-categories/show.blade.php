@extends('core::layouts.app')

@section('title', 'Product Category Details — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Category: {{ $category->name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Product Category Details &amp; Associated Products</p>
    </div>
    <div style="display: flex; gap: 8px;">
        @can('hospital:product-categories:edit')
            <a href="{{ route('hospital.product-categories.edit', $category->id) }}" class="btn btn-primary">Edit Category</a>
        @endcan
        <a href="{{ route('hospital.product-categories.index') }}" class="btn btn-outline">← Back to List</a>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">Category Information</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Code</div>
            <div style="font-size: 1.125rem; font-weight: 600; color: #92400e;">{{ $category->code }}</div>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Type</div>
            <div style="font-size: 0.875rem; font-weight: 600; text-transform: capitalize;">{{ $category->type }}</div>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Status</div>
            <div>
                @if($category->is_active)
                    <span class="badge" style="background: #dcfce7; color: #166534;">Active</span>
                @else
                    <span class="badge" style="background: #f1f5f9; color: #64748b;">Inactive</span>
                @endif
            </div>
        </div>
        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Created At</div>
            <div style="font-size: 0.875rem; color: #334155;">{{ $category->created_at?->format('M d, Y H:i') ?? '—' }}</div>
        </div>
    </div>
    @if($category->description)
        <div style="margin-top: 1rem; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Description</div>
            <p style="font-size: 0.875rem; color: #334155; margin: 0;">{{ $category->description }}</p>
        </div>
    @endif
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0;">Associated Products</h3>
        @can('hospital:products:create')
            <a href="{{ route('hospital.products.create') }}" class="btn btn-outline btn-sm">+ Add Product</a>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Code</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Cost</th>
                    <th>Stock Qty</th>
                    <th>Status</th>
                    <th style="width: 120px; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($category->services as $product)
                    <tr>
                        <td><span class="badge" style="background: #fef3c7; color: #92400e;">{{ $product->code }}</span></td>
                        <td style="font-weight: 600;">{{ $product->name }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>${{ number_format($product->cost, 2) }}</td>
                        <td>{{ $product->qty }}</td>
                        <td>
                            @if($product->status === 'active')
                                <span class="badge" style="background: #dcfce7; color: #166534;">Active</span>
                            @else
                                <span class="badge" style="background: #f1f5f9; color: #64748b;">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('hospital.products.show', $product->id) }}" class="btn btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 1.5rem; color: #94a3b8;">
                            No products currently assigned to this category.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

