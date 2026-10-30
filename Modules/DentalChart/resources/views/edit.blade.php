@extends('core::layouts.app')

@section('title', 'Edit Dental Chart #' . $dentalChart->id)

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="page-title">Edit Dental Chart #{{ $dentalChart->id }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Patient: {{ $dentalChart->patient->full_name }}</p>
    </div>
    <div class="actions no-print" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-left: auto; justify-content: flex-end;">
        <a href="{{ !empty($invoiceId) ? route('dental.show', ['dentalChart' => $dentalChart, 'invoice_id' => $invoiceId]) : route('dental.show', $dentalChart) }}" class="btn btn-primary">Open Interactive Chart</a>
        <a href="{{ route('dental-charts.index') }}" class="btn btn-outline">Back to Charts</a>
        @if(!empty($invoiceId))
            <a href="{{ route('invoices.edit', $invoiceId) }}" class="btn btn-outline" style="border-color: #0d9488; color: #0d9488; font-weight: 600; margin-left: auto;">
                ← Back to Invoice
            </a>
        @endif
    </div>
</div>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form method="POST" action="{{ route('dental-charts.update', $dentalChart) }}">
        @csrf
        @method('PUT')

        @if(!empty($invoiceId))
            <input type="hidden" name="patient_id" value="{{ old('patient_id', $dentalChart->patient_id) }}">
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #475569; margin-bottom: 4px;">Patient</label>
                <div style="padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 700; color: #0f172a; display: flex; align-items: center; justify-content: space-between;">
                    <span>👤 {{ $dentalChart->patient->full_name }} <span style="font-weight: 500; color: #64748b;">({{ $dentalChart->patient->patient_number }})</span></span>
                    <span style="font-size: 0.75rem; background: #ccfbf1; color: #0f766e; padding: 2px 8px; border-radius: 999px; font-weight: 600;">Locked for Invoice</span>
                </div>
            </div>
        @else
            <div class="form-group">
                <label for="patient_id">Patient *</label>
                <select name="patient_id" id="patient_id" class="form-control" required>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id', $dentalChart->patient_id) == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }} ({{ $patient->patient_number }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="form-row">
            <div class="form-group">
                <label for="chart_date">Chart Date *</label>
                <input type="date" id="chart_date" name="chart_date" class="form-control" value="{{ old('chart_date', $dentalChart->chart_date->format('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label for="dentition_type">Dentition Type</label>
                <select id="dentition_type" name="dentition_type" class="form-control">
                    <option value="mixed" {{ old('dentition_type', $dentalChart->dentition_type) === 'mixed' ? 'selected' : '' }}>Mixed</option>
                    <option value="permanent" {{ old('dentition_type', $dentalChart->dentition_type) === 'permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="primary" {{ old('dentition_type', $dentalChart->dentition_type) === 'primary' ? 'selected' : '' }}>Primary / Deciduous</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="examination_notes">Examination / General Notes</label>
            <textarea id="examination_notes" name="examination_notes" class="form-control" rows="3">{{ old('examination_notes', $dentalChart->examination_notes) }}</textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
            <button type="button" class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete this dental chart and all its recorded teeth/findings?')) document.getElementById('delete-chart-form').submit();">Delete Chart</button>

            <div style="display: flex; gap: 8px;">
                <a href="{{ route('dental-charts.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Chart</button>
            </div>
        </div>
    </form>

    <form id="delete-chart-form" action="{{ route('dental-charts.destroy', $dentalChart) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

