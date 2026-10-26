@extends('core::layouts.app')

@section('title', 'Dental Charts')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dental Examination Charts</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">List of patient examination records and charts</p>
    </div>
    <a href="{{ route('dental-charts.create') }}" class="btn btn-primary">+ New Dental Chart</a>
</div>

<div class="card">
    <form method="GET" action="{{ route('dental-charts.index') }}" style="display: flex; gap: 8px; margin-bottom: 1rem; flex-wrap: wrap;">
        <select name="patient_id" class="form-control" style="max-width: 300px;" onchange="this.form.submit()">
            <option value="">All Patients</option>
            @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                    {{ $patient->full_name }} ({{ $patient->patient_number }})
                </option>
            @endforeach
        </select>
        @if(request('patient_id'))
            <a href="{{ route('dental-charts.index') }}" class="btn btn-outline">Clear Filter</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Dentition Type</th>
                    <th>Recorded Teeth</th>
                    <th>Notes</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($charts as $chart)
                    <tr>
                        <td><strong>{{ $chart->chart_date->format('Y-m-d') }}</strong></td>
                        <td>
                            <a href="{{ route('patients.show', $chart->patient) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                {{ $chart->patient->full_name }}
                            </a>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">#{{ $chart->patient->patient_number }}</div>
                        </td>
                        <td><span class="badge">{{ ucfirst($chart->dentition_type) }}</span></td>
                        <td>{{ $chart->teeth_count }} teeth</td>
                        <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $chart->examination_notes ?: '—' }}
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('dental.show', $chart) }}" class="btn btn-primary btn-sm">Open Chart</a>
                                <a href="{{ route('dental-charts.edit', $chart) }}" class="btn btn-outline btn-sm">Edit Info</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No dental charts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $charts->links() }}
    </div>
</div>
@endsection
