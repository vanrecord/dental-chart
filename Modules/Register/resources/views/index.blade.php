@extends('core::layouts.app')

@section('title', 'Patient Visit Registration')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Patient Visit Registration</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Register a patient for a clinical visit, create new billing invoices, or link follow-up consultations</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="button" class="btn btn-primary" onclick="openNewPatientModal()">+ Quick Create Patient</button>
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

@php
    $effectiveWorkflow = old('workflow', $preselectedWorkflow ?? 'new_invoice');
    $selectedInvoiceModel = null;
    if ($preselectedPatient && $preselectedPatient->invoices->isNotEmpty()) {
        if (old('invoice_id')) {
            $selectedInvoiceModel = $preselectedPatient->invoices->firstWhere('id', old('invoice_id'));
        } elseif (!empty($preselectedInvoiceId)) {
            $selectedInvoiceModel = $preselectedPatient->invoices->firstWhere('id', $preselectedInvoiceId);
        }
        if (!$selectedInvoiceModel) {
            $selectedInvoiceModel = $preselectedPatient->invoices->first();
        }
    }
@endphp

<form method="POST" action="{{ route('register.store') }}" id="registrationForm">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            
            <!-- STEP 1: Find or Create Patient -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary); color: #fff; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        <h2 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: #0f172a;">Find or Select Patient</h2>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" onclick="openNewPatientModal()">+ New Patient</button>
                </div>

                <div style="position: relative;">
                    <label for="patientSearchInput" style="font-size: 0.8125rem; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Search Existing Patient <span style="font-weight: 400; color: #64748b; font-size: 0.75rem;">(enter at least 2 characters)</span></label>
                    <input type="text" id="patientSearchInput" class="form-control" placeholder="Search by patient ID (e.g. P000125), full name, or phone number (min. 2 characters)..." autocomplete="off">
                    
                    <div id="patientSearchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 50; max-height: 260px; overflow-y: auto; margin-top: 4px;">
                        <!-- AJAX populated results -->
                    </div>
                </div>

                <!-- STEP 2: Selected Patient Details Card -->
                <div id="selectedPatientCard" style="{{ $preselectedPatient ? '' : 'display: none;' }} margin-top: 1.25rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <span class="badge" id="dispPatientNumber" style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px; display: inline-block;">
                                {{ $preselectedPatient?->patient_number ?? 'P-000000' }}
                            </span>
                            <h3 id="dispFullName" style="font-size: 1.15rem; font-weight: 700; margin: 2px 0 6px; color: #0f172a;">
                                {{ $preselectedPatient?->full_name ?? '—' }}
                            </h3>
                            <div style="display: flex; gap: 14px; font-size: 0.8125rem; color: #64748b; flex-wrap: wrap;">
                                <div><strong>Gender:</strong> <span id="dispGender">{{ ucfirst($preselectedPatient?->sex ?? '—') }}</span></div>
                                <div><strong>DOB:</strong> <span id="dispDob">{{ optional($preselectedPatient?->date_of_birth)->format('Y-m-d') ?? '—' }}</span></div>
                                <div><strong>Phone:</strong> <span id="dispPhone">{{ $preselectedPatient?->phone ?? '—' }}</span></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline btn-sm" onclick="clearSelectedPatient()" style="font-size: 0.75rem; color: #64748b;">Change</button>
                    </div>
                </div>

                <input type="hidden" name="patient_id" id="patientIdInput" value="{{ $preselectedPatient?->id ?? old('patient_id') }}" required>
            </div>

            <!-- STEP 3: Choose Registration Workflow -->
            <div class="card">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1rem;">
                    <span style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary); color: #fff; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center;">2</span>
                    <h2 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: #0f172a;">Registration & Billing Workflow</h2>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1.25rem;">
                    <!-- Option A: New Invoice -->
                    <label id="optNewInvoiceLabel" style="border: {{ $effectiveWorkflow === 'new_invoice' ? '2px solid var(--primary)' : '1px solid #cbd5e1' }}; background: {{ $effectiveWorkflow === 'new_invoice' ? '#f0fdf4' : '#ffffff' }}; border-radius: 8px; padding: 14px; cursor: pointer; display: flex; gap: 10px; align-items: flex-start;">
                        <input type="radio" name="workflow" value="new_invoice" id="workflowNewInvoice" {{ $effectiveWorkflow === 'new_invoice' ? 'checked' : '' }} onchange="switchWorkflow('new_invoice')" style="margin-top: 3px; accent-color: var(--primary);">
                        <div>
                            <strong style="color: #0f172a; font-size: 0.9375rem; display: block;">Option A: New Invoice</strong>
                            <span style="color: #475569; font-size: 0.8125rem; display: block; margin-top: 2px; line-height: 1.4;">
                                Create a new invoice and initial reception record for this visit.
                            </span>
                        </div>
                    </label>

                    <!-- Option B: Follow-up Visit -->
                    <label id="optFollowUpLabel" style="border: {{ $effectiveWorkflow === 'follow_up' ? '2px solid var(--primary)' : '1px solid #cbd5e1' }}; background: {{ $effectiveWorkflow === 'follow_up' ? '#f0fdf4' : '#ffffff' }}; border-radius: 8px; padding: 14px; cursor: pointer; display: flex; gap: 10px; align-items: flex-start;">
                        <input type="radio" name="workflow" value="follow_up" id="workflowFollowUp" {{ $effectiveWorkflow === 'follow_up' ? 'checked' : '' }} onchange="switchWorkflow('follow_up')" style="margin-top: 3px; accent-color: var(--primary);">
                        <div>
                            <strong style="color: #0f172a; font-size: 0.9375rem; display: block;">Option B: Follow-up Visit</strong>
                            <span style="color: #475569; font-size: 0.8125rem; display: block; margin-top: 2px; line-height: 1.4;">
                                Link a new reception to an existing eligible invoice without creating another invoice.
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Option A Form Fields -->
                <div id="newInvoiceFields" style="{{ $effectiveWorkflow === 'new_invoice' ? 'display: block;' : 'display: none;' }}">
                    <div class="form-group">
                        <label for="visit_type">Visit Type</label>
                        <select name="visit_type" id="visit_type" class="form-control">
                            <option value="initial" {{ old('visit_type') == 'initial' ? 'selected' : '' }}>Initial Consultation</option>
                            <option value="examination" {{ old('visit_type') == 'examination' ? 'selected' : '' }}>Dental Examination</option>
                            <option value="emergency" {{ old('visit_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="procedure" {{ old('visit_type') == 'procedure' ? 'selected' : '' }}>Treatment / Procedure</option>
                        </select>
                    </div>
                </div>

                <!-- Option B Existing Invoices List -->
                <div id="followUpFields" style="{{ $effectiveWorkflow === 'follow_up' ? 'display: block;' : 'display: none;' }}">
                    <div style="font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">
                        Select Patient's Existing Eligible Invoice:
                    </div>

                    <div id="invoicesLoading" style="display: none; padding: 1rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                        Loading patient invoices...
                    </div>

                    <div id="invoicesEmpty" style="display: none; padding: 1.5rem; text-align: center; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; color: #92400e; font-size: 0.875rem;">
                        No eligible invoices found for this patient. Please choose <strong>Option A: New Invoice</strong> instead.
                    </div>

                    <div id="invoicesTableWrap" class="table-wrap" style="max-height: 240px; overflow-y: auto;">
                        <table style="font-size: 0.8125rem;">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">Select</th>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Receptions</th>
                                </tr>
                            </thead>
                            <tbody id="invoicesTbody">
                                @if($preselectedPatient && $preselectedPatient->invoices->isNotEmpty())
                                    @foreach($preselectedPatient->invoices as $inv)
                                        @php
                                            $isChecked = $selectedInvoiceModel && ($selectedInvoiceModel->id == $inv->id);
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="radio" name="invoice_id" value="{{ $inv->id }}" {{ $isChecked ? 'checked' : '' }} onchange="selectExistingInvoice({{ json_encode($inv) }})">
                                            </td>
                                            <td><strong>{{ $inv->invoice_number }}</strong></td>
                                            <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                                            <td>${{ number_format($inv->total_amount, 2) }}</td>
                                            <td>${{ number_format($inv->paid_amount, 2) }}</td>
                                            <td style="font-weight: 700; color: {{ $inv->balance_amount > 0 ? '#b91c1c' : '#15803d' }};">
                                                ${{ number_format($inv->balance_amount, 2) }}
                                            </td>
                                            <td><span class="badge">{{ ucfirst($inv->status) }}</span></td>
                                            <td>{{ $inv->receptions_count }} visits</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label for="chief_complaint">Chief Complaint / Reason for Visit</label>
                    <input type="text" name="chief_complaint" id="chief_complaint" class="form-control" value="{{ old('chief_complaint') }}" placeholder="e.g. Tooth sensitivity, regular cleaning checkup, follow-up on restoration...">
                </div>

                <div class="form-group">
                    <label for="notes">Additional Clinical / Billing Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Optional notes for this visit or invoice...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- STEP 4: Confirm and Summary Sidebar -->
        <div style="position: sticky; top: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <div class="card" style="border-top: 4px solid var(--primary);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.75rem;">
                    <span style="width: 24px; height: 24px; border-radius: 50%; background: #0369a1; color: #fff; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center;">3</span>
                    <h2 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: #0f172a;">Registration Summary</h2>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.8125rem;">
                    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.4rem;">
                        <span style="color: var(--text-muted); display: block;">Patient</span>
                        <strong id="summaryPatientName" style="color: #0f172a; font-size: 0.875rem;">{{ $preselectedPatient?->full_name ?? 'None selected' }}</strong>
                    </div>

                    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.4rem;">
                        <span style="color: var(--text-muted); display: block;">Workflow Option</span>
                        <strong id="summaryWorkflow" style="color: var(--primary);">
                            {{ $effectiveWorkflow === 'follow_up' ? 'Option B: Follow-up Visit' : 'Option A: New Invoice' }}
                        </strong>
                    </div>

                    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.4rem;">
                        <span style="color: var(--text-muted); display: block;">Invoice Reference</span>
                        <strong id="summaryInvoiceRef">
                            @if($effectiveWorkflow === 'follow_up' && $selectedInvoiceModel)
                                {{ $selectedInvoiceModel->invoice_number }} (Reused)
                            @elseif($effectiveWorkflow === 'follow_up')
                                Please select invoice below
                            @else
                                Auto-generated upon save
                            @endif
                        </strong>
                    </div>

                    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.4rem;">
                        <span style="color: var(--text-muted); display: block;">Outstanding Balance</span>
                        <strong id="summaryBalance" style="color: #0f172a;">
                            @if($effectiveWorkflow === 'follow_up' && $selectedInvoiceModel)
                                ${{ number_format($selectedInvoiceModel->balance_amount, 2) }}
                            @else
                                $0.00
                            @endif
                        </strong>
                    </div>

                    <div>
                        <span style="color: var(--text-muted); display: block;">Reception Type</span>
                        <strong id="summaryReceptionType">
                            {{ $effectiveWorkflow === 'follow_up' ? 'Follow-up Visit (Checked In)' : 'Initial Visit (Checked In)' }}
                        </strong>
                    </div>
                </div>

                <div style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 8px;">
                    <button type="submit" id="btnSubmitRegister" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;" {{ $preselectedPatient ? '' : 'disabled' }}>
                        Confirm &amp; Register Visit
                    </button>
                    <a href="{{ route('register.index') }}" class="btn btn-outline" style="width: 100%; justify-content: center; font-size: 0.8125rem;">
                        Cancel / Reset
                    </a>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 0.75rem; color: #64748b; line-height: 1.4;">
                <strong style="color: #334155;">💡 Integrity Notice:</strong> All visit registrations and billing modifications are executed within secure database transactions. Reusing an existing invoice links a new reception without duplicating invoices.
            </div>
        </div>
    </div>
</form>

<!-- Modal: Quick Create Patient -->
<div id="quickPatientModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1000; place-items: center; padding: 20px;">
    <div style="background: #fff; width: min(600px, 100%); border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); overflow: hidden;">
        <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #0f172a;">Quick Register New Patient</h3>
            <button type="button" onclick="closeNewPatientModal()" style="border: 0; background: transparent; font-size: 1.25rem; cursor: pointer; color: #64748b;">&times;</button>
        </div>

        <form id="quickPatientForm" style="padding: 20px;">
            <div id="quickPatientErrors" style="display: none; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 8px 12px; border-radius: 6px; font-size: 0.8125rem; margin-bottom: 12px;"></div>

            <div class="form-row">
                <div class="form-group">
                    <label for="qp_patient_number">Patient ID / Number *</label>
                    <input type="text" id="qp_patient_number" class="form-control" required placeholder="e.g. P000126">
                </div>
                <div class="form-group">
                    <label for="qp_full_name">Full Name *</label>
                    <input type="text" id="qp_full_name" class="form-control" required placeholder="e.g. John Doe">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="qp_sex">Sex</label>
                    <select id="qp_sex" class="form-control">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="qp_date_of_birth">Date of Birth</label>
                    <input type="date" id="qp_date_of_birth" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="qp_phone">Phone Number</label>
                <input type="text" id="qp_phone" class="form-control" placeholder="+1 (555) 000-0000">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 1.25rem;">
                <button type="button" class="btn btn-outline" onclick="closeNewPatientModal()">Cancel</button>
                <button type="submit" id="qp_submit_btn" class="btn btn-primary">Save &amp; Select Patient</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentSelectedPatient = @json($preselectedPatient);
let selectedInvoice = @json($selectedInvoiceModel);
let preselectedInvoiceId = @json($preselectedInvoiceId ?? null);

// Search patients via AJAX with debouncing
const searchInput = document.getElementById('patientSearchInput');
const searchResults = document.getElementById('patientSearchResults');
let searchTimeout = null;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('register.search_patients') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    searchResults.innerHTML = `<div style="padding: 12px; font-size: 0.8125rem; color: #64748b; text-align: center;">No matching patients found. Click "+ New Patient" to create.</div>`;
                } else {
                    searchResults.innerHTML = data.map(p => `
                        <div onclick='selectPatient(${JSON.stringify(p)})' style="padding: 10px 14px; border-bottom: 1px solid #f1f5f9; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                            <div>
                                <strong style="color: #0f172a; font-size: 0.875rem;">${p.full_name}</strong>
                                <span style="font-size: 0.75rem; color: #0284c7; margin-left: 6px;">[${p.patient_number}]</span>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Phone: ${p.phone || '—'} · Gender: ${p.sex || '—'}</div>
                            </div>
                            <span class="btn btn-outline btn-sm" style="font-size: 0.7rem; padding: 2px 8px;">Select</span>
                        </div>
                    `).join('');
                }
                searchResults.style.display = 'block';
            })
            .catch(err => console.error(err));
        }, 250);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
}

