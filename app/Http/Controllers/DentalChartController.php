<?php

namespace App\Http\Controllers;

use App\Http\Requests\DentalChartRequest;
use App\Models\DentalChart;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DentalChartController extends Controller
{
    public function index(Request $request): View
    {
        $query = DentalChart::query()
            ->with(['patient'])
            ->withCount('teeth');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->integer('patient_id'));
        }

        $charts = $query->latest('chart_date')->paginate(15)->withQueryString();
        $patients = Patient::orderBy('full_name')->get(['id', 'patient_number', 'full_name']);

        return view('dental_charts.index', compact('charts', 'patients'));
    }

    public function create(Request $request): View
    {
        $selectedPatientId = $request->query('patient_id');
        $patients = Patient::orderBy('full_name')->get(['id', 'patient_number', 'full_name']);

        return view('dental_charts.create', compact('patients', 'selectedPatientId'));
    }

    public function store(DentalChartRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['created_by'] = $request->user()->id;
        }

        $chart = DentalChart::create($data);

        return redirect()->route('dental.show', $chart)
            ->with('success', 'Dental chart created. You can now record tooth findings.');
    }

    public function show(DentalChart $dentalChart): RedirectResponse
    {
        return redirect()->route('dental.show', $dentalChart);
    }

    public function edit(DentalChart $dentalChart): View
    {
        $patients = Patient::orderBy('full_name')->get(['id', 'patient_number', 'full_name']);

        return view('dental_charts.edit', compact('dentalChart', 'patients'));
    }

    public function update(DentalChartRequest $request, DentalChart $dentalChart): RedirectResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['updated_by'] = $request->user()->id;
        }

        $dentalChart->update($data);

        return redirect()->route('dental-charts.index')
            ->with('success', 'Dental chart updated successfully.');
    }

    public function destroy(DentalChart $dentalChart): RedirectResponse
    {
        $patientId = $dentalChart->patient_id;
        $dentalChart->delete();

        return redirect()->route('dental-charts.index', ['patient_id' => $patientId])
            ->with('success', 'Dental chart deleted successfully.');
    }
}
