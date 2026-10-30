<?php

namespace Modules\Hospital\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doctors';

    protected $fillable = [
        'user_id',
        'hospital_id',
        'doctor_code',
        'specialization',
        'qualification',
        'license_number',
        'experience_years',
        'consultation_fee',
        'bio',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'consultation_fee' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    /**
     * Get the user account for this doctor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the personal profile through user.
     */
    public function profile(): HasOneThrough
    {
        return $this->hasOneThrough(Profile::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    /**
     * Doctor full name accessor (user display name first, fallback to profile first/last).
     */
    public function getFullNameAttribute(): string
    {
        if (! empty($this->user?->name)) {
            return $this->user->name;
        }

        if ($this->relationLoaded('user') && $this->user?->relationLoaded('profile') && $this->user?->profile) {
            return $this->user->profile->full_name;
        }

        return 'Dr. Unknown';
    }

    /**
     * Doctor email accessor.
     */
    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    /**
     * Doctor phone accessor.
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->user?->profile?->phone;
    }

    /**
     * Doctor photo URL accessor.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->user?->profile?->photo_url;
    }

    /**
     * Auto-generate unique Doctor Code (e.g. DOC-0001).
     */
    public static function generateDoctorCode(): string
    {
        $count = static::withTrashed()->count() + 1;
        do {
            $code = 'DOC-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            $exists = static::withTrashed()->where('doctor_code', $code)->exists();
            $count++;
        } while ($exists);

        return $code;
    }
}
