<?php

namespace Modules\Register\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Invoice\Models\Invoice;
use Modules\Patient\Http\Requests\PatientRequest;
use Modules\Patient\Models\Patient;
use Modules\Register\Http\Requests\RegisterVisitRequest;
use Modules\Register\Models\Reception;
use Modules\Register\Services\RegistrationService;
use Modules\User\Traits\AuthorizesWithPermissions;

class RegisterController extends Controller
{
    use AuthorizesWithPermissions;

    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    /**
     * Display the main registration page.
     */
    public function index(Request $request): View
    {
        $this->checkPermission('register:register:view');

        $patientId = $request->input('patient_id');
        $preselectedInvoiceId = $request->input('invoice_id');
        $preselectedWorkflow = $request->input('workflow');

        $preselectedPatient = null;
        if ($patientId) {
            $preselectedPatient = Patient::with(['invoices' => fn ($q) => $q->eligibleForFollowUp()->withCount('receptions')->latest()])
                ->find($patientId);
        } elseif ($preselectedInvoiceId) {
            $invoice = Invoice::find($preselectedInvoiceId);
            if ($invoice) {
                $preselectedPatient = Patient::with(['invoices' => fn ($q) => $q->eligibleForFollowUp()->withCount('receptions')->latest()])
                    ->find($invoice->patient_id);
            }
        }

        if ($preselectedInvoiceId && !$preselectedWorkflow) {
            $preselectedWorkflow = 'follow_up';
        }

        $recentReceptions = Reception::with(['patient', 'invoice'])
            ->latest('reception_date')
            ->limit(10)
            ->get();

        return view('register::index', compact('preselectedPatient', 'recentReceptions', 'preselectedInvoiceId', 'preselectedWorkflow'));
    }

    /**
     * AJAX Search for patients by ID, name, or phone.
     */
    public function searchPatients(Request $request): JsonResponse
    {
        $this->checkPermission('register:register:view');

        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $lowerQ = mb_strtolower($q, 'UTF-8');

        $patients = Patient::query()
            ->where(function ($query) use ($lowerQ) {
                $query->whereRaw('LOWER(patient_number) LIKE ?', ["%{$lowerQ}%"])
                    ->orWhereRaw('LOWER(full_name) LIKE ?', ["%{$lowerQ}%"])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$lowerQ}%"]);
            })
            ->latest()
            ->limit(15)
            ->get(['id', 'patient_number', 'full_name', 'sex', 'date_of_birth', 'phone', 'address']);

        return response()->json($patients);
    }

    /**
     * AJAX Retrieve eligible invoices for a selected patient.
     */
    public function patientInvoices(Patient $patient): JsonResponse
    {
        $this->checkPermission('register:register:view');

        $invoices = $patient->invoices()
            ->eligibleForFollowUp()
            ->withCount('receptions')
            ->latest('invoice_date')
            ->get(['id', 'invoice_number', 'invoice_date', 'total_amount', 'paid_amount', 'balance_amount', 'status']);

        return response()->json([
            'patient' => [
                'id' => $patient->id,
                'patient_number' => $patient->patient_number,
                'full_name' => $patient->full_name,
            ],
            'invoices' => $invoices,
        ]);
    }

    /**
     * AJAX quick create patient and return data for instant selection.
     */
    public function quickCreatePatient(PatientRequest $request): JsonResponse
    {
        $this->checkPermission('patient:patients:create');

        $patient = Patient::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Patient '{$patient->full_name}' was registered successfully.",
            'patient' => $patient,
        ], 201);
    }

    /**
     * Store patient visit registration (Option A: New Invoice or Option B: Follow-up).
     */
    public function store(RegisterVisitRequest $request): RedirectResponse
    {
        $this->checkPermission('register:register:create');

        $validated = $request->validated();
        $patient = Patient::findOrFail($validated['patient_id']);
        $userId = auth()->id();

        try {
            if ($validated['workflow'] === 'new_invoice') {
                $result = $this->registrationService->registerWithNewInvoice($patient, [
                    'total_amount' => $validated['total_amount'] ?? 0.00,
                    'invoice_date' => $validated['invoice_date'] ?? now()->toDateString(),
                    'invoice_notes' => $validated['notes'] ?? null,
                    'reception_date' => $validated['reception_date'] ?? now(),
                    'visit_type' => $validated['visit_type'] ?? 'initial',
                    'chief_complaint' => $validated['chief_complaint'] ?? 'Initial Consultation & Visit',
                    'reception_notes' => $validated['notes'] ?? null,
                ], $userId);

                $msg = "Visit successfully registered for {$patient->full_name}. Generated Reception #{$result['reception']->reception_number} and new Invoice #{$result['invoice']->invoice_number}.";
            } else {
                $invoice = Invoice::findOrFail($validated['invoice_id']);

                $result = $this->registrationService->registerFollowUp($patient, $invoice, [
                    'reception_date' => $validated['reception_date'] ?? now(),
                    'chief_complaint' => $validated['chief_complaint'] ?? 'Follow-up Visit',
                    'reception_notes' => $validated['notes'] ?? null,
                ], $userId);

                $msg = "Follow-up visit successfully registered for {$patient->full_name}. Generated Reception #{$result['reception']->reception_number} linked to existing Invoice #{$invoice->invoice_number}.";
            }

            $targetInvoice = $result['invoice'];

            return redirect()->route('invoices.edit', $targetInvoice)
                ->with('success', $msg);
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to register visit: ' . $e->getMessage());
        }
    }
}
