@extends('core::layouts.app')

@section('title', 'Patients - Dental Chart')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Patients</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage registered patients and their dental charts</p>
    </div>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">+ Create Patient</a>
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
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Invoices</th>
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
                        <td>{{ ucfirst($patient->sex ?? '—') }}</td>
                        <td>{{ $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '—' }}</td>
                        <td>{{ $patient->phone ?? '—' }}</td>
                        <td style="color: var(--text-muted); font-size: 0.8125rem;">
                            {{ $patient->created_at ? $patient->created_at->format('Y-m-d') : '—' }}
                        </td>
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #334155;">{{ $patient->invoices_count }} inv</span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                @can('patient:patients:view')
                                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline btn-sm">View</a>
                                @endcan
                                @can('patient:patients:edit')
                                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline btn-sm">Edit</a>
                                @endcan
                                @can('patient:patients:delete')
                                    <form method="POST" action="{{ route('patients.destroy', $patient) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete patient \'{{ $patient->full_name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #fca5a5;">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No patients found. Click "+ Create Patient" to register one.
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
