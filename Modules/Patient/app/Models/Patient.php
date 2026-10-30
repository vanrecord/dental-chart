<?php

namespace Modules\Patient\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\DentalChart\Models\DentalChart;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_number',
        'full_name',
        'date_of_birth',
        'sex',
        'phone',
        'address',
        'medical_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function dentalCharts(): HasMany
    {
        return $this->hasMany(DentalChart::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\Modules\Invoice\Models\Invoice::class);
    }

    public function receptions(): HasMany
    {
        return $this->hasMany(\Modules\Register\Models\Reception::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\Modules\Payment\Models\Payment::class);
    }

    public function getTotalInvoicedAttribute(): float
    {
        return (float) $this->invoices()->sum('total_amount');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return (float) $this->invoices()->whereNotIn('status', ['cancelled', 'refunded'])->sum('balance_amount');
    }
}

