@extends('core::layouts.app')

@section('title', 'Add Doctor — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Add Doctor</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Create doctor credentials, personal profile, and professional medical information</p>
    </div>
    <div>
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

<form method="POST" action="{{ route('hospital.doctors.store') }}" enctype="multipart/form-data">
    @csrf

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- SECTION 1: User Account Information -->
        <div class="card" style="border-top: 4px solid var(--primary);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                <span style="background: #eff6ff; color: #1d4ed8; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">1</span>
                <div>
                    <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0;">User Account Information</h2>
                    <span style="font-size: 0.75rem; color: #64748b;">Credentials used by the doctor to sign in to the dental chart system</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="name">Display Name <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Dr. Sokha Chan" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Username <span style="font-size: 0.75rem; color: #64748b;">(Optional, auto-generated if blank)</span></label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="e.g. dr_sokha">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address <span style="color: #dc2626;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="doctor@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password <span style="color: #dc2626;">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password <span style="color: #dc2626;">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="is_active">User Account Status</label>
                    <select id="is_active" name="is_active" class="form-control">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active (Allow Login)</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Disabled (Prevent Login)</option>
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
                    <span style="font-size: 0.75rem; color: #64748b;">Personal identity, contact number, address, and profile photo</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 220px 1fr; gap: 1.5rem; align-items: start;">
                <!-- Photo Upload & Preview -->
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 1.25rem;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: #e2e8f0; overflow: hidden; margin-bottom: 0.75rem; border: 2px solid #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center;">
                        <img id="photoPreview" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <span id="photoPlaceholder" style="font-size: 32px; color: #94a3b8;">👨‍⚕️</span>
                    </div>
                    <label class="btn btn-outline btn-sm" style="cursor: pointer; margin-bottom: 4px;">
                        Choose Photo
                        <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;" onchange="previewDoctorPhoto(event)">
                    </label>
                    <span style="font-size: 0.75rem; color: #64748b;">JPG, PNG, WebP (Max 4MB)</span>
                </div>

                <!-- Personal Info Inputs -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="First name">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Last name">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number (+855)</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+855 12 345 678">
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}" placeholder="Residential or Clinic Address, City, Country">
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
                    <span style="font-size: 0.75rem; color: #64748b;">Medical specialty, credentials, license details, and consultation charges</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="doctor_code">Doctor Code <span style="font-size: 0.75rem; color: #64748b;">(Auto-assigned if empty)</span></label>
                    <input type="text" id="doctor_code" name="doctor_code" class="form-control" value="{{ old('doctor_code', $suggestedCode) }}" placeholder="e.g. DOC-0001">
                </div>

                <div class="form-group">
                    <label class="form-label" for="specialization">Specialization</label>
                    <input type="text" list="specializationList" id="specialization" name="specialization" class="form-control" value="{{ old('specialization') }}" placeholder="e.g. Orthodontics, General Dentistry">
                    <datalist id="specializationList">
                        @foreach($specializations as $spec)
                            <option value="{{ $spec }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group">
                    <label class="form-label" for="qualification">Medical Qualification</label>
                    <input type="text" id="qualification" name="qualification" class="form-control" value="{{ old('qualification') }}" placeholder="e.g. DDS, DMD, BDS, MSc">
                </div>

                <div class="form-group">
                    <label class="form-label" for="license_number">Medical License #</label>
                    <input type="text" id="license_number" name="license_number" class="form-control" value="{{ old('license_number') }}" placeholder="e.g. DENT-LIC-98765">
                </div>

                <div class="form-group">
                    <label class="form-label" for="experience_years">Years of Experience</label>
                    <input type="number" id="experience_years" name="experience_years" class="form-control" min="0" max="70" value="{{ old('experience_years', '0') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="consultation_fee">Consultation Fee ($ USD)</label>
                    <input type="number" step="0.01" id="consultation_fee" name="consultation_fee" class="form-control" min="0" value="{{ old('consultation_fee', '0.00') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Doctor Practice Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active Doctor</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive / On Leave</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label" for="bio">Professional Bio &amp; Notes</label>
                    <textarea id="bio" name="bio" rows="3" class="form-control" placeholder="Summary of clinical background, certifications, and expertise...">{{ old('bio') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 0.5rem;">
            <a href="{{ route('hospital.doctors.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary" style="min-width: 160px;">Save Doctor</button>
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
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

