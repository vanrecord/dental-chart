@extends('core::layouts.app')

@section('title', 'Payment Details - ' . $payment->payment_number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Payment: {{ $payment->payment_number }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">
            Recorded on <strong>{{ $payment->payment_date->format('M d, Y') }}</strong> · 
            Status: <strong>{{ ucfirst($payment->status) }}</strong>
        </p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('payments.print', $payment) }}" target="_blank" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px; color: #16a34a; border-color: #86efac;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print Receipt
        </a>
        @if($payment->invoice)
            <a href="{{ route('invoices.show', $payment->invoice) }}" class="btn btn-primary">View Linked Invoice</a>
        @endif
        @can('payment:payments:edit')
            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline">Edit</a>
        @endcan
        <a href="{{ route('payments.index') }}" class="btn btn-outline">Back to Payments</a>
    </div>
</div>

@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        {{ session('success') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
    <!-- Left Column: Payment Details Card -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <div class="card" style="border-top: 4px solid #15803d;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
                <div>
                    <span style="font-size: 0.75rem; color: #166534; font-weight: 700; text-transform: uppercase;">Payment Receipt</span>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #15803d; margin: 4px 0 0;">
                        ${{ number_format($payment->amount, 2) }} <span style="font-size: 0.875rem; color: #64748b;">{{ $payment->currency }}</span>
                    </h2>
                </div>
                <div>
                    <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.8125rem; padding: 4px 10px;">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 0.875rem; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Transaction / Payment ID</span>
                    <strong>{{ $payment->payment_number }}</strong>
                </div>

                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Payment Date</span>
                    <strong>{{ $payment->payment_date->format('F d, Y') }}</strong>
                </div>

                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Payment Method</span>
                    <strong style="text-transform: uppercase;">{{ str_replace('_', ' ', $payment->payment_method) }}</strong>
                </div>

                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Transaction Reference</span>
                    <span>{{ $payment->transaction_reference ?: '—' }}</span>
                </div>

                <div style="grid-column: 1 / -1;">
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Notes / Remarks</span>
                    <div style="background: #f8fafc; padding: 8px 12px; border-radius: 6px; font-size: 0.8125rem; color: #334155; margin-top: 4px;">
                        {{ $payment->notes ?: 'No additional notes recorded for this transaction.' }}
                    </div>
                </div>

                @if($payment->creator)
                    <div style="grid-column: 1 / -1; font-size: 0.75rem; color: #64748b;">
                        Recorded by: <strong>{{ $payment->creator->name }}</strong> on {{ $payment->created_at->format('Y-m-d H:i') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Related Invoice & Patient Summaries -->
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        <!-- Clinic Provider Card -->
        @if($clinicSetting = clinic_settings())
            <div class="card" style="padding: 12px 14px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($clinicSetting->logo_url)
                        <img src="{{ $clinicSetting->logo_url }}" alt="Logo" style="width: 36px; height: 36px; object-fit: contain; border-radius: 6px;">
                    @endif
                    <div>
                        <strong style="color: {{ $clinicSetting->english_title_color ?: '#0f172a' }}; font-size: 0.875rem; display: block;">
                            {{ $clinicSetting->english_title ?: 'OdontoCare Dental Clinic' }}
                        </strong>
                        @if($clinicSetting->khmer_title)
                            <span style="color: {{ $clinicSetting->khmer_title_color ?: '#64748b' }}; font-size: 0.75rem; display: block;">
                                {{ $clinicSetting->khmer_title }}
                            </span>
                        @endif
                    </div>
                </div>
                @if($clinicSetting->phone || $clinicSetting->email)
                    <div style="margin-top: 8px; font-size: 0.75rem; color: #64748b; border-top: 1px solid #f1f5f9; padding-top: 6px;">
                        @if($clinicSetting->phone) <div>Phone: {{ $clinicSetting->phone }}</div> @endif
                        @if($clinicSetting->email) <div>Email: {{ $clinicSetting->email }}</div> @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- Related Invoice Summary -->
        <div class="card">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem; color: #0f172a;">Related Invoice</h3>
            @if($payment->invoice)
                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.8125rem;">
                    <div>
                        <span style="color: var(--text-muted); display: block;">Invoice #</span>
                        <a href="{{ route('invoices.show', $payment->invoice) }}" style="font-weight: 700; color: var(--primary); text-decoration: none; font-size: 0.9375rem;">
                            {{ $payment->invoice->invoice_number }}
                        </a>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block;">Invoice Total</span>
                        <strong>${{ number_format($payment->invoice->total_amount, 2) }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block;">Remaining Balance</span>
                        <strong style="color: {{ $payment->invoice->balance_amount > 0 ? '#b91c1c' : '#15803d' }};">
                            ${{ number_format($payment->invoice->balance_amount, 2) }}
                        </strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block;">Current Invoice Status</span>
                        <span class="badge">{{ ucfirst($payment->invoice->status) }}</span>
                    </div>
                </div>
            @else
                <span style="color: var(--text-muted);">Invoice not found</span>
            @endif
        </div>

        <!-- Related Patient Summary -->
        <div class="card">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem; color: #0f172a;">Patient Information</h3>
            @php $patient = $payment->patient ?? $payment->invoice?->patient; @endphp
            @if($patient)
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 0.8125rem;">
                    <div>
                        <span style="color: var(--text-muted); display: block;">Full Name</span>
                        <a href="{{ route('patients.show', $patient) }}" style="font-weight: 700; color: #0f172a; text-decoration: none;">
                            {{ $patient->full_name }}
                        </a>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block;">Patient ID</span>
                        <strong>{{ $patient->patient_number }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block;">Phone</span>
                        <span>{{ $patient->phone ?? '—' }}</span>
                    </div>
                </div>
            @else
                <span style="color: var(--text-muted);">Patient not found</span>
            @endif
        </div>
    </div>
</div>
@endsection

