<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Setting\Http\Requests\ClinicSettingRequest;
use Modules\Setting\Models\ClinicSetting;
use Modules\User\Traits\AuthorizesWithPermissions;

class GeneralSettingController extends Controller
{
    use AuthorizesWithPermissions;

    /**
     * Display the clinic settings form with Clinic Info and Except Income Doctor tabs.
     */
    public function edit(Request $request): View
    {
        $this->checkPermission('settings:general:view');

        $setting = ClinicSetting::getSettings();

        // Load all doctors and staff members for the exclusion configuration
        $doctors = User::with('roles')
            ->orderBy('name')
            ->get();

        $activeTab = $request->query('tab', 'clinic_info');

        return view('setting::general', compact('setting', 'doctors', 'activeTab'));
    }

    /**
     * Update clinic information and doctor exclusion configuration.
     */
    public function update(ClinicSettingRequest $request): RedirectResponse
    {
        $this->checkPermission('settings:general:edit');

        $validated = $request->validated();
        $activeTab = $validated['active_tab'] ?? 'clinic_info';

        DB::transaction(function () use ($request, $validated) {
            $setting = ClinicSetting::getSettings();

            // 1. Handle logo deletion request
            if ($request->boolean('remove_logo')) {
                if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                    Storage::disk('public')->delete($setting->logo_path);
                }
                $setting->logo_path = null;
            }

            // 2. Handle new logo upload
            if ($request->hasFile('logo')) {
                if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                    Storage::disk('public')->delete($setting->logo_path);
                }
                $path = $request->file('logo')->store('clinic', 'public');
                $setting->logo_path = $path;
            }

            // 3. Update Clinic Info fields
            $setting->khmer_title = $validated['khmer_title'] ?? null;
            $setting->khmer_title_color = $validated['khmer_title_color'] ?? '#000000';
            $setting->english_title = $validated['english_title'] ?? null;
            $setting->english_title_color = $validated['english_title_color'] ?? '#000000';
            $setting->description = $validated['description'] ?? null;
            $setting->description_color = $validated['description_color'] ?? '#555555';
            $setting->facebook_account = $validated['facebook_account'] ?? null;
            $setting->facebook_page = $validated['facebook_page'] ?? null;
            $setting->email = $validated['email'] ?? null;
            $setting->phone = $validated['phone'] ?? null;
            $setting->address = $this->sanitizeAddress($validated['address'] ?? null);

            // 4. Update Excluded Doctor IDs
            $rawDocIds = $validated['except_doctor_ids'] ?? [];
            $setting->except_doctor_ids = array_values(array_unique(array_map('intval', (array) $rawDocIds)));

            $setting->save();
        });

        return redirect()->route('settings.general', ['tab' => $activeTab])
            ->with('success', 'Clinic settings have been updated successfully.');
    }

    /**
     * Remove the current clinic logo.
     */
    public function destroyLogo(Request $request): JsonResponse|RedirectResponse
    {
        $this->checkPermission('settings:general:edit');

        $setting = ClinicSetting::getSettings();

        if ($setting->logo_path) {
            if (Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $setting->logo_path = null;
            $setting->save();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Clinic logo removed successfully.',
            ]);
        }

        return redirect()->route('settings.general', ['tab' => 'clinic_info'])
            ->with('success', 'Clinic logo removed successfully.');
    }

    /**
     * Sanitize HTML content with a strict allowlist of formatting tags.
     */
    protected function sanitizeAddress(?string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        // Remove scripts and styles including their inner contents
        $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $clean);

        // Allow only safe text formatting tags
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><span><div><h1><h2><h3><h4><h5><h6><blockquote>';
        $clean = strip_tags($clean, $allowedTags);

        // Strip any inline javascript events (e.g. onclick, onerror, onload)
        $clean = preg_replace('/on[a-zA-Z]+\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', $clean);

        // Strip javascript: pseudo-protocol in links
        $clean = preg_replace('/href\s*=\s*["\']\s*javascript:[^"\']*["\']/i', 'href="#"', $clean);

        return trim($clean);
    }
}
