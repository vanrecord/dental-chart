<?php

namespace Modules\DentalChart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ToothFinding\Models\ToothFinding;

class ChartTooth extends Model
{
    use HasFactory;

    protected $table = 'chart_teeth';

    protected $fillable = [
        'dental_chart_id',
        'tooth_number',
        'tooth_type',
        'tooth_status',
        'general_notes',
    ];

    protected $casts = [
        'tooth_number' => 'integer',
    ];

    public function dentalChart(): BelongsTo
    {
        return $this->belongsTo(DentalChart::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(ToothFinding::class, 'chart_tooth_id');
    }
}
