<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Khmer OS", "Khmer OS Battambang";
            color: #1e293b;
            background-color: #f1f5f9;
            font-size: 13px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .btn-print {
            background: #16a34a;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-print:hover {
            background: #15803d;
        }

        .btn-close {
            background: #334155;
            color: #ffffff;
            border: none;
            padding: 8px 14px;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-close:hover {
            background: #475569;
        }

        .receipt-sheet {
            max-width: 760px;
            margin: 24px auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        /* Clinic Header */
        .clinic-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .clinic-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .clinic-logo {
            max-width: 68px;
            max-height: 68px;
            object-fit: contain;
        }

        .clinic-names h1 {
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .clinic-names h2 {
            font-size: 14.5px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .clinic-contact {
            font-size: 11.5px;
            color: #475569;
            line-height: 1.45;
        }

        .receipt-badge-title {
            text-align: right;
        }

        .receipt-title {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 0.05em;
            color: #15803d;
            text-transform: uppercase;
        }

        .receipt-title-kh {
            font-size: 13.5px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .receipt-meta-item {
            font-size: 12px;
            color: #475569;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 4px;
            margin-top: 6px;
            letter-spacing: 0.05em;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 22px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 16px;
        }

        .info-card h3 {
            font-size: 11.5px;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }

        .info-row {
            display: flex;
            margin-bottom: 4px;
            font-size: 12px;
        }

        .info-label {
            width: 110px;
            color: #64748b;
            flex-shrink: 0;
        }

        .info-val {
            color: #0f172a;
            font-weight: 600;
        }

        /* Prominent Paid Amount Banner */
        .amount-banner {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 8px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .amount-label {
            font-size: 13px;
            font-weight: 700;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .amount-val {
            font-size: 28px;
            font-weight: 900;
            color: #15803d;
        }

        /* Breakdown Table */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 22px;
        }

        .breakdown-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
            padding: 8px 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            font-size: 11px;
            text-transform: uppercase;
        }

        .breakdown-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Notes & Remarks */
        .receipt-notes {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 11.5px;
            color: #475569;
            margin-bottom: 30px;
        }

        /* Signatures */
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 36px;
            padding-top: 10px;
        }

        .signature-col {
            text-align: center;
        }

        .signature-line {
            width: 190px;
            margin: 48px auto 6px;
            border-top: 1px solid #475569;
        }

        .signature-title {
            font-weight: 700;
            font-size: 12px;
            color: #1e293b;
        }

        .signature-subtitle {
            font-size: 11px;
            color: #64748b;
        }

        .receipt-footer-message {
            text-align: center;
            font-size: 11.5px;
            color: #64748b;
            margin-top: 32px;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .no-print-bar,
            .no-print {
                display: none !important;
            }

            .receipt-sheet {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 10mm 12mm !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            @page {
                size: A4;
                margin: 8mm 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- Top Floating Actions Bar (Hidden on Print) -->
    <div class="no-print-bar">
        <div style="font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
            <span>🧾 Print Payment Receipt: {{ $payment->payment_number }}</span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" class="btn-print" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Print Receipt
            </button>
            <button type="button" class="btn-close" onclick="window.close()">Close Window</button>
        </div>
    </div>

    <!-- Printable Receipt Sheet -->
    <div class="receipt-sheet">
        <!-- Clinic Header -->
        <header class="clinic-header">
            <div class="clinic-brand">
                @if(!empty($clinicSetting?->logo_url))
                    <img src="{{ $clinicSetting->logo_url }}" alt="Clinic Logo" class="clinic-logo">
                @endif
                <div class="clinic-names">
                    @if(!empty($clinicSetting?->khmer_title))
                        <h2 style="{{ !empty($clinicSetting->khmer_title_color) ? 'color: ' . $clinicSetting->khmer_title_color . ';' : 'color: #0f172a;' }}">
                            {{ $clinicSetting->khmer_title }}
                        </h2>
                    @endif
                    <h1 style="{{ !empty($clinicSetting?->english_title_color) ? 'color: ' . $clinicSetting->english_title_color . ';' : 'color: #0f172a;' }}">
                        {{ $clinicSetting?->english_title ?: 'OdontoCare Dental Clinic' }}
                    </h1>
                    <div class="clinic-contact">
                        @if(!empty($clinicSetting?->address))
                            <div>{!! $clinicSetting->address !!}</div>
                        @else
                            <div>No. 123, Preah Sihanouk Blvd, Phnom Penh, Cambodia</div>
                        @endif
                        <div>
                            @if(!empty($clinicSetting?->phone)) Phone: <strong>{{ $clinicSetting->phone }}</strong> @endif
                            @if(!empty($clinicSetting?->email)) · Email: <strong>{{ $clinicSetting->email }}</strong> @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="receipt-badge-title">
                <div class="receipt-title">RECEIPT</div>
                <div class="receipt-title-kh">បង្កាន់ដៃទទួលប្រាក់</div>
                <div class="receipt-meta-item">Receipt #: <strong>{{ $payment->payment_number }}</strong></div>
                <div class="receipt-meta-item">Date: <strong>{{ $payment->payment_date->format('M d, Y') }}</strong></div>
                <div>
                    <span class="status-badge">{{ strtoupper($payment->status) }}</span>
                </div>
            </div>
        </header>

        <!-- Amount Received Banner -->
        <div class="amount-banner">
            <div>
                <span class="amount-label">Amount Received / ចំនួនទឹកប្រាក់ទទួលបាន</span>
                <div style="font-size: 11.5px; color: #166534; margin-top: 2px;">
                    Method: <strong style="text-transform: uppercase;">{{ str_replace('_', ' ', $payment->payment_method) }}</strong>
                    @if(!empty($payment->transaction_reference))
                        · Ref: <strong>{{ $payment->transaction_reference }}</strong>
                    @endif
                </div>
            </div>
            <div class="amount-val">
                ${{ number_format($payment->amount, 2) }} <span style="font-size: 13px; font-weight: 700; color: #166534;">{{ $payment->currency ?: 'USD' }}</span>
            </div>
        </div>

        @php
            $patient = $payment->patient ?? $payment->invoice?->patient;
            $invoice = $payment->invoice;
        @endphp

        <!-- Patient & Receipt Info Grid -->
        <section class="info-grid">
            <div class="info-card">
                <h3>Received From / ព័ត៌មានអ្នកបង់ប្រាក់</h3>
                <div class="info-row">
                    <span class="info-label">Patient Name:</span>
                    <span class="info-val">{{ $patient->full_name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Patient ID:</span>
                    <span class="info-val">{{ $patient->patient_number ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone Number:</span>
                    <span class="info-val">{{ $patient->phone ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Gender / DOB:</span>
                    <span class="info-val">
                        {{ ucfirst($patient->sex ?? '—') }}
                        @if(!empty($patient->date_of_birth))
                            · {{ $patient->date_of_birth->format('Y-m-d') }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="info-card">
                <h3>Payment Details / ព័ត៌មានការទូទាត់</h3>
                <div class="info-row">
                    <span class="info-label">Receipt Number:</span>
                    <span class="info-val">{{ $payment->payment_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Date:</span>
                    <span class="info-val">{{ $payment->payment_date->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-val" style="text-transform: uppercase;">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Reference #:</span>
                    <span class="info-val">{{ $payment->transaction_reference ?: '—' }}</span>
                </div>
                @if($payment->creator)
                    <div class="info-row">
                        <span class="info-label">Cashier / Staff:</span>
                        <span class="info-val">{{ $payment->creator->name }}</span>
                    </div>
                @endif
            </div>
        </section>

        <!-- Linked Invoice Summary Table -->
        @if($invoice)
            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th>Linked Invoice #</th>
                        <th>Invoice Date</th>
                        <th class="text-right">Total Billed</th>
                        <th class="text-right">This Receipt</th>
                        <th class="text-right">Remaining Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                        <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                        <td class="text-right">${{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="text-right" style="font-weight: 700; color: #15803d;">
                            ${{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="text-right" style="font-weight: 700; color: {{ $invoice->balance_amount > 0 ? '#b91c1c' : '#15803d' }};">
                            ${{ number_format($invoice->balance_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        <!-- Remarks -->
        @if(!empty($payment->notes))
            <div class="receipt-notes">
                <strong>Remarks / Notes:</strong> {{ $payment->notes }}
            </div>
        @endif

        <!-- Signatures -->
        <div class="signature-grid">
            <div class="signature-col">
                <div class="signature-line"></div>
                <div class="signature-title">Cashier / ហត្ថលេខាអ្នកទទួលប្រាក់</div>
                <div class="signature-subtitle">{{ $payment->creator->name ?? 'Dental Clinic Cashier' }}</div>
            </div>
            <div class="signature-col">
                <div class="signature-line"></div>
                <div class="signature-title">Paid By / ហត្ថលេខាអ្នកបង់ប្រាក់</div>
                <div class="signature-subtitle">{{ $patient->full_name ?? 'Customer Signature' }}</div>
            </div>
        </div>

        <div class="receipt-footer-message">
            Thank you for your payment to {{ $clinicSetting?->english_title ?: 'our dental clinic' }}!
            <br>
            សូមអរគុណចំពោះការទូទាត់ប្រាក់! សូមជូនពរលោកអ្នកមានសុខភាពល្អ និងស្នាមញញឹមស្រស់ថ្លា!
        </div>
    </div>

</body>
</html>

