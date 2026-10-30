<?php

namespace Modules\Payment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Invoice\Models\Invoice;
use Modules\Patient\Models\Patient;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'patient_id',
        'payment_date',
        'amount',
        'currency',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate the next unique sequential payment number.
     * e.g. PAY-2026-001
     */
    public static function generatePaymentNumber(): string
    {
        $year = date('Y');
        $prefix = "PAY-{$year}-";

        $latest = static::withTrashed()
            ->where('payment_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($latest && preg_match('/PAY-\d+-(\d+)/', $latest->payment_number, $matches)) {
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}

