@extends('core::layouts.app')

@section('title', 'Patients - Dental Chart')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Patients</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage registered patients and their dental charts</p>
    </div>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">+ New Patient</a>
</div>

<div class="card">
    <form method="GET" action="{{ route('patients.index') }}" style="display: flex; gap: 8px; margin-bottom: 1rem;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, patient #, phone..." class="form-control" style="max-width: 360px;">
        <button type="submit" class="btn btn-outline">Search</button>
        @if(request('search'))
            <a href="{{ route('patients.index') }}" class="btn btn-outline">Clear</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Patient #</th>
                    <th>Full Name</th>
                    <th>Sex / DOB</th>
                    <th>Phone</th>
                    <th>Charts</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td><strong>{{ $patient->patient_number }}</strong></td>
                        <td>
                            <a href="{{ route('patients.show', $patient) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                {{ $patient->full_name }}
                            </a>
                        </td>
                        <td>
                            {{ ucfirst($patient->sex ?? '—') }}
                            @if($patient->date_of_birth)
                                <span style="color: var(--text-muted); font-size: 0.75rem;">({{ $patient->date_of_birth->format('Y-m-d') }})</span>
                            @endif
                        </td>
                        <td>{{ $patient->phone ?? '—' }}</td>
                        <td>
                            <span class="badge">{{ $patient->dental_charts_count }} charts</span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline btn-sm">View</a>
                                <a href="{{ route('dental-charts.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">+ Chart</a>
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline btn-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No patients found. Click "+ New Patient" to register one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $patients->links() }}
    </div>
</div>
@endsection
