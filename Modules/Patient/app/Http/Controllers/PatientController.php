<?php

namespace Modules\Patient\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Patient\Http\Requests\PatientRequest;
use Modules\Patient\Models\Patient;
use Modules\User\Traits\AuthorizesWithPermissions;

class PatientController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('patient:patients:view');

        $query = Patient::query()->withCount(['dentalCharts', 'invoices', 'receptions']);

        if ($request->filled('search')) {
            $search = mb_strtolower($request->query('search'), 'UTF-8');
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(full_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(patient_number) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"]);
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patient::index', compact('patients'));
    }

    public function create(): View
    {
        $this->checkPermission('patient:patients:create');

        return view('patient::create');
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        $this->checkPermission('patient:patients:create');

        $patient = Patient::create($request->validated());

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient created successfully.');
    }

    public function show(Patient $patient): View
    {
        $this->checkPermission('patient:patients:view');

        $patient->load([
            'dentalCharts' => fn ($q) => $q->latest('chart_date'),
            'invoices' => fn ($q) => $q->withCount('receptions')->latest('invoice_date'),
            'receptions' => fn ($q) => $q->with('invoice')->latest('reception_date'),
            'payments' => fn ($q) => $q->with('invoice')->latest('payment_date'),
        ]);

        return view('patient::show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        $this->checkPermission('patient:patients:edit');

        return view('patient::edit', compact('patient'));
    }

    public function update(PatientRequest $request, Patient $patient): RedirectResponse
    {
        $this->checkPermission('patient:patients:update');

        $patient->update($request->validated());

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->checkPermission('patient:patients:delete');

        if ($patient->invoices()->where('paid_amount', '>', 0)->exists()) {
            return redirect()->route('patients.index')
                ->with('error', "Cannot delete patient '{$patient->full_name}' because financial invoice records exist.");
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
}
