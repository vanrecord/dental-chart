@extends('layouts.app')

@section('title', 'Edit Dental Chart #' . $dentalChart->id)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Dental Chart #{{ $dentalChart->id }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Patient: {{ $dentalChart->patient->full_name }}</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('dental.show', $dentalChart) }}" class="btn btn-primary">Open Interactive Chart</a>
        <a href="{{ route('dental-charts.index') }}" class="btn btn-outline">Back to Charts</a>
    </div>
</div>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <form method="POST" action="{{ route('dental-charts.update', $dentalChart) }}">
        @csrf
        @method('PUT')

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

