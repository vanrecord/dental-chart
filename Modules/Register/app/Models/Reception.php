<?php

namespace Modules\Register\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Invoice\Models\Invoice;
use Modules\Patient\Models\Patient;

class Reception extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reception_number',
        'patient_id',
        'invoice_id',
        'reception_date',
        'visit_type',
        'chief_complaint',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'reception_date' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate the next unique sequential reception number.
     * e.g. REC-001, REC-002, REC-003
     */
    public static function generateReceptionNumber(): string
    {
        $latest = static::withTrashed()
            ->where('reception_number', 'like', 'REC-%')
            ->orderByDesc('id')
            ->first();

        if ($latest && preg_match('/REC-(\d+)/', $latest->reception_number, $matches)) {
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return 'REC-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}

