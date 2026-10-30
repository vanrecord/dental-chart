<?php

namespace Modules\Setting\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ClinicSetting extends Model
{
    protected $table = 'clinic_settings';

    protected $fillable = [
        'logo_path',
        'khmer_title',
        'khmer_title_color',
        'english_title',
        'english_title_color',
        'description',
        'description_color',
        'facebook_account',
        'facebook_page',
        'email',
        'phone',
        'address',
        'except_doctor_ids',
    ];

    protected $casts = [
        'except_doctor_ids' => 'array',
    ];

    /**
     * Singleton instance retriever with sensible default fallback.
     */
    public static function getSettings(): self
    {
        $setting = static::first();

        if (! $setting) {
            $setting = static::create([
                'khmer_title' => 'គ្លីនិកធ្មេញ អូដុនតូខែរ',
                'khmer_title_color' => '#000000',
                'english_title' => 'OdontoCare Dental Clinic',
                'english_title_color' => '#000000',
                'description' => 'Providing gentle, high quality, and modern dental treatments',
                'description_color' => '#555555',
                'facebook_account' => 'OdontoCare Dental Clinic',
                'facebook_page' => 'https://facebook.com/odontocare',
                'email' => 'info@odontocare.com',
                'phone' => '+855 23 888 999',
                'address' => '<p>No. 123, Preah Sihanouk Blvd, Khan Daun Penh, Phnom Penh, Cambodia</p>',
                'except_doctor_ids' => [],
            ]);
        }

        return $setting;
    }

    /**
     * Accessor for full logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    /**
     * Check if a specific doctor is excluded from income calculations.
     */
    public function isDoctorExcluded(int|string|null $doctorId): bool
    {
        if (! $doctorId) {
            return false;
        }

        $ids = $this->except_doctor_ids ?? [];

        return in_array((int) $doctorId, array_map('intval', (array) $ids), true);
    }

    /**
     * Get Collection of excluded User models.
     */
    public function excludedDoctors(): Collection
    {
        $ids = $this->except_doctor_ids ?? [];
        if (empty($ids)) {
            return collect();
        }

        return User::whereIn('id', $ids)->get();
    }
}

