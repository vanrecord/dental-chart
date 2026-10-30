@extends('core::layouts.app')

@section('title', 'Doctors — Hospital Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Doctors</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage hospital doctors, medical specialists, user accounts, and credentials</p>
    </div>
    <div>
        @can('hospital:doctors:create')
            <a href="{{ route('hospital.doctors.create') }}" class="btn btn-primary">+ Add Doctor</a>
        @endcan
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

<div class="card">
    <!-- Search & Filters -->
    <form method="GET" action="{{ route('hospital.doctors.index') }}" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.25rem; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, email, phone, specialization..." class="form-control" style="max-width: 300px;">

        <select name="specialization" class="form-control" style="max-width: 200px;">
            <option value="">All Specializations</option>
            @foreach($specializations as $spec)
                <option value="{{ $spec }}" {{ request('specialization') === $spec ? 'selected' : '' }}>
                    {{ $spec }}
                </option>
            @endforeach
        </select>

        <select name="doctor_status" class="form-control" style="max-width: 150px;">
            <option value="">Doctor Status</option>
            <option value="1" {{ request('doctor_status') === '1' ? 'selected' : '' }}>Active Doctor</option>
            <option value="0" {{ request('doctor_status') === '0' ? 'selected' : '' }}>Inactive Doctor</option>
        </select>

        <select name="account_status" class="form-control" style="max-width: 150px;">
            <option value="">Account Status</option>
            <option value="1" {{ request('account_status') === '1' ? 'selected' : '' }}>Active Account</option>
            <option value="0" {{ request('account_status') === '0' ? 'selected' : '' }}>Inactive Account</option>
        </select>

        <button type="submit" class="btn btn-outline">Filter</button>
        @if(request()->hasAny(['search', 'specialization', 'doctor_status', 'account_status']))
            <a href="{{ route('hospital.doctors.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 90px;">Doctor Code</th>
                    <th>Doctor Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Specialization</th>
                    <th>Qualification</th>
                    <th>License #</th>
                    <th style="text-align: right;">Fee</th>
                    <th style="text-align: center;">Doctor Status</th>
                    <th style="text-align: center;">Account Status</th>
                    <th style="width: 100px;">Created Date</th>
                    <th style="text-align: right; width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $doctor)
                    <tr>
                        <td>
                            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700;">
                                {{ $doctor->doctor_code }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                @if($doctor->photo_url)
                                    <img src="{{ $doctor->photo_url }}" alt="Photo" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1;">
                                @else
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">
                                        {{ strtoupper(substr($doctor->full_name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('hospital.doctors.show', $doctor) }}" style="font-weight: 700; color: #0f172a; text-decoration: none;">
                                        {{ $doctor->full_name }}
                                    </a>
                                    @if($doctor->user?->username)
                                        <div style="font-size: 0.75rem; color: #64748b;">
                                            &#64;{{ $doctor->user->username }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 0.8125rem; color: #334155;">{{ $doctor->email ?? '—' }}</span>
                        </td>
                        <td>
                            <span style="font-size: 0.8125rem; color: #334155;">{{ $doctor->phone ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #0f172a; font-weight: 600;">
                                {{ $doctor->specialization ?: 'General' }}
                            </span>
                        </td>
                        <td style="font-size: 0.8125rem; color: #475569;">
                            {{ $doctor->qualification ?: '—' }}
                        </td>
                        <td style="font-size: 0.8125rem; color: #475569;">
                            {{ $doctor->license_number ?: '—' }}
                        </td>
                        <td style="text-align: right; font-weight: 600; color: #0f172a;">
                            ${{ number_format($doctor->consultation_fee, 2) }}
                        </td>
                        <td style="text-align: center;">
                            @if($doctor->status)
                                <span class="badge" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">Active</span>
                            @else
                                <span class="badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($doctor->user?->is_active)
                                <span class="badge" style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe;">Active</span>
                            @else
                                <span class="badge" style="background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1;">Disabled</span>
                            @endif
                        </td>
                        <td style="font-size: 0.8125rem; color: #64748b;">
                            {{ $doctor->created_at ? $doctor->created_at->format('Y-m-d') : '—' }}
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 4px;">
                                @can('hospital:doctors:view')
                                    <a href="{{ route('hospital.doctors.show', $doctor) }}" class="btn btn-outline btn-sm">View</a>
                                @endcan
                                @can('hospital:doctors:edit')
                                    <a href="{{ route('hospital.doctors.edit', $doctor) }}" class="btn btn-outline btn-sm">Edit</a>
                                @endcan
                                @can('hospital:doctors:delete')
                                    <form method="POST" action="{{ route('hospital.doctors.destroy', $doctor) }}" style="display: inline;" class="delete-confirm-form" data-type="doctor" data-name="{{ $doctor->full_name }} ({{ $doctor->doctor_code }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-outline btn-sm btn-delete-swal" style="color: #dc2626; border-color: #fca5a5;">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            No doctor records found. Click "+ Add Doctor" to register one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($doctors->hasPages())
        <div style="margin-top: 1rem;">
            {{ $doctors->links() }}
        </div>
    @endif
</div>
@endsection

