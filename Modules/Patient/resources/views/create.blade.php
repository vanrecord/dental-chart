@extends('core::layouts.app')

@section('title', 'New Patient - Dental Chart')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Register New Patient</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Enter patient personal and contact details</p>
    </div>
    <a href="{{ route('patients.index') }}" class="btn btn-outline">Back to Patients</a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form method="POST" action="{{ route('patients.store') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="patient_number">Patient Number *</label>
                <input type="text" id="patient_number" name="patient_number" class="form-control" value="{{ old('patient_number', 'P-'.date('Ymd').'-'.rand(100, 999)) }}" required>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="{{ old('full_name') }}" required placeholder="e.g. John Doe">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
            </div>
            <div class="form-group">
                <label for="sex">Sex</label>
                <select id="sex" name="sex" class="form-control">
                    <option value="">Select...</option>
                    <option value="male" {{ old('sex') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('sex') === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('sex') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. +1 555-0199">
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="form-control" rows="2" placeholder="Street, City, Postal Code...">{{ old('address') }}</textarea>
        </div>

        <div class="form-group">
            <label for="medical_notes">Medical / Dental Notes</label>
            <textarea id="medical_notes" name="medical_notes" class="form-control" rows="3" placeholder="Allergies, conditions, medications...">{{ old('medical_notes') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 1.5rem;">
            <a href="{{ route('patients.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Patient</button>
        </div>
    </form>
</div>
@endsection
