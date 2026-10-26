<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToothFinding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chart_tooth_id',
        'dental_condition_id',
        'surface',
        'custom_color',
        'note',
        'treatment_status',
        'created_by',
        'updated_by',
    ];

    public function chartTooth(): BelongsTo
    {
        return $this->belongsTo(ChartTooth::class, 'chart_tooth_id');
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(DentalCondition::class, 'dental_condition_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

