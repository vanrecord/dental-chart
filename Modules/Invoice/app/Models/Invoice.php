<?php

namespace Modules\Invoice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Patient\Models\Patient;
use Modules\Payment\Models\Payment;
use Modules\Register\Models\Reception;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'patient_id',
        'invoice_date',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function receptions(): HasMany
    {
        return $this->hasMany(Reception::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if invoice is eligible for a follow-up visit.
     */
    public function isEligibleForReuse(): bool
    {
        return ! in_array($this->status, ['cancelled', 'closed', 'refunded'], true);
    }

    /**
     * Scope for invoices eligible for follow-up reuse.
     */
    public function scopeEligibleForFollowUp($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'closed', 'refunded']);
    }

    /**
     * Recalculate paid amount, outstanding balance and invoice status based on recorded payments.
     */
    public function recalculateBalance(): self
    {
        if ($this->items()->exists()) {
            $subtotal = (float) $this->items()->sum('amount');
            $this->subtotal = $subtotal;
            $discount = (float) ($this->discount ?? 0);
            $tax = (float) ($this->tax ?? 0);
            $this->total_amount = max(0, $subtotal - $discount + $tax);
        }

        $paid = (float) $this->payments()
            ->where('status', 'completed')
            ->sum('amount');

        $this->paid_amount = $paid;
        $total = (float) $this->total_amount;
        $balance = max(0, $total - $paid);
        $this->balance_amount = $balance;

        if ($this->status !== 'cancelled' && $this->status !== 'refunded') {
            if ($total > 0 && $paid >= $total) {
                $this->status = 'paid';
            } elseif ($paid > 0) {
                $this->status = 'partial';
            } else {
                $this->status = 'unpaid';
            }
        }

        $this->save();

        return $this;
    }

    /**
     * Generate the next unique sequential invoice number.
     * e.g. INV-2026-001 or INV-2026-0001
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "INV-{$year}-";

        $latest = static::withTrashed()
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($latest && preg_match('/INV-\d+-(\d+)/', $latest->invoice_number, $matches)) {
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}

