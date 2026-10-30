<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Invoice\Models\Invoice;
use Modules\Payment\Http\Requests\PaymentRequest;
use Modules\Payment\Models\Payment;
use Modules\User\Traits\AuthorizesWithPermissions;

class PaymentController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('payment:payments:view');

        $query = Payment::with(['invoice.patient', 'patient']);

        // Search by payment number, transaction reference, invoice number, or patient
        if ($search = $request->input('search')) {
            $lowerSearch = mb_strtolower($search, 'UTF-8');
            $query->where(function ($q) use ($lowerSearch) {
                $q->whereRaw('LOWER(payment_number) LIKE ?', ["%{$lowerSearch}%"])
                  ->orWhereRaw('LOWER(transaction_reference) LIKE ?', ["%{$lowerSearch}%"])
                  ->orWhereHas('invoice', fn ($iq) => $iq->whereRaw('LOWER(invoice_number) LIKE ?', ["%{$lowerSearch}%"]))
                  ->orWhereHas('patient', function ($pq) use ($lowerSearch) {
                      $pq->whereRaw('LOWER(full_name) LIKE ?', ["%{$lowerSearch}%"])
                         ->orWhereRaw('LOWER(patient_number) LIKE ?', ["%{$lowerSearch}%"]);
                  });
            });
        }

        // Method filter
        if ($method = $request->input('payment_method')) {
            $query->where('payment_method', $method);
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Date range
        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('payment_date', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->whereDate('payment_date', '<=', $toDate);
        }

        $payments = $query->latest('payment_date')->paginate(15)->withQueryString();

        return view('payment::index', compact('payments'));
    }

    public function create(Request $request): View
    {
        $this->checkPermission('payment:payments:create');

        $selectedInvoice = null;
        if ($invoiceId = $request->input('invoice_id')) {
            $selectedInvoice = Invoice::with('patient')->find($invoiceId);
        }

        $eligibleInvoices = Invoice::with('patient')
            ->whereNotIn('status', ['paid', 'cancelled', 'refunded'])
            ->latest('invoice_date')
            ->get();

        return view('payment::create', compact('eligibleInvoices', 'selectedInvoice'));
    }

    public function store(PaymentRequest $request): RedirectResponse
    {
        $this->checkPermission('payment:payments:create');

        $validated = $request->validated();
        $invoice = Invoice::findOrFail($validated['invoice_id']);

        $amount = (float) $validated['amount'];

        if ($amount > (float) $invoice->balance_amount && $invoice->balance_amount > 0) {
            return back()
                ->withInput()
                ->with('error', "Payment amount (\${$amount}) cannot exceed the invoice outstanding balance (\${$invoice->balance_amount}).");
        }

        $payment = DB::transaction(function () use ($invoice, $validated, $amount) {
            $payment = Payment::create([
                'payment_number' => Payment::generatePaymentNumber(),
                'invoice_id' => $invoice->id,
                'patient_id' => $invoice->patient_id,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'currency' => 'USD',
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'status' => $validated['status'] ?? 'completed',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $invoice->recalculateBalance();

            return $payment;
        });

        return redirect()->route('payments.show', $payment)
            ->with('success', "Payment '{$payment->payment_number}' of \${$payment->amount} recorded successfully.");
    }

    public function show(Payment $payment): View
    {
        $this->checkPermission('payment:payments:view');

        $payment->load(['invoice.patient', 'patient', 'creator']);

        return view('payment::show', compact('payment'));
    }

    public function print(Payment $payment): View
    {
        $this->checkPermission('payment:payments:view');

        $payment->load(['invoice.patient', 'invoice.payments', 'patient', 'creator']);

        $clinicSetting = clinic_settings();

        return view('payment::print', compact('payment', 'clinicSetting'));
    }

    public function edit(Payment $payment): View
    {
        $this->checkPermission('payment:payments:edit');

        $payment->load(['invoice.patient']);

        return view('payment::edit', compact('payment'));
    }

    public function update(PaymentRequest $request, Payment $payment): RedirectResponse
    {
        $this->checkPermission('payment:payments:update');

        $validated = $request->validated();

        DB::transaction(function () use ($payment, $validated) {
            $payment->update([
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'status' => $validated['status'] ?? $payment->status,
                'notes' => $validated['notes'] ?? null,
            ]);

            $payment->invoice->recalculateBalance();
        });

        return redirect()->route('payments.show', $payment)
            ->with('success', "Payment '{$payment->payment_number}' updated successfully.");
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $this->checkPermission('payment:payments:delete');

        $invoice = $payment->invoice;
        $paymentNumber = $payment->payment_number;

        DB::transaction(function () use ($payment, $invoice) {
            $payment->delete();
            $invoice->recalculateBalance();
        });

        return redirect()->route('payments.index')
            ->with('success', "Payment '{$paymentNumber}' deleted and invoice balance recalculated.");
    }
}
