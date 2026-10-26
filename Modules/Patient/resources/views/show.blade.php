@extends('core::layouts.app')

@section('title', 'Patient Details - ' . $patient->full_name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $patient->full_name }}</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Patient # {{ $patient->patient_number }}</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('dental-charts.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary">+ New Dental Chart</a>
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline">Edit Info</a>
        <a href="{{ route('patients.index') }}" class="btn btn-outline">Back to Patients</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 340px 1fr; gap: 1.5rem; align-items: start;">
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Patient Information</h2>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Date of Birth</span>
                <strong>{{ optional($patient->date_of_birth)->format('M d, Y') ?? '—' }}</strong>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Sex</span>
                <strong>{{ ucfirst($patient->sex ?? '—') }}</strong>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Phone</span>
                <strong>{{ $patient->phone ?? '—' }}</strong>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Address</span>
                <div>{{ $patient->address ?? '—' }}</div>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">Medical / Dental Notes</span>
                <div style="background: #f8fafc; padding: 0.5rem; border-radius: 6px; font-size: 0.8125rem; color: #475569;">
                    {{ $patient->medical_notes ?? 'No notes recorded.' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700;">Dental Charts ({{ $patient->dentalCharts->count() }})</h2>
            <a href="{{ route('dental-charts.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">+ Create Chart</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Chart Date</th>
                        <th>Dentition</th>
                        <th>Examination Notes</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patient->dentalCharts as $chart)
                        <tr>
                            <td><strong>{{ $chart->chart_date->format('M d, Y') }}</strong></td>
                            <td><span class="badge">{{ ucfirst($chart->dentition_type) }}</span></td>
                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $chart->examination_notes ?: '—' }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ route('dental.show', $chart) }}" class="btn btn-primary btn-sm">Open Interactive Chart</a>
                                    <a href="{{ route('dental-charts.edit', $chart) }}" class="btn btn-outline btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No dental charts recorded for this patient yet. Click "+ Create Chart" to start.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

