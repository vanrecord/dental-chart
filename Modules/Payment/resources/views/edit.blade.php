@extends('core::layouts.app')

@section('title', 'Edit Payment - ' . $payment->payment_number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Payment: {{ $payment->payment_number }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Modify transaction details, method, reference, or amount</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline">View Details</a>
        <a href="{{ route('payments.index') }}" class="btn btn-outline">Back to Payments</a>
    </div>
</div>

@if ($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        <strong>Please correct the following errors:</strong>
        <ul style="margin: 4px 0 0; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="max-width: 640px; margin: 0 auto;">
    <form method="POST" action="{{ route('payments.update', $payment) }}">
        @csrf
        @method('PUT')

        <div style="background: #f8fafc; padding: 12px 14px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 1.25rem; font-size: 0.875rem;">
            <div><strong>Linked Invoice:</strong> {{ $payment->invoice->invoice_number ?? '—' }}</div>
            <div style="color: #64748b; font-size: 0.8125rem; margin-top: 2px;">
                Patient: {{ $payment->patient->full_name ?? ($payment->invoice->patient->full_name ?? '—') }}
            </div>
        </div>

        <input type="hidden" name="invoice_id" value="{{ $payment->invoice_id }}">

        <div class="form-row">
            <div class="form-group">
                <label for="payment_date">Payment Date *</label>
                <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="amount">Payment Amount ($) *</label>
                <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount', $payment->amount) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="payment_method">Payment Method *</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="cash" {{ old('payment_method', $payment->payment_method) === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="credit_card" {{ old('payment_method', $payment->payment_method) === 'credit_card' ? 'selected' : '' }}>Credit / Debit Card</option>
                    <option value="bank_transfer" {{ old('payment_method', $payment->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer / Wire</option>
                    <option value="insurance" {{ old('payment_method', $payment->payment_method) === 'insurance' ? 'selected' : '' }}>Insurance Coverage</option>
                    <option value="check" {{ old('payment_method', $payment->payment_method) === 'check' ? 'selected' : '' }}>Bank Check</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Payment Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="completed" {{ old('status', $payment->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ old('status', $payment->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="refunded" {{ old('status', $payment->status) === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    <option value="failed" {{ old('status', $payment->status) === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="transaction_reference">Transaction Reference / Receipt #</label>
            <input type="text" name="transaction_reference" id="transaction_reference" class="form-control" value="{{ old('transaction_reference', $payment->transaction_reference) }}">
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes', $payment->notes) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 1.5rem;">
            <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Payment</button>
        </div>
    </form>
</div>
@endsection

