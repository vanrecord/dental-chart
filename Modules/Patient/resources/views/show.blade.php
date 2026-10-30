@extends('core::layouts.app')

@section('title', 'Patient Details - ' . $patient->full_name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $patient->full_name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Patient ID: <strong>{{ $patient->patient_number }}</strong></p>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        @can('register:register:create')
            <a href="{{ route('register.index', ['patient_id' => $patient->id]) }}" class="btn btn-primary" style="background: #0284c7; border-color: #0284c7;">+ Register Visit</a>
        @endcan
        @can('odontogram:dental_chart:create')
            <a href="{{ route('dental-charts.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline">+ Dental Chart</a>
        @endcan
        @can('patient:patients:edit')
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline">Edit Info</a>
        @endcan
        <a href="{{ route('patients.index') }}" class="btn btn-outline">Back to Patients</a>
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
    <!-- Left Column: Patient Profile & Financial Summary -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <!-- Profile Card -->
        <div class="card">
            <h2 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: #0f172a;">Patient Profile</h2>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Patient ID</span>
                    <strong>{{ $patient->patient_number }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Date of Birth</span>
                    <strong>{{ optional($patient->date_of_birth)->format('M d, Y') ?? '—' }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Gender</span>
                    <strong>{{ ucfirst($patient->sex ?? '—') }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Phone</span>
                    <strong>{{ $patient->phone ?? '—' }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Address</span>
                    <div>{{ $patient->address ?? '—' }}</div>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Medical Notes</span>
                    <div style="background: #f8fafc; padding: 0.5rem; border-radius: 6px; font-size: 0.8125rem; color: #475569;">
                        {{ $patient->medical_notes ?? 'No notes recorded.' }}
                    </div>
                </div>
                <div style="border-top: 1px solid #f1f5f9; padding-top: 0.5rem; font-size: 0.75rem; color: #64748b;">
                    Registered: {{ $patient->created_at ? $patient->created_at->format('M d, Y') : '—' }}
                </div>
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="card" style="border-top: 4px solid #0284c7;">
            <h2 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.75rem; color: #0f172a;">Billing &amp; Payments</h2>
            <div style="display: grid; grid-template-columns: 1fr; gap: 8px;">
                <div style="background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.75rem; color: #64748b; display: block;">Total Invoiced</span>
                    <strong style="font-size: 1.1rem; color: #0f172a;">${{ number_format($patient->total_invoiced, 2) }}</strong>
                </div>
                <div style="background: #f0fdf4; padding: 10px; border-radius: 6px; border: 1px solid #bbf7d0;">
                    <span style="font-size: 0.75rem; color: #166534; display: block;">Total Paid</span>
                    <strong style="font-size: 1.1rem; color: #15803d;">${{ number_format($patient->total_paid, 2) }}</strong>
                </div>
                <div style="background: {{ $patient->outstanding_balance > 0 ? '#fef2f2' : '#f8fafc' }}; padding: 10px; border-radius: 6px; border: 1px solid {{ $patient->outstanding_balance > 0 ? '#fecaca' : '#e2e8f0' }};">
                    <span style="font-size: 0.75rem; color: {{ $patient->outstanding_balance > 0 ? '#991b1b' : '#64748b' }}; display: block;">Outstanding Balance</span>
                    <strong style="font-size: 1.1rem; color: {{ $patient->outstanding_balance > 0 ? '#b91c1c' : '#0f172a' }};">
                        ${{ number_format($patient->outstanding_balance, 2) }}
                    </strong>
                </div>
            </div>

            @can('register:register:create')
                <div style="margin-top: 1rem;">
                    <a href="{{ route('register.index', ['patient_id' => $patient->id]) }}" class="btn btn-outline" style="width: 100%; justify-content: center; font-size: 0.8125rem;">
                        + Register New Visit
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <!-- Right Column: Receptions, Invoices & Dental Charts -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Receptions History -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">Reception &amp; Visit History ({{ $patient->receptions->count() }})</h2>
                @can('register:register:create')
                    <a href="{{ route('register.index', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">+ Register Visit</a>
                @endcan
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Reception #</th>
                            <th>Date &amp; Time</th>
                            <th>Visit Type</th>
                            <th>Linked Invoice</th>
                            <th>Chief Complaint</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->receptions as $rec)
                            <tr>
                                <td><strong>{{ $rec->reception_number }}</strong></td>
                                <td>{{ $rec->reception_date->format('Y-m-d H:i') }}</td>
                                <td><span class="badge" style="text-transform: capitalize;">{{ str_replace('_', ' ', $rec->visit_type) }}</span></td>
                                <td>
                                    @if($rec->invoice)
                                        <a href="{{ route('invoices.show', $rec->invoice) }}" style="font-weight: 600; text-decoration: none;">
                                            {{ $rec->invoice->invoice_number }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 1.25rem;">
                                    No visit receptions recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoices History -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">Billing Invoices ({{ $patient->invoices->count() }})</h2>
                @can('invoice:invoices:create')
                    <a href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline btn-sm">+ Create Invoice</a>
                @endcan
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Visits</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->invoices as $inv)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $inv) }}" style="font-weight: 600; color: var(--primary); text-decoration: none;">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                                <td>${{ number_format($inv->total_amount, 2) }}</td>
                                <td>${{ number_format($inv->paid_amount, 2) }}</td>
                                <td style="font-weight: 700; color: {{ $inv->balance_amount > 0 ? '#b91c1c' : '#15803d' }};">
                                    ${{ number_format($inv->balance_amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge" style="text-transform: capitalize;">{{ $inv->status }}</span>
                                </td>
                                <td>{{ $inv->receptions_count }} visits</td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 4px;">
                                        <a href="{{ route('invoices.show', $inv) }}" class="btn btn-outline btn-sm">View</a>
                                        @if($inv->balance_amount > 0)
                                            <a href="{{ route('payments.create', ['invoice_id' => $inv->id]) }}" class="btn btn-outline btn-sm" style="color: #15803d; border-color: #86efac;">Pay</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 1.25rem;">
                                    No invoices generated for this patient yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dental Charts -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">Dental Charts ({{ $patient->dentalCharts->count() }})</h2>
                @can('odontogram:dental_chart:create')
                    <a href="{{ route('dental-charts.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">+ Create Chart</a>
                @endcan
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Chart Date</th>
                            <th>Dentition</th>
                            <th>Examination Notes</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->dentalCharts as $chart)
                            <tr>
                                <td><strong>{{ $chart->chart_date->format('M d, Y') }}</strong></td>
                                <td><span class="badge">{{ ucfirst($chart->dentition_type) }}</span></td>
                                <td style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $chart->examination_notes ?: '—' }}
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('dental.show', $chart) }}" class="btn btn-primary btn-sm">Interactive Odontogram</a>
                                        <a href="{{ route('dental-charts.edit', $chart) }}" class="btn btn-outline btn-sm">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1.25rem;">
                                    No dental charts recorded for this patient yet.
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
