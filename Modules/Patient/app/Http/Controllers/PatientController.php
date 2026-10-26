<?php

namespace Modules\Patient\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Patient\Http\Requests\PatientRequest;
use Modules\Patient\Models\Patient;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Patient::query()->withCount('dentalCharts');

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('patient_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patient::index', compact('patients'));
    }

    public function create(): View
    {
        return view('patient::create');
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        $patient = Patient::create($request->validated());

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient created successfully.');
    }

    public function show(Patient $patient): View
    {
        $patient->load(['dentalCharts' => fn ($q) => $q->latest('chart_date')]);

        return view('patient::show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        return view('patient::edit', compact('patient'));
    }

    public function update(PatientRequest $request, Patient $patient): RedirectResponse
    {
        $patient->update($request->validated());

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
}
