@extends('core::layouts.app')

@section('title', 'Products — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Products</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage hospital medical inventory, dental consumables, and equipment</p>
    </div>
    <div>
        @can('hospital:products:create')
            <a href="{{ route('hospital.products.create') }}" class="btn btn-primary">+ New Product</a>
        @endcan
    </div>
</div>

@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <form method="GET" action="{{ route('hospital.products.index') }}" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.25rem; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, description..." class="form-control" style="max-width: 280px;">

        <select name="category_id" class="form-control" style="max-width: 220px;">
            <option value="">All Product Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="status" class="form-control" style="max-width: 150px;">
            <option value="">All Statuses</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-outline">Filter</button>
        @if(request()->hasAny(['search', 'category_id', 'status']))
            <a href="{{ route('hospital.products.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Code</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Cost</th>
                    <th>Discount</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Commission</th>
                    <th>Commission Type</th>
                    <th style="width: 90px;">Status</th>
                    <th style="width: 160px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <span class="badge" style="background: #fef3c7; color: #92400e; font-weight: 600;">{{ $product->code }}</span>
                        </td>
                        <td>
                            <a href="{{ route('hospital.products.show', $product->id) }}" style="font-weight: 600; color: #0f172a; text-decoration: none;">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>
                            @if($product->category)
                                <a href="{{ route('hospital.product-categories.show', $product->category->id) }}" style="color: #b45309; text-decoration: none; font-size: 0.875rem;">
                                    {{ $product->category->name }}
                                </a>
                            @else
                                <span style="color: #94a3b8;">—</span>
                            @endif
                        </td>
                        <td style="color: #64748b; font-size: 0.875rem;">
                            {{ Str::limit($product->description ?? '—', 40) }}
                        </td>
                        <td style="font-weight: 600;">${{ number_format($product->price, 2) }}</td>
                        <td style="color: #64748b;">${{ number_format($product->cost, 2) }}</td>
                        <td style="color: #64748b;">${{ number_format($product->discount, 2) }}</td>
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #334155; font-weight: 600;">
                                {{ $product->qty }}
                            </span>
                        </td>
                        <td style="font-size: 0.875rem; color: #64748b;">
                            {{ $product->supplier_id ? 'Supplier #'.$product->supplier_id : '—' }}
                        </td>
                        <td>${{ number_format($product->commission, 2) }}</td>
                        <td style="text-transform: capitalize; font-size: 0.875rem; color: #475569;">{{ $product->commission_type }}</td>
                        <td>
                            @if($product->status === 'active')
                                <span class="badge" style="background: #dcfce7; color: #166534;">Active</span>
                            @else
                                <span class="badge" style="background: #f1f5f9; color: #64748b;">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                @can('hospital:products:view')
                                    <a href="{{ route('hospital.products.show', $product->id) }}" class="btn btn-outline btn-sm" title="View">View</a>
                                @endcan
                                @can('hospital:products:edit')
                                    <a href="{{ route('hospital.products.edit', $product->id) }}" class="btn btn-outline btn-sm" title="Edit">Edit</a>
                                @endcan
                                @can('hospital:products:delete')
                                    <form method="POST" action="{{ route('hospital.products.destroy', $product->id) }}" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #fca5a5;" title="Delete">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" style="text-align: center; padding: 2rem; color: #94a3b8;">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div style="margin-top: 1.25rem;">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

