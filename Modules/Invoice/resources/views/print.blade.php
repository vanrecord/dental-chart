<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
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
            background: #2563eb;
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
            background: #1d4ed8;
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

        .invoice-sheet {
            max-width: 820px;
            margin: 24px auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
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
            max-width: 72px;
            max-height: 72px;
            object-fit: contain;
        }

        .clinic-names h1 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .clinic-names h2 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .clinic-contact {
            font-size: 11.5px;
            color: #475569;
            line-height: 1.45;
        }

        .invoice-badge-title {
            text-align: right;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 0.05em;
            color: #0f172a;
            text-transform: uppercase;
        }

        .invoice-title-kh {
            font-size: 14px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .invoice-meta-item {
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
        }

        .status-paid {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .status-partial {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .status-unpaid {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Patient & Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 22px;
        }

        .info-col h3 {
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
            width: 105px;
            color: #64748b;
            flex-shrink: 0;
        }

        .info-val {
            color: #0f172a;
            font-weight: 600;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .items-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
            padding: 8px 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.03em;
        }

        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }

        .items-table tr:last-child td {
            border-bottom: 2px solid #cbd5e1;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Summary Section */
        .summary-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .summary-box {
            width: 320px;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 8px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .summary-row.total-row {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            padding: 7px 8px;
            font-weight: 800;
            font-size: 14px;
            background: #f8fafc;
            color: #0f172a;
            margin-top: 4px;
        }

        .summary-row.balance-row {
            font-weight: 800;
            font-size: 14px;
            padding: 7px 8px;
            color: #b91c1c;
            border-bottom: 2px solid #e2e8f0;
        }

        .summary-row.paid-row {
            font-weight: 700;
            color: #15803d;
        }

        /* Payments History Breakdown */
        .payments-history {
            margin-bottom: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
        }

        .payments-history h4 {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        .payments-table th {
            background: #f8fafc;
            color: #64748b;
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .payments-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Notes & Terms */
        .invoice-notes {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 11.5px;
            color: #475569;
            margin-bottom: 28px;
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
            width: 200px;
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

        .invoice-footer-message {
            text-align: center;
            font-size: 11.5px;
            color: #64748b;
            margin-top: 30px;
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

            .invoice-sheet {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 10mm 12mm !important;
                box-shadow: none !important;
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
            <span>📄 Print Invoice: {{ $invoice->invoice_number }}</span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" class="btn-print" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Print Invoice
            </button>
            <button type="button" class="btn-close" onclick="window.close()">Close Window</button>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="invoice-sheet">
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

            <div class="invoice-badge-title">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-title-kh">វិក្កយបត្រ</div>
                <div class="invoice-meta-item">Invoice #: <strong>{{ $invoice->invoice_number }}</strong></div>
                <div class="invoice-meta-item">Date: <strong>{{ $invoice->invoice_date->format('M d, Y') }}</strong></div>
                <div>
                    @if($invoice->status === 'paid')
                        <span class="status-badge status-paid">PAID / បានទូទាត់</span>
                    @elseif($invoice->status === 'partial')
                        <span class="status-badge status-partial">PARTIAL / បង់ខ្លះ</span>
                    @else
                        <span class="status-badge status-unpaid">UNPAID / មិនទាន់ទូទាត់</span>
                    @endif
                </div>
            </div>
        </header>

        <!-- Patient & Invoice Info -->
        <section class="info-grid">
            <div class="info-col">
                <h3>Billed To / ព័ត៌មានអ្នកជំងឺ</h3>
                <div class="info-row">
                    <span class="info-label">Patient Name:</span>
                    <span class="info-val">{{ $invoice->patient->full_name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Patient ID:</span>
                    <span class="info-val">{{ $invoice->patient->patient_number ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-val">{{ $invoice->patient->phone ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Gender / DOB:</span>
                    <span class="info-val">
                        {{ ucfirst($invoice->patient->sex ?? '—') }}
                        @if(!empty($invoice->patient->date_of_birth))
                            · {{ $invoice->patient->date_of_birth->format('Y-m-d') }}
                        @endif
                    </span>
                </div>
                @if(!empty($invoice->patient?->address))
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-val">{{ $invoice->patient->address }}</span>
                    </div>
                @endif
            </div>

            <div class="info-col">
                <h3>Invoice Details / ព័ត៌មានវិក្កយបត្រ</h3>
                <div class="info-row">
                    <span class="info-label">Invoice Number:</span>
                    <span class="info-val">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Invoice Date:</span>
                    <span class="info-val">{{ $invoice->invoice_date->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Status:</span>
                    <span class="info-val" style="text-transform: capitalize;">{{ $invoice->status }}</span>
                </div>
                @if($invoice->creator)
                    <div class="info-row">
                        <span class="info-label">Issued By:</span>
                        <span class="info-val">{{ $invoice->creator->name }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Visits / Receptions:</span>
                    <span class="info-val">{{ $invoice->receptions->count() }} registered</span>
                </div>
            </div>
        </section>

        <!-- Itemized Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 32px;" class="text-center">#</th>
                    <th style="width: 85px;">Date</th>
                    <th style="width: 75px;">Type</th>
                    <th>Service / Product Description</th>
                    <th style="width: 90px;">Teeth #</th>
                    <th style="width: 50px;" class="text-center">Qty</th>
                    <th style="width: 95px;" class="text-right">Unit Price</th>
                    <th style="width: 95px;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $idx => $item)
                    <tr>
                        <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                        <td>{{ $item->operate_date?->format('Y-m-d') ?? $invoice->invoice_date->format('Y-m-d') }}</td>
                        <td>
                            @if($item->isService())
                                <span style="font-size: 10.5px; font-weight: 700; color: #166534;">SERVICE</span>
                            @else
                                <span style="font-size: 10.5px; font-weight: 700; color: #0369a1;">PRODUCT</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if(!empty($item->note))
                                <div style="font-size: 11px; color: #64748b;">{{ $item->note }}</div>
                            @endif
                        </td>
                        <td>{{ $item->tooth_numbers ?: '—' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right" style="font-weight: 700;">${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 16px; color: #64748b;">
                            Standard dental consultation and general examination.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals & Balances -->
        <div class="summary-container">
            <div class="summary-box">
                @if($invoice->subtotal > 0 || $invoice->discount > 0 || $invoice->tax > 0)
                    <div class="summary-row">
                        <span style="color: #475569;">Subtotal / សរុបរង:</span>
                        <span>${{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                @endif
                @if($invoice->discount > 0)
                    <div class="summary-row" style="color: #b91c1c;">
                        <span>Discount / បញ្ចុះតម្លៃ:</span>
                        <span>-${{ number_format($invoice->discount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->tax > 0)
                    <div class="summary-row" style="color: #475569;">
                        <span>Tax / ពន្ធ:</span>
                        <span>+${{ number_format($invoice->tax, 2) }}</span>
                    </div>
                @endif

                <div class="summary-row total-row">
                    <span>TOTAL BILLED / សរុប:</span>
                    <span>${{ number_format($invoice->total_amount, 2) }}</span>
                </div>

                <div class="summary-row paid-row">
                    <span>Amount Paid / ប្រាក់បានបង់:</span>
                    <span>${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>

                <div class="summary-row balance-row">
                    <span>BALANCE DUE / នៅខ្វះ:</span>
                    <span>${{ number_format($invoice->balance_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payments Breakdown (if any) -->
        @if($invoice->payments->count() > 0)
            <div class="payments-history">
                <h4>Payment Transactions ({{ $invoice->payments->count() }}) / ប្រវត្តិការទូទាត់</h4>
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Transaction Ref</th>
                            <th class="text-right">Paid Amount</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $pmt)
                            <tr>
                                <td><strong>{{ $pmt->payment_number }}</strong></td>
                                <td>{{ $pmt->payment_date->format('Y-m-d') }}</td>
                                <td style="text-transform: uppercase;">{{ str_replace('_', ' ', $pmt->payment_method) }}</td>
                                <td>{{ $pmt->transaction_reference ?: '—' }}</td>
                                <td class="text-right" style="font-weight: 700; color: #15803d;">${{ number_format($pmt->amount, 2) }}</td>
                                <td class="text-center">
                                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #15803d;">{{ $pmt->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Optional Notes -->
        @if(!empty($invoice->notes))
            <div class="invoice-notes">
                <strong>Remarks / Notes:</strong> {{ $invoice->notes }}
            </div>
        @endif

        <!-- Signatures -->
        <div class="signature-grid">
            <div class="signature-col">
                <div class="signature-line"></div>
                <div class="signature-title">Authorized Staff / ហត្ថលេខាអ្នកចេញវិក្កយបត្រ</div>
                <div class="signature-subtitle">{{ $invoice->creator->name ?? 'Dental Clinic Cashier' }}</div>
            </div>
            <div class="signature-col">
                <div class="signature-line"></div>
                <div class="signature-title">Patient / ហត្ថលេខាអ្នកជំងឺ</div>
                <div class="signature-subtitle">{{ $invoice->patient->full_name ?? 'Customer Signature' }}</div>
            </div>
        </div>

        <div class="invoice-footer-message">
            Thank you for choosing {{ $clinicSetting?->english_title ?: 'our dental clinic' }} for your dental health care!
            <br>
            សូមអរគុណចំពោះការជឿទុកចិត្តលើសេវាកម្មព្យាបាលធ្មេញរបស់យើងខ្ញុំ!
        </div>
    </div>

</body>
</html>

