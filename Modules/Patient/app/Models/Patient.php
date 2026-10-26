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
}
