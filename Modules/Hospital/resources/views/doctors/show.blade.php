@extends('core::layouts.app')

@section('title', 'Doctor Profile — ' . $doctor->full_name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $doctor->full_name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">
            Doctor Code: <strong>{{ $doctor->doctor_code }}</strong> · 
            Specialization: <strong>{{ $doctor->specialization ?: 'General' }}</strong>
        </p>
    </div>
    <div style="display: flex; gap: 8px;">
        @can('hospital:doctors:edit')
            <a href="{{ route('hospital.doctors.edit', $doctor) }}" class="btn btn-primary">Edit Doctor</a>
        @endcan
        <a href="{{ route('hospital.doctors.index') }}" class="btn btn-outline">Back to Doctors</a>
    </div>
</div>

@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        {{ session('success') }}
    </div>
@endif

@php
    $user = $doctor->user;
    $profile = $user?->profile;
@endphp

<div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">

    <!-- Left Column: Doctor Profile Card & Account Credentials -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <!-- Profile Card -->
        <div class="card" style="text-align: center; padding: 2rem 1.5rem;">
            <div style="width: 110px; height: 110px; border-radius: 50%; background: #e2e8f0; overflow: hidden; margin: 0 auto 1rem; border: 3px solid #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center;">
                @if($doctor->photo_url)
                    <img src="{{ $doctor->photo_url }}" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <span style="font-size: 40px; color: #64748b;">👨‍⚕️</span>
                @endif
            </div>

            <h2 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">{{ $doctor->full_name }}</h2>
            <div style="font-size: 0.875rem; color: #0284c7; font-weight: 600; margin-bottom: 8px;">
                {{ $doctor->specialization ?: 'Dental Practitioner' }}
            </div>

            <div style="display: flex; justify-content: center; gap: 6px; flex-wrap: wrap; margin-bottom: 1.25rem;">
                @if($doctor->status)
                    <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">Active Doctor</span>
                @else
                    <span class="badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">Inactive</span>
                @endif

                @if($user?->is_active)
                    <span class="badge" style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe;">Account Active</span>
                @else
                    <span class="badge" style="background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1;">Account Disabled</span>
                @endif
            </div>

            <div style="border-top: 1px solid #f1f5f9; padding-top: 1rem; text-align: left; font-size: 0.8125rem; display: flex; flex-direction: column; gap: 8px;">
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Doctor ID Code</span>
                    <strong style="color: #0369a1;">{{ $doctor->doctor_code }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Phone Number</span>
                    <span>{{ $profile?->phone ?: 'Not provided' }}</span>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Email Address</span>
                    <span>{{ $user?->email ?: 'Not provided' }}</span>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Standard Consultation Fee</span>
                    <strong style="color: #15803d; font-size: 1rem;">${{ number_format($doctor->consultation_fee, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- User Login Account Box -->
        <div class="card" style="border-top: 4px solid var(--primary);">
            <h3 style="font-size: 0.9375rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">Sign-in Account Details</h3>
            <div style="font-size: 0.8125rem; display: flex; flex-direction: column; gap: 8px;">
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Username</span>
                    <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #334155;">{{ $user?->username ?: '—' }}</code>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Sign-in Email</span>
                    <strong>{{ $user?->email }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Assigned Roles</span>
                    @forelse($user?->roles ?? [] as $role)
                        <span class="badge" style="background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; margin-right: 4px;">{{ $role->label }}</span>
                    @empty
                        <span style="color: #94a3b8;">No roles assigned</span>
                    @endforelse
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Account Registration Date</span>
                    <span>{{ $user?->created_at ? $user->created_at->format('M d, Y H:i') : '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Personal & Professional Details -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Professional Information Card -->
        <div class="card" style="border-top: 4px solid #16a34a;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                Doctor Professional &amp; Medical Details
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; font-size: 0.875rem;">
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Medical Specialization</span>
                    <strong style="color: #0f172a;">{{ $doctor->specialization ?: 'General Practitioner' }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Qualifications &amp; Degrees</span>
                    <strong>{{ $doctor->qualification ?: '—' }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Medical License Number</span>
                    <code>{{ $doctor->license_number ?: '—' }}</code>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Years of Experience</span>
                    <strong>{{ $doctor->experience_years }} {{ Str::plural('year', $doctor->experience_years) }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Consultation Fee</span>
                    <strong style="color: #15803d;">${{ number_format($doctor->consultation_fee, 2) }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Doctor Practice Status</span>
                    <span>{{ $doctor->status ? 'Active Doctor' : 'Inactive / On Leave' }}</span>
                </div>
                <div style="grid-column: 1 / -1; margin-top: 0.5rem;">
                    <span style="color: #64748b; display: block; font-size: 0.75rem; margin-bottom: 4px;">Professional Bio</span>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; font-size: 0.8125rem; color: #334155; line-height: 1.5;">
                        {{ $doctor->bio ?: 'No professional summary recorded.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Profile Card -->
        <div class="card" style="border-top: 4px solid #0284c7;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                Personal Profile Information
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; font-size: 0.875rem;">
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">First Name</span>
                    <strong>{{ $profile?->first_name ?: '—' }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Last Name</span>
                    <strong>{{ $profile?->last_name ?: '—' }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Gender</span>
                    <span style="text-transform: capitalize;">{{ $profile?->gender ?: '—' }}</span>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Date of Birth</span>
                    <span>{{ $profile?->date_of_birth ? $profile->date_of_birth->format('M d, Y') : '—' }}</span>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Phone Number</span>
                    <span>{{ $profile?->phone ?: '—' }}</span>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.75rem;">Residential Address</span>
                    <span>{{ $profile?->address ?: '—' }}</span>
                </div>
            </div>
        </div>

        <!-- Action / Management Panel -->
        @can('hospital:doctors:delete')
            <div class="card" style="border-left: 4px solid #dc2626; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #991b1b; display: block; font-size: 0.9375rem;">Doctor Account Removal</strong>
                    <span style="font-size: 0.8125rem; color: #64748b;">Soft-deleting this doctor preserves medical history and patient records.</span>
                </div>
                <form method="POST" action="{{ route('hospital.doctors.destroy', $doctor) }}" class="delete-confirm-form" data-type="doctor" data-name="{{ $doctor->full_name }} ({{ $doctor->doctor_code }})">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-outline btn-delete-swal" style="color: #dc2626; border-color: #fca5a5;">
                        Delete Doctor
                    </button>
                </form>
            </div>
        @endcan

    </div>
</div>
@endsection

