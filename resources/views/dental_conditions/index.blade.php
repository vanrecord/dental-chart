@extends('layouts.app')

@section('title', 'Dental Conditions')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dental Conditions & Legend Guide</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage the diagnoses, treatments, and colors shown in the chart editor</p>
    </div>
    <a href="{{ route('dental-conditions.create') }}" class="btn btn-primary">+ New Condition</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Color</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Usage Count</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conditions as $cond)
                    <tr>
                        <td>
                            <span class="swatch-circle" style="background: {{ $cond->default_color }};"></span>
                            <code>{{ $cond->default_color }}</code>
                        </td>
                        <td><strong>{{ $cond->name }}</strong></td>
                        <td><code>{{ $cond->code }}</code></td>
                        <td><span class="badge">{{ ucfirst($cond->category) }}</span></td>
                        <td>
                            @if($cond->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $cond->findings_count }} findings</td>
                        <td style="text-align: right;">
                            <a href="{{ route('dental-conditions.edit', $cond) }}" class="btn btn-outline btn-sm">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No conditions registered yet. Click "+ New Condition" to add one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $conditions->links() }}
    </div>
</div>
@endsection

