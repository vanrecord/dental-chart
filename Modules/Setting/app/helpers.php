<?php

use Modules\Setting\Models\ClinicSetting;
use Illuminate\Support\Facades\Schema;

if (! function_exists('clinic_settings')) {
    /**
     * Retrieve the global clinic settings model instance.
     */
    function clinic_settings(): ?ClinicSetting
    {
        try {
            if (Schema::hasTable('clinic_settings')) {
                return ClinicSetting::getSettings();
            }
        } catch (\Throwable $e) {
            // Gracefully handle situations before database migration
        }

        return null;
    }
}

