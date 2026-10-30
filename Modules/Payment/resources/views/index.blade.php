@extends('core::layouts.app')

@section('title', 'Payments & Transactions Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Payments</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage hospital payment records, settlement methods, and transaction receipts</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('invoices.index') }}" class="btn btn-outline">Invoices</a>
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
    <!-- Search & Filter Form -->
    <form method="GET" action="{{ route('payments.index') }}" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.25rem; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by payment #, reference, invoice #, patient..." class="form-control" style="max-width: 320px;">
        
        <select name="payment_method" class="form-control" style="max-width: 160px;">
            <option value="">All Methods</option>
            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="credit_card" {{ request('payment_method') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
            <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="insurance" {{ request('payment_method') === 'insurance' ? 'selected' : '' }}>Insurance</option>
            <option value="check" {{ request('payment_method') === 'check' ? 'selected' : '' }}>Check</option>
        </select>

        <select name="status" class="form-control" style="max-width: 150px;">
            <option value="">All Statuses</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
        </select>

        <div style="display: flex; align-items: center; gap: 6px;">
            <label style="font-size: 0.75rem; color: #64748b; margin: 0;">From:</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" style="max-width: 145px;">
        </div>

        <div style="display: flex; align-items: center; gap: 6px;">
            <label style="font-size: 0.75rem; color: #64748b; margin: 0;">To:</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" style="max-width: 145px;">
        </div>

        <button type="submit" class="btn btn-outline">Filter</button>
        @if(request()->hasAny(['search', 'payment_method', 'status', 'from_date', 'to_date']))
            <a href="{{ route('payments.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Payment ID / Txn #</th>
                    <th>Payment Date</th>
                    <th>Invoice #</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pmt)
                    <tr>
                        <td>
                            <a href="{{ route('payments.show', $pmt) }}" style="font-weight: 700; color: var(--primary); text-decoration: none;">
                                {{ $pmt->payment_number }}
                            </a>
                            @if($pmt->transaction_reference)
                                <span style="display: block; font-size: 0.75rem; color: #64748b;">Ref: {{ $pmt->transaction_reference }}</span>
                            @endif
                        </td>
                        <td>{{ $pmt->payment_date->format('Y-m-d') }}</td>
                        <td>
                            @if($pmt->invoice)
                                <a href="{{ route('invoices.show', $pmt->invoice) }}" style="font-weight: 600; text-decoration: none;">
                                    {{ $pmt->invoice->invoice_number }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #334155;">
                                {{ $pmt->patient->patient_number ?? ($pmt->invoice->patient->patient_number ?? '—') }}
                            </span>
                        </td>
                        <td>
                            @php $p = $pmt->patient ?? $pmt->invoice?->patient; @endphp
                            @if($p)
                                <a href="{{ route('patients.show', $p) }}" style="color: #0f172a; font-weight: 600; text-decoration: none;">
                                    {{ $p->full_name }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="text-transform: uppercase;">
                                {{ str_replace('_', ' ', $pmt->payment_method) }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: #15803d; font-size: 0.9375rem;">
                                ${{ number_format($pmt->amount, 2) }}
                            </strong>
                        </td>
                        <td>
                            @if($pmt->status === 'completed')
                                <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">Completed</span>
                            @elseif($pmt->status === 'pending')
                                <span class="badge" style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a;">Pending</span>
                            @elseif($pmt->status === 'refunded')
                                <span class="badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">Refunded</span>
                            @else
                                <span class="badge">{{ ucfirst($pmt->status) }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 4px;">
                                @can('payment:payments:view')
                                    <a href="{{ route('payments.show', $pmt) }}" class="btn btn-outline btn-sm">View</a>
                                    <a href="{{ route('payments.print', $pmt) }}" target="_blank" class="btn btn-outline btn-sm" style="color: #0369a1; border-color: #7dd3fc;" title="Print Receipt">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 2px;">
                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                            <rect x="6" y="14" width="12" height="8"></rect>
                                        </svg>
                                        Print
                                    </a>
                                @endcan
                                @can('payment:payments:edit')
                                    <a href="{{ route('payments.edit', $pmt) }}" class="btn btn-outline btn-sm">Edit</a>
                                @endcan
                                @can('payment:payments:delete')
                                    <form method="POST" action="{{ route('payments.destroy', $pmt) }}" style="display: inline;" class="delete-confirm-form" data-type="payment" data-name="{{ $pmt->payment_number }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-outline btn-sm btn-delete-swal" style="color: #dc2626; border-color: #fca5a5;">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No payment records found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
        <div style="margin-top: 1rem;">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
