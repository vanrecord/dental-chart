@extends('core::layouts.app')

@section('title', 'Service Category Details — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Category: {{ $category->name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Service Category Details &amp; Associated Services</p>
    </div>
    <div style="display: flex; gap: 8px;">
        @can('hospital:service-categories:edit')
            <a href="{{ route('hospital.service-categories.edit', $category->id) }}" class="btn btn-primary">Edit Category</a>
        @endcan
        <a href="{{ route('hospital.service-categories.index') }}" class="btn btn-outline">← Back to List</a>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">Category Information</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Code</div>
            <div style="font-size: 1.125rem; font-weight: 600; color: #0369a1;">{{ $category->code }}</div>
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
        <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0;">Associated Services</h3>
        @can('hospital:services:create')
            <a href="{{ route('hospital.services.create') }}" class="btn btn-outline btn-sm">+ Add Service</a>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Code</th>
                    <th>Service Name</th>
                    <th>Price</th>
                    <th>Cost</th>
                    <th>Discount</th>
                    <th>Status</th>
                    <th style="width: 120px; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($category->services as $service)
                    <tr>
                        <td><span class="badge" style="background: #e0f2fe; color: #0369a1;">{{ $service->code }}</span></td>
                        <td style="font-weight: 600;">{{ $service->name }}</td>
                        <td>${{ number_format($service->price, 2) }}</td>
                        <td>${{ number_format($service->cost, 2) }}</td>
                        <td>${{ number_format($service->discount, 2) }}</td>
                        <td>
                            @if($service->status === 'active')
                                <span class="badge" style="background: #dcfce7; color: #166534;">Active</span>
                            @else
                                <span class="badge" style="background: #f1f5f9; color: #64748b;">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('hospital.services.show', $service->id) }}" class="btn btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 1.5rem; color: #94a3b8;">
                            No services currently assigned to this category.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