function selectPatient(patient) {
    currentSelectedPatient = patient;
    document.getElementById('patientIdInput').value = patient.id;
    document.getElementById('dispPatientNumber').textContent = patient.patient_number;
    document.getElementById('dispFullName').textContent = patient.full_name;
    document.getElementById('dispGender').textContent = patient.sex ? patient.sex.charAt(0).toUpperCase() + patient.sex.slice(1) : '—';
    document.getElementById('dispDob').textContent = patient.date_of_birth ? patient.date_of_birth.substring(0, 10) : '—';
    document.getElementById('dispPhone').textContent = patient.phone || '—';

    document.getElementById('selectedPatientCard').style.display = 'block';
    searchResults.style.display = 'none';
    searchInput.value = '';

    document.getElementById('summaryPatientName').textContent = patient.full_name;
    document.getElementById('btnSubmitRegister').disabled = false;

    // Load patient's eligible invoices
    loadPatientInvoices(patient.id);
}

function clearSelectedPatient() {
    currentSelectedPatient = null;
    document.getElementById('patientIdInput').value = '';
    document.getElementById('selectedPatientCard').style.display = 'none';
    document.getElementById('summaryPatientName').textContent = 'None selected';
    document.getElementById('btnSubmitRegister').disabled = true;
    document.getElementById('invoicesTbody').innerHTML = '';
}

