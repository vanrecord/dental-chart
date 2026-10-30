@extends('core::layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create Invoice</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Generate a new billing invoice with multiple services and products</p>
    </div>
    <a href="{{ route('invoices.index') }}" class="btn btn-outline">Back to Invoices</a>
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

<form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
    @csrf
    <input type="hidden" name="redirect_to_dental_chart" id="redirect_to_dental_chart" value="0">

    <!-- Top Card: Patient & General Details -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: #0f172a;">Invoice Information</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="patient_id" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px;">Patient <span style="color: #dc2626;">*</span></label>
                <select name="patient_id" id="patient_id" class="form-control" required style="width: 100%;">
                    <option value="">-- Select a patient --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ (old('patient_id', $preselectedPatient?->id) == $patient->id) ? 'selected' : '' }}>
                            {{ $patient->full_name }} ({{ $patient->patient_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="invoice_date" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px;">Invoice Date <span style="color: #dc2626;">*</span></label>
                <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 0; grid-column: 1 / -1;">
                <label for="notes" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px;">Notes / Memo</label>
                <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}" placeholder="Optional invoice description or notes...">
            </div>
        </div>
    </div>

    <!-- Middle Card: Invoice Items Section -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: #0f172a;">Invoice Items</h3>
                <p style="font-size: 0.8125rem; color: #64748b; margin: 2px 0 0 0;">Add clinical dental services or inventory products to this invoice</p>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" id="btnAddService" class="btn" style="background: #10b981; color: white; font-weight: 600; border: none; display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 6px; cursor: pointer;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    + Service
                </button>
                <button type="button" id="btnAddProduct" class="btn" style="background: #0284c7; color: white; font-weight: 600; border: none; display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 6px; cursor: pointer;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    + Product
                </button>
                @can('odontogram:dental_chart:create')
                    <a href="{{ route('dental-charts.create', array_filter(['patient_id' => $preselectedPatient?->id])) }}" class="btn" id="btnCreateDentalChart" style="background: #0d9488; color: white; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 6px; text-decoration: none;" title="Create Dental Charts for patient">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2C8.5 2 6 4.5 6 7.5c0 3 1.5 5 2 8 .5 3 1.5 6.5 4 6.5s3.5-3.5 4-6.5c.5-3 2-5 2-8C18 4.5 15.5 2 12 2z"></path>
                            <path d="M9 10h6"></path>
                        </svg>
                        Create Dental Charts
                    </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="data-table" id="itemsTable" style="width: 100%; min-width: 900px; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="width: 130px; padding: 10px 8px; font-size: 0.8125rem;">Operate Date</th>
                        <th style="min-width: 240px; padding: 10px 8px; font-size: 0.8125rem;">Service / Product</th>
                        <th style="width: 130px; padding: 10px 8px; font-size: 0.8125rem;">Treatment Teeth</th>
                        <th style="width: 90px; padding: 10px 8px; font-size: 0.8125rem;">Quantity</th>
                        <th style="width: 120px; padding: 10px 8px; font-size: 0.8125rem;">Unit Price ($)</th>
                        <th style="min-width: 150px; padding: 10px 8px; font-size: 0.8125rem;">Note</th>
                        <th style="width: 110px; padding: 10px 8px; font-size: 0.8125rem; text-align: right;">Amount ($)</th>
                        <th style="width: 70px; padding: 10px 8px; font-size: 0.8125rem; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <!-- Dynamic Rows Injected by JS -->
                </tbody>
            </table>
        </div>

        <div id="noItemsMessage" style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8; background: #fafafa; border: 1px dashed #e2e8f0; border-radius: 6px; margin-top: 8px;">
            <p style="margin: 0; font-size: 0.875rem;">No items added yet. Click <strong>+ Service</strong> or <strong>+ Product</strong> above to add billable items.</p>
        </div>
    </div>

    <!-- Bottom Section: Summary & Action Buttons -->
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
        <div><!-- Left empty space / extra notes if needed --></div>

        <div class="card" style="background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h4 style="font-size: 0.9375rem; font-weight: 700; margin-bottom: 0.875rem; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                Invoice Summary
            </h4>
            
            <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Subtotal:</span>
                    <strong id="displaySubtotal">$0.00</strong>
                    <input type="hidden" name="subtotal" id="inputSubtotal" value="{{ old('subtotal', '0.00') }}">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b;">Discount ($):</span>
                    <input type="number" step="0.01" min="0" name="discount" id="inputDiscount" class="form-control" style="width: 100px; text-align: right; padding: 4px 8px; font-size: 0.875rem;" value="{{ old('discount', '0.00') }}">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b;">Tax ($):</span>
                    <input type="number" step="0.01" min="0" name="tax" id="inputTax" class="form-control" style="width: 100px; text-align: right; padding: 4px 8px; font-size: 0.875rem;" value="{{ old('tax', '0.00') }}">
                </div>

                <div style="border-top: 2px solid #e2e8f0; margin-top: 4px; padding-top: 8px; display: flex; justify-content: space-between; font-size: 1.1rem; color: #0f172a;">
                    <strong>Grand Total:</strong>
                    <strong id="displayGrandTotal" style="color: #0284c7;">$0.00</strong>
                    <input type="hidden" name="total_amount" id="inputTotalAmount" value="{{ old('total_amount', '0.00') }}">
                </div>

                <div style="display: flex; justify-content: space-between; color: #166534; font-size: 0.875rem;">
                    <span>Amount Paid:</span>
                    <strong id="displayPaidAmount">$0.00</strong>
                </div>

                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 8px 12px; margin-top: 4px; display: flex; justify-content: space-between; color: #991b1b; font-size: 0.95rem;">
                    <strong>Outstanding:</strong>
                    <strong id="displayBalance">$0.00</strong>
                </div>
            </div>

            <div style="display: flex; gap: 8px; margin-top: 1.25rem;">
                <a href="{{ route('invoices.index') }}" class="btn btn-outline" style="flex: 1; text-align: center;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Create Invoice</button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const servicesCatalog = @json($services);
    const productsCatalog = @json($products);
    const oldItems = @json(old('items', []));

    const tableBody = document.getElementById('itemsTableBody');
    const noItemsMessage = document.getElementById('noItemsMessage');
    const btnAddService = document.getElementById('btnAddService');
    const btnAddProduct = document.getElementById('btnAddProduct');
    const inputDiscount = document.getElementById('inputDiscount');
    const inputTax = document.getElementById('inputTax');

    let rowIndex = 0;

    function getTodayString() {
        const today = new Date();
        const y = today.getFullYear();
        const m = String(today.getMonth() + 1).padStart(2, '0');
        const d = String(today.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function formatDateString(val) {
        if (!val) return getTodayString();
        if (typeof val === 'string') {
            if (val.includes('T')) {
                return val.split('T')[0];
            }
            if (val.length >= 10) {
                return val.substring(0, 10);
            }
        }
        return val;
    }

    function addRow(type, initialData = null) {
        noItemsMessage.style.display = 'none';

        const index = rowIndex++;
        const catalog = type === 'service' ? servicesCatalog : productsCatalog;
        const isService = type === 'service';
        const defaultDate = initialData ? formatDateString(initialData.operate_date) : getTodayString();
        const defaultQty = initialData ? (initialData.quantity || 1) : 1;
        const defaultPrice = initialData ? (initialData.unit_price || 0) : (catalog.length > 0 ? catalog[0].price : 0);
        const defaultNote = initialData ? (initialData.note || '') : '';
        const defaultTeeth = initialData ? (initialData.tooth_numbers || '') : '';
        const selectedId = initialData ? initialData.service_id : (catalog.length > 0 ? catalog[0].id : '');

        const badgeBg = isService ? '#dcfce7' : '#e0f2fe';
        const badgeColor = isService ? '#166534' : '#0369a1';
        const typeLabel = isService ? 'Service' : 'Product';

        const tr = document.createElement('tr');
        tr.dataset.index = index;
        tr.style.borderBottom = '1px solid #f1f5f9';

        let optionsHtml = `<option value="">-- Choose ${typeLabel} --</option>`;
        catalog.forEach(item => {
            const isSel = String(item.id) === String(selectedId) ? 'selected' : '';
            optionsHtml += `<option value="${item.id}" data-price="${item.price}" data-name="${item.name}" ${isSel}>${item.name} (${item.code}) - $${parseFloat(item.price).toFixed(2)}</option>`;
        });

        tr.innerHTML = `
            <td style="padding: 8px;">
                <input type="hidden" name="items[${index}][type]" value="${type}">
                <input type="date" name="items[${index}][operate_date]" class="form-control item-date" value="${defaultDate}" required style="padding: 4px 6px; font-size: 0.8125rem;">
            </td>
            <td style="padding: 8px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span class="badge" style="background: ${badgeBg}; color: ${badgeColor}; font-size: 0.7rem; text-transform: uppercase; font-weight: 700; padding: 2px 6px;">
                        ${typeLabel}
                    </span>
                    <select name="items[${index}][service_id]" class="form-control item-select" required style="padding: 4px 6px; font-size: 0.8125rem; flex: 1;">
                        ${optionsHtml}
                    </select>
                </div>
            </td>
            <td style="padding: 8px;">
                ${isService 
                    ? `<input type="text" name="items[${index}][tooth_numbers]" class="form-control item-teeth" value="${defaultTeeth}" placeholder="e.g. 11, 21" style="padding: 4px 6px; font-size: 0.8125rem;">` 
                    : `<input type="text" name="items[${index}][tooth_numbers]" class="form-control" value="N/A" disabled style="padding: 4px 6px; font-size: 0.8125rem; background: #f8fafc; color: #94a3b8;">`
                }
            </td>
            <td style="padding: 8px;">
                <input type="number" min="1" step="1" name="items[${index}][quantity]" class="form-control item-qty" value="${defaultQty}" required style="padding: 4px 6px; font-size: 0.8125rem; text-align: center;">
            </td>
            <td style="padding: 8px;">
                <input type="number" min="0" step="0.01" name="items[${index}][unit_price]" class="form-control item-price" value="${parseFloat(defaultPrice).toFixed(2)}" required style="padding: 4px 6px; font-size: 0.8125rem; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" name="items[${index}][note]" class="form-control item-note" value="${defaultNote}" placeholder="Notes..." style="padding: 4px 6px; font-size: 0.8125rem;">
            </td>
            <td style="padding: 8px; text-align: right; font-weight: 600; font-size: 0.875rem;" class="item-amount">
                $0.00
            </td>
            <td style="padding: 8px; text-align: center;">
                <button type="button" class="btn-remove-row" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; border-radius: 4px;" title="Remove row">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </td>
        `;

        tableBody.appendChild(tr);

        // Initialize Select2 on dynamically added row
        if (window.initSelect2) {
            window.initSelect2(tr);
        }

        // Attach change listeners
        const sel = tr.querySelector('.item-select');
        const priceInput = tr.querySelector('.item-price');
        const qtyInput = tr.querySelector('.item-qty');
        const removeBtn = tr.querySelector('.btn-remove-row');

        sel.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.price !== undefined) {
                priceInput.value = parseFloat(opt.dataset.price).toFixed(2);
            }
            recalculateTotals();
        });

        qtyInput.addEventListener('input', recalculateTotals);
        priceInput.addEventListener('input', recalculateTotals);

        removeBtn.addEventListener('click', function () {
            if (confirm('Are you sure you want to remove this item?')) {
                tr.remove();
                if (tableBody.children.length === 0) {
                    noItemsMessage.style.display = 'block';
                }
                recalculateTotals();
            }
        });

        recalculateTotals();
    }

    function recalculateTotals() {
        let subtotal = 0;
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(tr => {
            const qty = parseFloat(tr.querySelector('.item-qty')?.value) || 0;
            const price = parseFloat(tr.querySelector('.item-price')?.value) || 0;
            const amount = Math.max(0, qty * price);
            tr.querySelector('.item-amount').textContent = '$' + amount.toFixed(2);
            subtotal += amount;
        });

        const discount = parseFloat(inputDiscount.value) || 0;
        const tax = parseFloat(inputTax.value) || 0;
        const grandTotal = Math.max(0, subtotal - discount + tax);
        const paid = 0.00; // New invoice has 0 paid
        const balance = Math.max(0, grandTotal - paid);

        document.getElementById('displaySubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('inputSubtotal').value = subtotal.toFixed(2);
        document.getElementById('displayGrandTotal').textContent = '$' + grandTotal.toFixed(2);
        document.getElementById('inputTotalAmount').value = grandTotal.toFixed(2);
        document.getElementById('displayPaidAmount').textContent = '$' + paid.toFixed(2);
        document.getElementById('displayBalance').textContent = '$' + balance.toFixed(2);
    }

    btnAddService.addEventListener('click', function () {
        addRow('service');
    });

    btnAddProduct.addEventListener('click', function () {
        addRow('product');
    });

    inputDiscount.addEventListener('input', recalculateTotals);
    inputTax.addEventListener('input', recalculateTotals);

    const patientSelect = document.getElementById('patient_id');
    const btnCreateDentalChart = document.getElementById('btnCreateDentalChart');
    if (patientSelect && btnCreateDentalChart) {
        patientSelect.addEventListener('change', function () {
            const pId = this.value;
            const baseUrl = "{{ route('dental-charts.create') }}";
            btnCreateDentalChart.href = pId ? `${baseUrl}?patient_id=${encodeURIComponent(pId)}` : baseUrl;
        });
    }

    if (btnCreateDentalChart) {
        btnCreateDentalChart.addEventListener('click', function (e) {
            e.preventDefault();

            const form = document.getElementById('invoiceForm');
            const redirectInput = document.getElementById('redirect_to_dental_chart');

            if (form && redirectInput) {
                recalculateTotals();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                redirectInput.value = '1';
                btnCreateDentalChart.style.pointerEvents = 'none';
                btnCreateDentalChart.style.opacity = '0.7';
                btnCreateDentalChart.textContent = 'Saving Items & Opening Chart...';
                form.submit();
            } else {
                window.location.href = btnCreateDentalChart.href;
            }
        });
    }

    // If returning from validation errors with oldItems, render them
    if (Array.isArray(oldItems) && oldItems.length > 0) {
        oldItems.forEach(item => {
            addRow(item.type || 'service', item);
        });
    }
});
</script>
@endsection
