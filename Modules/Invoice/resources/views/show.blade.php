@extends('core::layouts.app')

@section('title', 'Invoice Details - ' . $invoice->invoice_number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Invoice: {{ $invoice->invoice_number }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">
            Invoice Date: <strong>{{ $invoice->invoice_date->format('M d, Y') }}</strong> · 
            Status: <strong>{{ ucfirst($invoice->status) }}</strong>
        </p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px; color: #0369a1; border-color: #7dd3fc;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print Invoice
        </a>
        @can('invoice:invoices:edit')
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline">Edit Invoice</a>
        @endcan
        <a href="{{ route('invoices.index') }}" class="btn btn-outline">Back to Invoices</a>
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

<div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">
    <!-- Left Column: Patient & Financial Balances -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <!-- Financial Card -->
        <div class="card" style="border-top: 4px solid var(--primary);">
            <h2 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: #0f172a;">Invoice Balance</h2>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @if($invoice->subtotal > 0 || $invoice->discount > 0 || $invoice->tax > 0)
                    <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.8125rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="color: #64748b;">Subtotal:</span>
                            <strong>${{ number_format($invoice->subtotal, 2) }}</strong>
                        </div>
                        @if($invoice->discount > 0)
                            <div style="display: flex; justify-content: space-between; color: #b91c1c; margin-bottom: 4px;">
                                <span>Discount:</span>
                                <strong>-${{ number_format($invoice->discount, 2) }}</strong>
                            </div>
                        @endif
                        @if($invoice->tax > 0)
                            <div style="display: flex; justify-content: space-between; color: #475569;">
                                <span>Tax:</span>
                                <strong>+${{ number_format($invoice->tax, 2) }}</strong>
                            </div>
                        @endif
                    </div>
                @endif

                <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.75rem; color: #64748b; display: block;">Total Billed Amount</span>
                    <strong style="font-size: 1.25rem; color: #0f172a;">${{ number_format($invoice->total_amount, 2) }}</strong>
                </div>

                <div style="background: #f0fdf4; padding: 12px; border-radius: 8px; border: 1px solid #bbf7d0;">
                    <span style="font-size: 0.75rem; color: #166534; display: block;">Amount Paid</span>
                    <strong style="font-size: 1.25rem; color: #15803d;">${{ number_format($invoice->paid_amount, 2) }}</strong>
                </div>

                <div style="background: {{ $invoice->balance_amount > 0 ? '#fef2f2' : '#f8fafc' }}; padding: 12px; border-radius: 8px; border: 1px solid {{ $invoice->balance_amount > 0 ? '#fecaca' : '#e2e8f0' }};">
                    <span style="font-size: 0.75rem; color: {{ $invoice->balance_amount > 0 ? '#991b1b' : '#64748b' }}; display: block;">Outstanding Balance</span>
                    <strong style="font-size: 1.25rem; color: {{ $invoice->balance_amount > 0 ? '#b91c1c' : '#0f172a' }};">
                        ${{ number_format($invoice->balance_amount, 2) }}
                    </strong>
                </div>

                <div style="border-top: 1px solid #f1f5f9; padding-top: 8px; font-size: 0.8125rem; color: #64748b;">
                    <div><strong>Status:</strong> <span class="badge">{{ ucfirst($invoice->status) }}</span></div>
                    @if($invoice->creator)
                        <div style="margin-top: 4px;"><strong>Created By:</strong> {{ $invoice->creator->name }}</div>
                    @endif
                </div>
            </div>
        </div>

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

        <!-- Patient Summary Card -->
        <div class="card">
            <h2 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.75rem; color: #0f172a;">Patient Information</h2>
            @if($invoice->patient)
                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.8125rem;">
                    <div>
                        <span style="color: var(--text-muted); display: block;">Full Name</span>
                        <a href="{{ route('patients.show', $invoice->patient) }}" style="font-weight: 700; font-size: 0.9375rem; color: var(--primary); text-decoration: none;">
                            {{ $invoice->patient->full_name }}
                        </a>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block;">Patient ID</span>
                        <strong>{{ $invoice->patient->patient_number }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block;">Phone</span>
                        <span>{{ $invoice->patient->phone ?? '—' }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block;">Sex / DOB</span>
                        <span>{{ ucfirst($invoice->patient->sex ?? '—') }} · {{ optional($invoice->patient->date_of_birth)->format('Y-m-d') ?? '—' }}</span>
                    </div>
                </div>
            @else
                <span style="color: var(--text-muted);">No patient linked</span>
            @endif
        </div>
    </div>

    <!-- Right Column: Receptions History and Payments History -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Invoice Items: Services & Products -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">Invoice Items ({{ $invoice->items->count() }})</h2>
                @can('invoice:invoices:edit')
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline btn-sm">Edit Items</a>
                @endcan
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 80px;">Type</th>
                            <th>Service / Product</th>
                            <th style="width: 120px;">Teeth</th>
                            <th style="width: 60px; text-align: center;">Qty</th>
                            <th style="width: 90px; text-align: right;">Unit Price</th>
                            <th style="width: 100px; text-align: right;">Amount</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->items as $item)
                            <tr>
                                <td>{{ $item->operate_date?->format('Y-m-d') }}</td>
                                <td>
                                    @if($item->isService())
                                        <span class="badge" style="background: #dcfce7; color: #166534;">Service</span>
                                    @else
                                        <span class="badge" style="background: #e0f2fe; color: #0369a1;">Product</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $item->item_name }}</strong>
                                </td>
                                <td>
                                    {{ $item->tooth_numbers ?: '—' }}
                                </td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td style="text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                                <td style="text-align: right; font-weight: 600;">${{ number_format($item->amount, 2) }}</td>
                                <td style="color: #64748b; font-size: 0.8125rem;">{{ $item->note ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 1.25rem;">
                                    No itemized services or products recorded for this invoice.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Associated Receptions / Visits -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">Associated Receptions &amp; Visits ({{ $invoice->receptions->count() }})</h2>
                @can('register:register:create')
                    @if($invoice->isEligibleForReuse())
                        <a href="{{ route('register.index', ['patient_id' => $invoice->patient_id, 'invoice_id' => $invoice->id, 'workflow' => 'follow_up']) }}" class="btn btn-outline btn-sm">+ Follow-up Visit</a>
                    @endif
                @endcan
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Reception #</th>
                            <th>Visit Date</th>
                            <th>Visit Type</th>
                            <th>Chief Complaint</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->receptions as $rec)
                            <tr>
                                <td><strong>{{ $rec->reception_number }}</strong></td>
                                <td>{{ $rec->reception_date->format('Y-m-d H:i') }}</td>
                                <td><span class="badge" style="text-transform: capitalize;">{{ str_replace('_', ' ', $rec->visit_type) }}</span></td>
                                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $rec->chief_complaint ?: '—' }}
                                </td>
                                <td>
                                    <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                                        {{ ucfirst(str_replace('_', ' ', $rec->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1.25rem;">
                                    No receptions associated with this invoice.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Associated Payments -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">Payment Records ({{ $invoice->payments->count() }})</h2>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Payment #</th>
                            <th>Payment Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('payments.show', $payment) }}" style="font-weight: 600; color: var(--primary); text-decoration: none;">
                                        {{ $payment->payment_number }}
                                    </a>
                                </td>
                                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                <td><span class="badge" style="text-transform: uppercase;">{{ str_replace('_', ' ', $payment->payment_method) }}</span></td>
                                <td>{{ $payment->transaction_reference ?: '—' }}</td>
                                <td><strong style="color: #15803d;">${{ number_format($payment->amount, 2) }}</strong></td>
                                <td>
                                    <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline btn-sm">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 1.25rem;">
                                    No payments recorded yet. Current outstanding balance: <strong>${{ number_format($invoice->balance_amount, 2) }}</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

