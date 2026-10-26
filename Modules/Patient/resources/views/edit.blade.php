@extends('core::layouts.app')

@section('title', 'Edit Patient - ' . $patient->full_name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Patient: {{ $patient->full_name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Update patient details</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline">View Profile</a>
        <a href="{{ route('patients.index') }}" class="btn btn-outline">Back to Patients</a>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form method="POST" action="{{ route('patients.update', $patient) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="patient_number">Patient Number *</label>
                <input type="text" id="patient_number" name="patient_number" class="form-control" value="{{ old('patient_number', $patient->patient_number) }}" required>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="{{ old('full_name', $patient->full_name) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', optional($patient->date_of_birth)->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label for="sex">Sex</label>
                <select id="sex" name="sex" class="form-control">
                    <option value="">Select...</option>
                    <option value="male" {{ old('sex', $patient->sex) === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('sex', $patient->sex) === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('sex', $patient->sex) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}">
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $patient->address) }}</textarea>
        </div>

        <div class="form-group">
            <label for="medical_notes">Medical / Dental Notes</label>
            <textarea id="medical_notes" name="medical_notes" class="form-control" rows="3">{{ old('medical_notes', $patient->medical_notes) }}</textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
            <button type="button" class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete this patient?')) document.getElementById('delete-patient-form').submit();">Delete Patient</button>

            <div style="display: flex; gap: 8px;">
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Patient</button>
            </div>
        </div>
    </form>

    <form id="delete-patient-form" action="{{ route('patients.destroy', $patient) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
