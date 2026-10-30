@extends('core::layouts.app')

@section('title', 'Product Categories — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Product Categories</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage hospital dental supplies, medicine, and equipment product categories</p>
    </div>
    <div>
        @can('hospital:product-categories:create')
            <a href="{{ route('hospital.product-categories.create') }}" class="btn btn-primary">+ New Product Category</a>
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
    <form method="GET" action="{{ route('hospital.product-categories.index') }}" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.25rem; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, description..." class="form-control" style="max-width: 320px;">

        <select name="is_active" class="form-control" style="max-width: 160px;">
            <option value="">All Statuses</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-outline">Filter</button>
        @if(request()->hasAny(['search', 'is_active']))
            <a href="{{ route('hospital.product-categories.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Code</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th style="width: 120px;">Products</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 160px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <span class="badge" style="background: #fef3c7; color: #92400e; font-weight: 600;">{{ $category->code }}</span>
                        </td>
                        <td>
                            <a href="{{ route('hospital.product-categories.show', $category->id) }}" style="font-weight: 600; color: #0f172a; text-decoration: none;">
                                {{ $category->name }}
                            </a>
                        </td>
                        <td style="color: #64748b; font-size: 0.875rem;">
                            {{ Str::limit($category->description ?? '—', 50) }}
                        </td>
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #475569;">
                                {{ $category->services_count }} products
                            </span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-success" style="background: #dcfce7; color: #166534;">Active</span>
                            @else
                                <span class="badge badge-secondary" style="background: #f1f5f9; color: #64748b;">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                @can('hospital:product-categories:view')
                                    <a href="{{ route('hospital.product-categories.show', $category->id) }}" class="btn btn-outline btn-sm" title="View">View</a>
                                @endcan
                                @can('hospital:product-categories:edit')
                                    <a href="{{ route('hospital.product-categories.edit', $category->id) }}" class="btn btn-outline btn-sm" title="Edit">Edit</a>
                                @endcan
                                @can('hospital:product-categories:delete')
                                    <form method="POST" action="{{ route('hospital.product-categories.destroy', $category->id) }}" onsubmit="return confirm('Are you sure you want to delete this category?');" style="display: inline;">
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
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">
                            No product categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div style="margin-top: 1.25rem;">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection

