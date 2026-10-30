<?php

namespace Modules\Invoice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Hospital\Models\Service;
use Modules\Invoice\Http\Requests\InvoiceRequest;
use Modules\Invoice\Models\Invoice;
use Modules\Invoice\Models\InvoiceItem;
use Modules\Patient\Models\Patient;
use Modules\User\Traits\AuthorizesWithPermissions;

class InvoiceController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('invoice:invoices:view');

        $query = Invoice::with(['patient', 'receptions', 'payments', 'items'])
            ->withCount(['receptions', 'items']);

        // Search by invoice number, patient number, or patient name
        if ($search = $request->input('search')) {
            $lowerSearch = mb_strtolower($search, 'UTF-8');
            $query->where(function ($q) use ($lowerSearch) {
                $q->whereRaw('LOWER(invoice_number) LIKE ?', ["%{$lowerSearch}%"])
                  ->orWhereHas('patient', function ($pq) use ($lowerSearch) {
                      $pq->whereRaw('LOWER(full_name) LIKE ?', ["%{$lowerSearch}%"])
                         ->orWhereRaw('LOWER(patient_number) LIKE ?', ["%{$lowerSearch}%"]);
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Date range filters
        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('invoice_date', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->whereDate('invoice_date', '<=', $toDate);
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();

        return view('invoice::index', compact('invoices'));
    }

    public function create(Request $request): View
    {
        $this->checkPermission('invoice:invoices:create');

        $preselectedPatient = null;
        if ($patientId = $request->input('patient_id')) {
            $preselectedPatient = Patient::with('receptions')->find($patientId);
        }

        $patients = Patient::with(['receptions' => fn($q) => $q->latest('reception_date')])
            ->orderBy('full_name')
            ->get(['id', 'patient_number', 'full_name', 'phone']);

        $services = Service::where('type', Service::SERVICE)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        $products = Service::where('type', Service::PRODUCT)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('invoice::create', compact('patients', 'preselectedPatient', 'services', 'products'));
    }

    public function store(InvoiceRequest $request): RedirectResponse
    {
        $this->checkPermission('invoice:invoices:create');

        $validated = $request->validated();

        $invoice = DB::transaction(function () use ($validated) {
            $subtotal = 0.00;
            $itemsData = [];

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemInput) {
                    $service = Service::find($itemInput['service_id']);
                    $qty = max(1, (int) ($itemInput['quantity'] ?? 1));
                    $unitPrice = isset($itemInput['unit_price']) ? (float) $itemInput['unit_price'] : (float) ($service?->price ?? 0);
                    $lineDiscount = (float) ($itemInput['discount'] ?? 0);
                    $lineTax = (float) ($itemInput['tax'] ?? 0);
                    $lineAmount = max(0, ($qty * $unitPrice) - $lineDiscount + $lineTax);
                    $subtotal += $lineAmount;

                    $itemsData[] = [
                        'type' => $itemInput['type'] ?? ($service?->type ?? 'service'),
                        'service_id' => $service?->id,
                        'reception_id' => !empty($itemInput['reception_id']) ? $itemInput['reception_id'] : null,
                        'item_name' => $service ? "{$service->name} ({$service->code})" : 'Item',
                        'operate_date' => !empty($itemInput['operate_date']) ? substr($itemInput['operate_date'], 0, 10) : now()->toDateString(),
                        'tooth_numbers' => $itemInput['tooth_numbers'] ?? null,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'discount' => $lineDiscount,
                        'tax' => $lineTax,
                        'amount' => $lineAmount,
                        'note' => $itemInput['note'] ?? null,
                    ];
                }
            } else {
                $subtotal = (float) ($validated['total_amount'] ?? 0);
            }

            $discount = (float) ($validated['discount'] ?? 0);
            $tax = (float) ($validated['tax'] ?? 0);
            $grandTotal = max(0, $subtotal - $discount + $tax);

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'patient_id' => $validated['patient_id'],
                'invoice_date' => $validated['invoice_date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $grandTotal,
                'paid_amount' => 0.00,
                'balance_amount' => $grandTotal,
                'status' => $validated['status'] ?? 'unpaid',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($itemsData as $data) {
                $invoice->items()->create($data);
            }

            return $invoice;
        });

        if ($request->boolean('redirect_to_dental_chart')) {
            $pId = $validated['patient_id'] ?? $invoice->patient_id;
            $invoice->load(['patient.dentalCharts' => fn($q) => $q->latest('chart_date')->latest('id')]);
            $chart = $invoice->patient?->dentalCharts?->first();
            if ($chart) {
                return redirect()->route('dental.show', ['dentalChart' => $chart, 'invoice_id' => $invoice->id])
                    ->with('success', "Invoice created and items saved. Dental chart opened.");
            }

            return redirect()->route('dental-charts.create', ['patient_id' => $pId, 'invoice_id' => $invoice->id])
                ->with('success', "Invoice created and items saved. You can now create the dental chart.");
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice '{$invoice->invoice_number}' created successfully.");
    }

    public function show(Invoice $invoice): View
    {
        $this->checkPermission('invoice:invoices:view');

        $invoice->load([
            'patient',
            'items.service',
            'items.reception',
            'receptions' => fn ($q) => $q->latest('reception_date'),
            'payments' => fn ($q) => $q->latest('payment_date'),
            'creator',
        ]);

        return view('invoice::show', compact('invoice'));
    }

    public function print(Invoice $invoice): View
    {
        $this->checkPermission('invoice:invoices:view');

        $invoice->load([
            'patient',
            'items.service',
            'items.reception',
            'receptions' => fn ($q) => $q->latest('reception_date'),
            'payments' => fn ($q) => $q->latest('payment_date'),
            'creator',
        ]);

        $clinicSetting = clinic_settings();

        return view('invoice::print', compact('invoice', 'clinicSetting'));
    }

    public function edit(Invoice $invoice): View
    {
        $this->checkPermission('invoice:invoices:edit');

        $invoice->load([
            'patient.receptions',
            'patient.dentalCharts' => fn($q) => $q->latest('chart_date')->latest('id'),
            'items.service',
        ]);
        
        $patients = Patient::with([
            'receptions' => fn($q) => $q->latest('reception_date'),
            'dentalCharts' => fn($q) => $q->latest('chart_date')->latest('id'),
        ])
            ->orderBy('full_name')
            ->get(['id', 'patient_number', 'full_name']);

        $services = Service::where('type', Service::SERVICE)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        $products = Service::where('type', Service::PRODUCT)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('invoice::edit', compact('invoice', 'patients', 'services', 'products'));
    }

    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->checkPermission('invoice:invoices:update');

        $validated = $request->validated();

        DB::transaction(function () use ($invoice, $validated) {
            $subtotal = 0.00;
            $itemsData = [];

            if (isset($validated['items'])) {
                foreach ($validated['items'] as $itemInput) {
                    $service = Service::find($itemInput['service_id']);
                    $qty = max(1, (int) ($itemInput['quantity'] ?? 1));
                    $unitPrice = isset($itemInput['unit_price']) ? (float) $itemInput['unit_price'] : (float) ($service?->price ?? 0);
                    $lineDiscount = (float) ($itemInput['discount'] ?? 0);
                    $lineTax = (float) ($itemInput['tax'] ?? 0);
                    $lineAmount = max(0, ($qty * $unitPrice) - $lineDiscount + $lineTax);
                    $subtotal += $lineAmount;

                    $itemsData[] = [
                        'id' => $itemInput['id'] ?? null,
                        'type' => $itemInput['type'] ?? ($service?->type ?? 'service'),
                        'service_id' => $service?->id,
                        'reception_id' => !empty($itemInput['reception_id']) ? $itemInput['reception_id'] : null,
                        'item_name' => $service ? "{$service->name} ({$service->code})" : 'Item',
                        'operate_date' => !empty($itemInput['operate_date']) ? substr($itemInput['operate_date'], 0, 10) : now()->toDateString(),
                        'tooth_numbers' => $itemInput['tooth_numbers'] ?? null,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'discount' => $lineDiscount,
                        'tax' => $lineTax,
                        'amount' => $lineAmount,
                        'note' => $itemInput['note'] ?? null,
                    ];
                }
            } else {
                $subtotal = (float) ($validated['total_amount'] ?? $invoice->total_amount);
            }

            $discount = (float) ($validated['discount'] ?? $invoice->discount ?? 0);
            $tax = (float) ($validated['tax'] ?? $invoice->tax ?? 0);
            $grandTotal = max(0, $subtotal - $discount + $tax);

            $invoice->update([
                'patient_id' => $validated['patient_id'],
                'invoice_date' => $validated['invoice_date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $grandTotal,
                'status' => $validated['status'] ?? $invoice->status,
                'notes' => $validated['notes'] ?? null,
            ]);

            if (isset($validated['items'])) {
                $existingItemIds = [];
                foreach ($itemsData as $data) {
                    if (!empty($data['id'])) {
                        $item = $invoice->items()->find($data['id']);
                        if ($item) {
                            $item->update($data);
                            $existingItemIds[] = $item->id;
                            continue;
                        }
                    }
                    $newItem = $invoice->items()->create($data);
                    $existingItemIds[] = $newItem->id;
                }

                // Delete items removed in UI
                $invoice->items()->whereNotIn('id', $existingItemIds)->delete();
            }

            $invoice->recalculateBalance();
        });

        if ($request->boolean('redirect_to_dental_chart')) {
            $pId = $validated['patient_id'] ?? $invoice->patient_id;
            $invoice->load(['patient.dentalCharts' => fn($q) => $q->latest('chart_date')->latest('id')]);
            $chart = $invoice->patient?->dentalCharts?->first();
            if ($chart) {
                return redirect()->route('dental.show', ['dentalChart' => $chart, 'invoice_id' => $invoice->id])
                    ->with('success', "Invoice items saved. Dental chart opened.");
            }

            return redirect()->route('dental-charts.create', ['patient_id' => $pId, 'invoice_id' => $invoice->id])
                ->with('success', "Invoice items saved. You can now create the dental chart.");
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice '{$invoice->invoice_number}' updated successfully.");
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->checkPermission('invoice:invoices:delete');

        if ($invoice->payments()->where('status', 'completed')->exists()) {
            return back()->with('error', "Cannot delete invoice '{$invoice->invoice_number}' because completed payment records exist.");
        }

        $invoiceNumber = $invoice->invoice_number;
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', "Invoice '{$invoiceNumber}' deleted successfully.");
    }

    /**
     * AJAX endpoint to get receptions/visits for a patient
     */
    public function patientVisits(Patient $patient): JsonResponse
    {
        $this->checkPermission('invoice:invoices:view');

        $visits = $patient->receptions()
            ->latest('reception_date')
            ->get(['id', 'reception_number', 'reception_date', 'visit_type', 'chief_complaint']);

        return response()->json([
            'success' => true,
            'visits' => $visits,
        ]);
    }
}
