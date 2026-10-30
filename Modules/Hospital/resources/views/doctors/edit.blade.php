@extends('core::layouts.app')

@section('title', 'Edit Doctor — ' . $doctor->full_name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Doctor: {{ $doctor->full_name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">
            Code: <strong>{{ $doctor->doctor_code }}</strong> · Account: <strong>{{ $doctor->user?->email }}</strong>
        </p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('hospital.doctors.show', $doctor) }}" class="btn btn-outline">View Details</a>
        <a href="{{ route('hospital.doctors.index') }}" class="btn btn-outline">Back to Doctors</a>
    </div>
</div>

@if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        <strong style="display: block; margin-bottom: 4px;">Please correct the errors below:</strong>
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $user = $doctor->user;
    $profile = $user?->profile;
@endphp

<form method="POST" action="{{ route('hospital.doctors.update', $doctor) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- SECTION 1: User Account Information -->
        <div class="card" style="border-top: 4px solid var(--primary);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                <span style="background: #eff6ff; color: #1d4ed8; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">1</span>
                <div>
                    <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0;">User Account Information</h2>
                    <span style="font-size: 0.75rem; color: #64748b;">Manage authentication credentials and sign-in status (leave password blank to keep current)</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="name">Display Name <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user?->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user?->username) }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address <span style="color: #dc2626;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user?->email) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New Password <span style="font-size: 0.75rem; color: #64748b;">(Leave blank to keep current)</span></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Optional new password">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                </div>

                <div class="form-group">
                    <label class="form-label" for="is_active">User Account Status</label>
                    <select id="is_active" name="is_active" class="form-control">
                        <option value="1" {{ old('is_active', (string)($user?->is_active ?? '1')) == '1' ? 'selected' : '' }}>Active (Allow Login)</option>
                        <option value="0" {{ old('is_active', (string)($user?->is_active ?? '1')) == '0' ? 'selected' : '' }}>Disabled (Prevent Login)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Personal Profile Information -->
        <div class="card" style="border-top: 4px solid #0284c7;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                <span style="background: #e0f2fe; color: #0369a1; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">2</span>
                <div>
                    <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0;">Personal Profile Information</h2>
                    <span style="font-size: 0.75rem; color: #64748b;">Personal identity, contact details, address, and profile photo</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 220px 1fr; gap: 1.5rem; align-items: start;">
                <!-- Photo Upload & Current Photo -->
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 1.25rem;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: #e2e8f0; overflow: hidden; margin-bottom: 0.75rem; border: 2px solid #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center;">
                        @if($profile?->photo_url)
                            <img id="photoPreview" src="{{ $profile->photo_url }}" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                            <span id="photoPlaceholder" style="font-size: 32px; color: #94a3b8; display: none;">👨‍⚕️</span>
                        @else
                            <img id="photoPreview" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            <span id="photoPlaceholder" style="font-size: 32px; color: #94a3b8;">👨‍⚕️</span>
                        @endif
                    </div>
                    <label class="btn btn-outline btn-sm" style="cursor: pointer; margin-bottom: 6px;">
                        Change Photo
                        <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;" onchange="previewDoctorPhoto(event)">
                    </label>

                    @if($profile?->photo)
                        <label style="font-size: 0.75rem; color: #dc2626; display: flex; align-items: center; gap: 4px; cursor: pointer; margin-top: 4px;">
                            <input type="checkbox" name="remove_photo" value="1">
                            Remove Photo
                        </label>
                    @endif
                </div>

                <!-- Personal Info Inputs -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name', $profile?->first_name) }}" placeholder="First name">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name', $profile?->last_name) }}" placeholder="Last name">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $profile?->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $profile?->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $profile?->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', optional($profile?->date_of_birth)->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number (+855)</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $profile?->phone) }}" placeholder="+855 12 345 678">
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $profile?->address) }}" placeholder="Residential or Clinic Address, City, Country">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Doctor Professional Information -->
        <div class="card" style="border-top: 4px solid #16a34a;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                <span style="background: #dcfce7; color: #166534; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">3</span>
                <div>
                    <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0;">Doctor Professional Information</h2>
                    <span style="font-size: 0.75rem; color: #64748b;">Medical specialty, qualification credentials, license number, and fees</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="doctor_code">Doctor Code <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="doctor_code" name="doctor_code" class="form-control" value="{{ old('doctor_code', $doctor->doctor_code) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="specialization">Specialization</label>
                    <input type="text" list="specializationList" id="specialization" name="specialization" class="form-control" value="{{ old('specialization', $doctor->specialization) }}" placeholder="e.g. Orthodontics, General Dentistry">
                    <datalist id="specializationList">
                        @foreach($specializations as $spec)
                            <option value="{{ $spec }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group">
                    <label class="form-label" for="qualification">Medical Qualification</label>
                    <input type="text" id="qualification" name="qualification" class="form-control" value="{{ old('qualification', $doctor->qualification) }}" placeholder="e.g. DDS, DMD, BDS, MSc">
                </div>

                <div class="form-group">
                    <label class="form-label" for="license_number">Medical License #</label>
                    <input type="text" id="license_number" name="license_number" class="form-control" value="{{ old('license_number', $doctor->license_number) }}" placeholder="e.g. DENT-LIC-98765">
                </div>

                <div class="form-group">
                    <label class="form-label" for="experience_years">Years of Experience</label>
                    <input type="number" id="experience_years" name="experience_years" class="form-control" min="0" max="70" value="{{ old('experience_years', $doctor->experience_years) }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="consultation_fee">Consultation Fee ($ USD)</label>
                    <input type="number" step="0.01" id="consultation_fee" name="consultation_fee" class="form-control" min="0" value="{{ old('consultation_fee', $doctor->consultation_fee) }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Doctor Practice Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="1" {{ old('status', (string)(int)$doctor->status) == '1' ? 'selected' : '' }}>Active Doctor</option>
                        <option value="0" {{ old('status', (string)(int)$doctor->status) == '0' ? 'selected' : '' }}>Inactive / On Leave</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label" for="bio">Professional Bio &amp; Notes</label>
                    <textarea id="bio" name="bio" rows="3" class="form-control" placeholder="Summary of clinical background, certifications, and expertise...">{{ old('bio', $doctor->bio) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 0.5rem;">
            <a href="{{ route('hospital.doctors.show', $doctor) }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary" style="min-width: 160px;">Update Doctor</button>
        </div>

    </div>
</form>

<script>
function previewDoctorPhoto(event) {
    const input = event.target;
    const preview = document.getElementById('photoPreview');
    const placeholder = document.getElementById('photoPlaceholder');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

