@extends('core::layouts.app')

@section('title', 'Service Details — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Service: {{ $service->name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Service #{{ $service->code }} details and pricing configuration</p>
    </div>
    <div style="display: flex; gap: 8px;">
        @can('hospital:services:edit')
            <a href="{{ route('hospital.services.edit', $service->id) }}" class="btn btn-primary">Edit Service</a>
        @endcan
        <a href="{{ route('hospital.services.index') }}" class="btn btn-outline">← Back to Services</a>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">Service Information</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Service Code</div>
            <div style="font-size: 1.25rem; font-weight: 700; color: #0369a1;">{{ $service->code }}</div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Service Category</div>
            <div style="font-size: 1rem; font-weight: 600;">
                @if($service->category)
                    <a href="{{ route('hospital.service-categories.show', $service->category->id) }}" style="color: #0284c7; text-decoration: none;">
                        {{ $service->category->name }}
                    </a>
                @else
                    <span style="color: #94a3b8;">None</span>
                @endif
            </div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Type</div>
            <div style="font-size: 0.875rem; font-weight: 600; text-transform: capitalize;">{{ $service->type }}</div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Status</div>
            <div>
                @if($service->status === 'active')
                    <span class="badge" style="background: #dcfce7; color: #166534;">Active</span>
                @else
                    <span class="badge" style="background: #f1f5f9; color: #64748b;">Inactive</span>
                @endif
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
        <h4 style="font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 0.75rem;">Financial &amp; Pricing</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Price</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #0f172a;">${{ number_format($service->price, 2) }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Cost</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #64748b;">${{ number_format($service->cost, 2) }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Discount</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #64748b;">${{ number_format($service->discount, 2) }}</span>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748b; display: block;">Commission</span>
                <span style="font-size: 1.125rem; font-weight: 600; color: #0f172a;">${{ number_format($service->commission, 2) }} ({{ ucfirst($service->commission_type) }})</span>
            </div>
        </div>
    </div>

    @if($service->description)
        <div style="margin-top: 1.5rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Description / Instructions</div>
            <p style="font-size: 0.875rem; color: #334155; margin: 0; white-space: pre-line;">{{ $service->description }}</p>
        </div>
    @endif
</div>
@endsection