function loadPatientInvoices(patientId) {
    const loading = document.getElementById('invoicesLoading');
    const emptyNotice = document.getElementById('invoicesEmpty');
    const tableWrap = document.getElementById('invoicesTableWrap');
    const tbody = document.getElementById('invoicesTbody');

    loading.style.display = 'block';
    emptyNotice.style.display = 'none';
    tableWrap.style.display = 'none';
    tbody.innerHTML = '';

    fetch(`{{ url('register/patient-invoices') }}/${patientId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        loading.style.display = 'none';
        if (!data.invoices || data.invoices.length === 0) {
            emptyNotice.style.display = 'block';
        } else {
            tableWrap.style.display = 'block';
            tbody.innerHTML = data.invoices.map((inv, idx) => {
                const isMatch = preselectedInvoiceId ? (String(inv.id) === String(preselectedInvoiceId)) : (idx === 0);
                return `
                <tr>
                    <td>
                        <input type="radio" name="invoice_id" value="${inv.id}" ${isMatch ? 'checked' : ''} onchange='selectExistingInvoice(${JSON.stringify(inv)})'>
                    </td>
                    <td><strong>${inv.invoice_number}</strong></td>
                    <td>${inv.invoice_date.substring(0, 10)}</td>
                    <td>$${parseFloat(inv.total_amount).toFixed(2)}</td>
                    <td>$${parseFloat(inv.paid_amount).toFixed(2)}</td>
                    <td style="font-weight: 700; color: ${inv.balance_amount > 0 ? '#b91c1c' : '#15803d'};">
                        $${parseFloat(inv.balance_amount).toFixed(2)}
                    </td>
                    <td><span class="badge">${inv.status}</span></td>
                    <td>${inv.receptions_count} visits</td>
                </tr>
            `}).join('');

            // Automatically select matched or first invoice
            const autoChosen = (preselectedInvoiceId ? data.invoices.find(i => String(i.id) === String(preselectedInvoiceId)) : null) || data.invoices[0];
            selectExistingInvoice(autoChosen);
        }
    })
    .catch(err => {
        loading.style.display = 'none';
        console.error(err);
    });
}

function selectExistingInvoice(inv) {
    selectedInvoice = inv;
    if (document.getElementById('workflowFollowUp').checked) {
        document.getElementById('summaryInvoiceRef').textContent = `${inv.invoice_number} (Reused)`;
        document.getElementById('summaryBalance').textContent = `$${parseFloat(inv.balance_amount).toFixed(2)}`;
    }
}

function switchWorkflow(mode) {
    const newFields = document.getElementById('newInvoiceFields');
    const followFields = document.getElementById('followUpFields');
    const optNewLabel = document.getElementById('optNewInvoiceLabel');
    const optFollowLabel = document.getElementById('optFollowUpLabel');

    if (mode === 'new_invoice') {
        newFields.style.display = 'block';
        followFields.style.display = 'none';
        optNewLabel.style.borderColor = 'var(--primary)';
        optNewLabel.style.background = '#f0fdf4';
        optFollowLabel.style.borderColor = '#cbd5e1';
        optFollowLabel.style.background = '#ffffff';

        document.getElementById('summaryWorkflow').textContent = 'Option A: New Invoice';
        document.getElementById('summaryInvoiceRef').textContent = 'Auto-generated upon save';
        document.getElementById('summaryBalance').textContent = '$0.00';
        document.getElementById('summaryReceptionType').textContent = 'Initial Visit (Checked In)';
    } else {
        newFields.style.display = 'none';
        followFields.style.display = 'block';
        optFollowLabel.style.borderColor = 'var(--primary)';
        optFollowLabel.style.background = '#f0fdf4';
        optNewLabel.style.borderColor = '#cbd5e1';
        optNewLabel.style.background = '#ffffff';

        document.getElementById('summaryWorkflow').textContent = 'Option B: Follow-up Visit';
        if (selectedInvoice) {
            document.getElementById('summaryInvoiceRef').textContent = `${selectedInvoice.invoice_number} (Reused)`;
            document.getElementById('summaryBalance').textContent = `$${parseFloat(selectedInvoice.balance_amount).toFixed(2)}`;
        } else {
            document.getElementById('summaryInvoiceRef').textContent = 'Please select invoice below';
            document.getElementById('summaryBalance').textContent = '—';
        }
        document.getElementById('summaryReceptionType').textContent = 'Follow-up Visit (Checked In)';

        if (currentSelectedPatient && document.getElementById('invoicesTbody').children.length === 0) {
            loadPatientInvoices(currentSelectedPatient.id);
        }
    }
}

// Quick Create Patient Modal
function openNewPatientModal() {
    document.getElementById('quickPatientModal').style.display = 'grid';
}

function closeNewPatientModal() {
    document.getElementById('quickPatientModal').style.display = 'none';
    document.getElementById('quickPatientErrors').style.display = 'none';
    document.getElementById('quickPatientForm').reset();
}

document.getElementById('quickPatientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('qp_submit_btn');
    const errBox = document.getElementById('quickPatientErrors');
    btn.disabled = true;
    errBox.style.display = 'none';

    const payload = {
        patient_number: document.getElementById('qp_patient_number').value,
        full_name: document.getElementById('qp_full_name').value,
        sex: document.getElementById('qp_sex').value,
        date_of_birth: document.getElementById('qp_date_of_birth').value,
        phone: document.getElementById('qp_phone').value,
    };

    fetch('{{ route('register.quick_create_patient') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json();
        btn.disabled = false;
        if (!res.ok) {
            let errorMsg = data.message || 'Validation error.';
            if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('<br>');
            }
            errBox.innerHTML = errorMsg;
            errBox.style.display = 'block';
        } else {
            closeNewPatientModal();
            selectPatient(data.patient);
        }
    })
    .catch(err => {
        btn.disabled = false;
        errBox.textContent = 'Network or server error.';
        errBox.style.display = 'block';
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const initialWorkflow = "{{ $effectiveWorkflow }}";
    if (initialWorkflow === 'follow_up') {
        switchWorkflow('follow_up');
        if (selectedInvoice) {
            selectExistingInvoice(selectedInvoice);
        }
    }
});
</script>
@endsection
