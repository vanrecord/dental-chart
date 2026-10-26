@extends('core::layouts.app')

@section('title', 'Dental Chart System')

@section('content')
<div style="text-align: center; max-width: 720px; margin: 2rem auto 3rem;">
    <div style="font-size: 3rem; margin-bottom: 0.5rem;">🦷</div>
    <h1 style="font-size: 2.25rem; font-weight: 850; letter-spacing: -0.03em; color: #0f172a; margin-bottom: 0.75rem;">
        Dental Chart & Patient Management
    </h1>
    <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.6;">
        Manage patients, create dental charts, configure diagnoses and conditions, and record tooth findings on an interactive FDI dental chart.
    </p>
    <div style="display: flex; gap: 12px; justify-content: center; margin-top: 1.75rem; flex-wrap: wrap;">
        <a href="{{ url('/dental') }}" class="btn btn-primary" style="padding: 0.75rem 1.4rem; font-size: 1rem;">Open Interactive Chart</a>
        <a href="{{ route('patients.index') }}" class="btn btn-outline" style="padding: 0.75rem 1.4rem; font-size: 1rem;">Manage Patients</a>
        <a href="{{ route('dental-charts.index') }}" class="btn btn-outline" style="padding: 0.75rem 1.4rem; font-size: 1rem;">View Charts</a>
        <a href="{{ route('dental-conditions.index') }}" class="btn btn-outline" style="padding: 0.75rem 1.4rem; font-size: 1rem;">Conditions</a>
        <a href="{{ route('users.index') }}" class="btn btn-outline" style="padding: 0.75rem 1.4rem; font-size: 1rem;">Users</a>
        <a href="{{ route('roles.index') }}" class="btn btn-outline" style="padding: 0.75rem 1.4rem; font-size: 1rem;">Roles</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
    <div class="card">
        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">👥</div>
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">Patient Module</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">
            Register new patients, view history, manage contact information, and view associated dental charts.
        </p>
        <a href="{{ route('patients.index') }}" class="btn btn-outline btn-sm">Go to Patients &rarr;</a>
    </div>

    <div class="card">
        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">📋</div>
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">DentalChart Module</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">
            Track examinations, dentition types (mixed, permanent, primary), general notes, and recorded teeth.
        </p>
        <a href="{{ route('dental-charts.index') }}" class="btn btn-outline btn-sm">Go to Charts &rarr;</a>
    </div>

    <div class="card">
        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">🎨</div>
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">DentalCondition Module</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">
            Configure diagnosis categories, custom marking colors, codes (e.g. Caries, Crown, Restoration).
        </p>
        <a href="{{ route('dental-conditions.index') }}" class="btn btn-outline btn-sm">Go to Conditions &rarr;</a>
    </div>

    <div class="card">
        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">🔐</div>
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">User &amp; RBAC Module</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">
            Manage users, security roles, and permissions using module:controller:function matrix controls.
        </p>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('users.index') }}" class="btn btn-outline btn-sm">Users &rarr;</a>
            <a href="{{ route('roles.index') }}" class="btn btn-outline btn-sm">Roles &rarr;</a>
        </div>
    </div>

    <div class="card">
        <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">📐</div>
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">ToothFinding & Odontogram</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">
            Clickable tooth surfaces (top, bottom, left, right, center, whole) synchronized directly with database records.
        </p>
        <a href="{{ url('/dental') }}" class="btn btn-primary btn-sm">Open Chart &rarr;</a>
    </div>
</div>
@endsection

