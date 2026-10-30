@extends('core::layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Record Payment</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Collect and record a patient payment against an open billing invoice</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('payments.index') }}" class="btn btn-outline">Back to Payments</a>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline">Invoices</a>
    </div>
</div>

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        {{ session('error') }}
    </div>
@endif

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
    <form method="POST" action="{{ route('payments.store') }}">
        @csrf

        <div class="form-group">
            <label for="invoice_id">Select Open Invoice *</label>
            <select name="invoice_id" id="invoice_id" class="form-control" required onchange="updateInvoiceDetails(this)">
                <option value="">Select an invoice with outstanding balance...</option>
                @foreach($eligibleInvoices as $inv)
                    <option value="{{ $inv->id }}" 
                            data-balance="{{ $inv->balance_amount }}" 
                            data-total="{{ $inv->total_amount }}" 
                            data-paid="{{ $inv->paid_amount }}" 
                            data-patient="{{ $inv->patient->full_name ?? '—' }}" 
                            {{ (old('invoice_id', $selectedInvoice?->id) == $inv->id) ? 'selected' : '' }}>
                        {{ $inv->invoice_number }} — {{ $inv->patient->full_name ?? '—' }} (Balance: ${{ number_format($inv->balance_amount, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div id="invoiceBalanceNotice" style="display: {{ $selectedInvoice ? 'block' : 'none' }}; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 1.25rem; font-size: 0.875rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #166534;">Outstanding Invoice Balance:</span>
                <strong id="dispBalance" style="color: #15803d; font-size: 1.1rem;">
                    ${{ number_format($selectedInvoice?->balance_amount ?? 0, 2) }}
                </strong>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="payment_date">Payment Date *</label>
                <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="amount">Payment Amount ($) *</label>
                <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount', $selectedInvoice?->balance_amount) }}" required placeholder="0.00">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="payment_method">Payment Method *</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>Credit / Debit Card</option>
                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer / Wire</option>
                    <option value="insurance" {{ old('payment_method') === 'insurance' ? 'selected' : '' }}>Insurance Coverage</option>
                    <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>Bank Check</option>
                </select>
            </div>

            <div class="form-group">
                <label for="transaction_reference">Transaction Reference / Receipt #</label>
                <input type="text" name="transaction_reference" id="transaction_reference" class="form-control" value="{{ old('transaction_reference') }}" placeholder="e.g. TXN-12345, Card auth...">
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Payment Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Optional payment remarks...">{{ old('notes') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 1.5rem;">
            <a href="{{ route('payments.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary" style="background: #15803d; border-color: #15803d;">Record Payment</button>
        </div>
    </form>
</div>

<script>
function updateInvoiceDetails(select) {
    const option = select.options[select.selectedIndex];
    const notice = document.getElementById('invoiceBalanceNotice');
    const dispBalance = document.getElementById('dispBalance');
    const amountInput = document.getElementById('amount');

    if (option && option.dataset.balance) {
        const bal = parseFloat(option.dataset.balance);
        dispBalance.textContent = '$' + bal.toFixed(2);
        amountInput.value = bal.toFixed(2);
        notice.style.display = 'block';
    } else {
        notice.style.display = 'none';
    }
}
</script>
@endsection

