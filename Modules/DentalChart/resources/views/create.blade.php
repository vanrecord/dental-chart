@extends('core::layouts.app')

@section('title', 'Create Dental Chart')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create Dental Chart</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Initialize a new examination chart session for a patient</p>
    </div>
    <a href="{{ route('dental-charts.index') }}" class="btn btn-outline">Back to Charts</a>
</div>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form method="POST" action="{{ route('dental-charts.store') }}">
        @csrf

        <div class="form-group">
            <label for="patient_id">Select Patient *</label>
            <select name="patient_id" id="patient_id" class="form-control" required>
                <option value="">-- Choose Patient --</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatientId) == $patient->id ? 'selected' : '' }}>
                        {{ $patient->full_name }} ({{ $patient->patient_number }})
                    </option>
                @endforeach
            </select>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                Don't see the patient? <a href="{{ route('patients.create') }}" style="color: var(--primary);">Create a new patient first</a>.
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="chart_date">Chart Date *</label>
                <input type="date" id="chart_date" name="chart_date" class="form-control" value="{{ old('chart_date', date('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label for="dentition_type">Dentition Type</label>
                <select id="dentition_type" name="dentition_type" class="form-control">
                    <option value="mixed" {{ old('dentition_type') === 'mixed' ? 'selected' : '' }}>Mixed</option>
                    <option value="permanent" {{ old('dentition_type') === 'permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="primary" {{ old('dentition_type') === 'primary' ? 'selected' : '' }}>Primary / Deciduous</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="examination_notes">Examination / General Notes</label>
            <textarea id="examination_notes" name="examination_notes" class="form-control" rows="3" placeholder="Initial clinical observations...">{{ old('examination_notes') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 1.5rem;">
            <a href="{{ route('dental-charts.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Chart & Open Editor</button>
        </div>
    </form>
</div>
@endsection

