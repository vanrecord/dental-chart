@extends('core::layouts.app')

@section('title', 'Invoices & Billing Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Invoices</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage hospital patient invoices, track payments, and review outstanding balances</p>
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
    <!-- Search & Filters -->
    <form method="GET" action="{{ route('invoices.index') }}" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.25rem; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice #, patient ID, name..." class="form-control" style="max-width: 300px;">
        
        <select name="status" class="form-control" style="max-width: 160px;">
            <option value="">All Statuses</option>
            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <div style="display: flex; align-items: center; gap: 6px;">
            <label style="font-size: 0.75rem; color: #64748b; margin: 0;">From:</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" style="max-width: 150px;">
        </div>

        <div style="display: flex; align-items: center; gap: 6px;">
            <label style="font-size: 0.75rem; color: #64748b; margin: 0;">To:</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" style="max-width: 150px;">
        </div>

        <button type="submit" class="btn btn-outline">Filter</button>
        @if(request()->hasAny(['search', 'status', 'from_date', 'to_date']))
            <a href="{{ route('invoices.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Invoice Date</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Balance</th>
                    <th>Status</th>
                    <th>Visits</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td>
                            <a href="{{ route('invoices.show', $inv) }}" style="font-weight: 700; color: var(--primary); text-decoration: none;">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #334155;">
                                {{ $inv->patient->patient_number ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if($inv->patient)
                                <a href="{{ route('patients.show', $inv->patient) }}" style="color: #0f172a; font-weight: 600; text-decoration: none;">
                                    {{ $inv->patient->full_name }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                        <td><strong>${{ number_format($inv->total_amount, 2) }}</strong></td>
                        <td>${{ number_format($inv->paid_amount, 2) }}</td>
                        <td style="font-weight: 700; color: {{ $inv->balance_amount > 0 ? '#b91c1c' : '#15803d' }};">
                            ${{ number_format($inv->balance_amount, 2) }}
                        </td>
                        <td>
                            @if($inv->status === 'paid')
                                <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">Paid</span>
                            @elseif($inv->status === 'partial')
                                <span class="badge" style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a;">Partial</span>
                            @elseif($inv->status === 'cancelled')
                                <span class="badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">Cancelled</span>
                            @else
                                <span class="badge" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1;">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size: 0.8125rem; color: #64748b;">{{ $inv->receptions_count }} visits</span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 4px;">
                                @can('invoice:invoices:view')
                                    <a href="{{ route('invoices.show', $inv) }}" class="btn btn-outline btn-sm">View</a>
                                    <a href="{{ route('invoices.print', $inv) }}" target="_blank" class="btn btn-outline btn-sm" style="color: #0369a1; border-color: #7dd3fc;" title="Print Invoice">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 2px;">
                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                            <rect x="6" y="14" width="12" height="8"></rect>
                                        </svg>
                                        Print
                                    </a>
                                @endcan
                                @if($inv->balance_amount > 0)
                                    @can('payment:payments:create')
                                        <a href="{{ route('payments.create', ['invoice_id' => $inv->id]) }}" class="btn btn-outline btn-sm" style="color: #15803d; border-color: #86efac;">Pay</a>
                                    @endcan
                                @endif
                                @can('invoice:invoices:edit')
                                    <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-outline btn-sm">Edit</a>
                                @endcan
                                @can('invoice:invoices:delete')
                                    @if($inv->paid_amount <= 0)
                                        <form method="POST" action="{{ route('invoices.destroy', $inv) }}" style="display: inline;" class="delete-confirm-form" data-type="invoice" data-name="{{ $inv->invoice_number }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline btn-sm btn-delete-swal" style="color: #dc2626; border-color: #fca5a5;">Delete</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No invoices found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
        <div style="margin-top: 1rem;">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
