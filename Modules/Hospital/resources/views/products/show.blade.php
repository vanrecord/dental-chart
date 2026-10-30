@extends('core::layouts.app')

@section('title', 'Product Details — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Product: {{ $product->name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Product #{{ $product->code }} details, stock level, and supplier configuration</p>
    </div>
    <div style="display: flex; gap: 8px;">
        @can('hospital:products:edit')
            <a href="{{ route('hospital.products.edit', $product->id) }}" class="btn btn-primary">Edit Product</a>
        @endcan
        <a href="{{ route('hospital.products.index') }}" class="btn btn-outline">← Back to Products</a>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">Product Information</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Product Code</div>
            <div style="font-size: 1.25rem; font-weight: 700; color: #92400e;">{{ $product->code }}</div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Product Category</div>
            <div style="font-size: 1rem; font-weight: 600;">
                @if($product->category)
                    <a href="{{ route('hospital.product-categories.show', $product->category->id) }}" style="color: #b45309; text-decoration: none;">
                        {{ $product->category->name }}
                    </a>
                @else
                    <span style="color: #94a3b8;">None</span>
                @endif
            </div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Stock Quantity</div>
            <div style="font-size: 1.25rem; font-weight: 700; color: #1e293b;">{{ $product->qty }}</div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Status</div>
            <div>
                @if($product->status === 'active')
                    <span class="badge" style="background: #dcfce7; color: #166534;">Active</span>
                @else
                    <span class="badge" style="background: #f1f5f9; color: #64748b;">Inactive</span>
                @endif
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
        <h4 style="font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 0.75rem;">Inventory &amp; Financials</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Selling Price</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #0f172a;">${{ number_format($product->price, 2) }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Cost</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #64748b;">${{ number_format($product->cost, 2) }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Discount</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #64748b;">${{ number_format($product->discount, 2) }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Supplier</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #475569;">{{ $product->supplier_id ? 'Supplier #'.$product->supplier_id : 'None' }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Commission</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #0f172a;">${{ number_format($product->commission, 2) }} ({{ ucfirst($product->commission_type) }})</span>
            </div>
        </div>
    </div>

    @if($product->description)
        <div style="margin-top: 1.5rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Description / Storage Specs</div>
            <p style="font-size: 0.875rem; color: #334155; margin: 0; white-space: pre-line;">{{ $product->description }}</p>
        </div>
    @endif
</div>
@endsection

