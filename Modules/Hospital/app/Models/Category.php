<?php

namespace Modules\Hospital\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    const SERVICE = 'service';
    const PRODUCT = 'product';

    protected $fillable = [
        'hospital_id',
        'name',
        'description',
        'type',
        'is_active',
        'code',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $maxCode = static::query()
                    ->where('type', $model->type)
                    ->when($model->hospital_id, fn($q) => $q->where('hospital_id', $model->hospital_id))
                    ->selectRaw('COALESCE(MAX(CAST(code AS INTEGER)), 0) as max_code')
                    ->value('max_code');

                $model->code = (string) (((int) $maxCode) + 1);
            }

            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            // Protect code from being changed on updates
            if ($model->isDirty('code')) {
                $model->code = $model->getOriginal('code');
            }

            // Protect type from being changed on updates
            if ($model->isDirty('type')) {
                $model->type = $model->getOriginal('type');
            }

            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    public function isService(): bool
    {
        return $this->type === self::SERVICE;
    }

    public function isProduct(): bool
    {
        return $this->type === self::PRODUCT;
    }
}

