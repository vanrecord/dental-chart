<?php

namespace Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hospital\Models\Service;
use Modules\Register\Models\Reception;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_SERVICE = 'service';
    const TYPE_PRODUCT = 'product';

    protected $fillable = [
        'invoice_id',
        'type',
        'service_id',
        'reception_id',
        'item_name',
        'operate_date',
        'tooth_numbers',
        'quantity',
        'unit_price',
        'discount',
        'tax',
        'amount',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'operate_date' => 'date:Y-m-d',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function reception(): BelongsTo
    {
        return $this->belongsTo(Reception::class, 'reception_id');
    }

    public function isService(): bool
    {
        return $this->type === self::TYPE_SERVICE;
    }

    public function isProduct(): bool
    {
        return $this->type === self::TYPE_PRODUCT;
    }
}

