<?php

namespace Modules\Hospital\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Hospital\Http\Requests\DoctorRequest;
use Modules\Hospital\Models\Doctor;
use Modules\Hospital\Models\Profile;
use Modules\User\Models\Role;
use Modules\User\Traits\AuthorizesWithPermissions;

class DoctorController extends Controller
{
    use AuthorizesWithPermissions;

    /**
     * Standard list of medical and dental specializations.
     */
    protected array $defaultSpecializations = [
        'General Dentistry',
        'Orthodontics',
        'Endodontics',
        'Periodontics',
        'Prosthodontics',
        'Pediatric Dentistry',
        'Oral & Maxillofacial Surgery',
        'Cosmetic Dentistry',
        'Implantology',
    ];

    /**
     * Display a listing of doctors.
     */
    public function index(Request $request): View
    {
        $this->checkPermission('hospital:doctors:view');

        $query = Doctor::with(['user.profile', 'user.roles']);

        // Search by doctor name, email, phone, doctor code, specialization
        if ($search = $request->input('search')) {
            $lowerSearch = mb_strtolower(trim($search), 'UTF-8');
            $query->where(function ($q) use ($lowerSearch) {
                $q->whereRaw('LOWER(doctor_code) LIKE ?', ["%{$lowerSearch}%"])
                  ->orWhereRaw('LOWER(specialization) LIKE ?', ["%{$lowerSearch}%"])
                  ->orWhereRaw('LOWER(qualification) LIKE ?', ["%{$lowerSearch}%"])
                  ->orWhereRaw('LOWER(license_number) LIKE ?', ["%{$lowerSearch}%"])
                  ->orWhereHas('user', function ($uq) use ($lowerSearch) {
                      $uq->whereRaw('LOWER(name) LIKE ?', ["%{$lowerSearch}%"])
                         ->orWhereRaw('LOWER(email) LIKE ?', ["%{$lowerSearch}%"])
                         ->orWhereRaw('LOWER(username) LIKE ?', ["%{$lowerSearch}%"])
                         ->orWhereHas('profile', function ($pq) use ($lowerSearch) {
                             $pq->whereRaw('LOWER(first_name) LIKE ?', ["%{$lowerSearch}%"])
                                ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$lowerSearch}%"])
                                ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$lowerSearch}%"]);
                         });
                  });
            });
        }

        // Specialization filter
        if ($specialization = $request->input('specialization')) {
            $query->where('specialization', $specialization);
        }

        // Doctor status filter (active / inactive)
        if ($request->filled('doctor_status')) {
            $query->where('status', (bool) $request->input('doctor_status'));
        }

        // User account status filter (active / inactive)
        if ($request->filled('account_status')) {
            $accountActive = (bool) $request->input('account_status');
            $query->whereHas('user', function ($uq) use ($accountActive) {
                $uq->where('is_active', $accountActive);
            });
        }

        $doctors = $query->latest('id')->paginate(15)->withQueryString();

        // Get all unique specializations for filter dropdown
        $savedSpecs = Doctor::whereNotNull('specialization')
            ->where('specialization', '!=', '')
            ->distinct()
            ->pluck('specialization')
            ->toArray();
        $specializations = array_values(array_unique(array_merge($this->defaultSpecializations, $savedSpecs)));
        sort($specializations);

        return view('hospital::doctors.index', compact('doctors', 'specializations'));
    }

    /**
     * Show the form for creating a new doctor and user account.
     */
    public function create(): View
    {
        $this->checkPermission('hospital:doctors:create');

        $suggestedCode = Doctor::generateDoctorCode();
        $specializations = $this->defaultSpecializations;

        return view('hospital::doctors.create', compact('suggestedCode', 'specializations'));
    }

    /**
     * Store a newly created doctor, profile, and user in storage in a single transaction.
     */
    public function store(DoctorRequest $request): RedirectResponse
    {
        $this->checkPermission('hospital:doctors:create');

        $validated = $request->validated();

        $doctor = DB::transaction(function () use ($request, $validated) {
            // 1. Generate username if not provided
            $username = ! empty($validated['username'])
                ? $validated['username']
                : preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $validated['email'])[0]) . rand(10, 99);

            // Ensure username uniqueness
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter++;
            }

            // 2. Create User account
            $user = User::create([
                'name' => $validated['name'],
                'username' => $username,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => $request->boolean('is_active', true),
            ]);

            // 3. Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('doctors/photos', 'public');
            }

            // 4. Create Profile
            $user->profile()->create([
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'photo' => $photoPath,
            ]);

            // 5. Create Doctor
            $doctorCode = ! empty($validated['doctor_code'])
                ? $validated['doctor_code']
                : Doctor::generateDoctorCode();

            $doctor = $user->doctor()->create([
                'hospital_id' => $validated['hospital_id'] ?? null,
                'doctor_code' => $doctorCode,
                'specialization' => $validated['specialization'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'experience_years' => (int) ($validated['experience_years'] ?? 0),
                'consultation_fee' => (float) ($validated['consultation_fee'] ?? 0.00),
                'bio' => $validated['bio'] ?? null,
                'status' => $request->boolean('status', true),
            ]);

            // 6. Assign Doctor / Dentist Role to the user
            $doctorRole = Role::whereIn('name', ['doctor', 'dentist'])->first();
            if ($doctorRole) {
                $user->assignRole($doctorRole);
            }

            return $doctor;
        });

        return redirect()->route('hospital.doctors.index')
            ->with('success', "Doctor '{$doctor->full_name}' ({$doctor->doctor_code}) and user account created successfully.");
    }

    /**
     * Display the specified doctor.
     */
    public function show(Doctor $doctor): View
    {
        $this->checkPermission('hospital:doctors:view');

        $doctor->load(['user.profile', 'user.roles']);

        return view('hospital::doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified doctor.
     */
    public function edit(Doctor $doctor): View
    {
        $this->checkPermission('hospital:doctors:edit');

        $doctor->load(['user.profile']);
        $specializations = $this->defaultSpecializations;

        return view('hospital::doctors.edit', compact('doctor', 'specializations'));
    }

    /**
     * Update the specified doctor, profile, and user in storage in a single transaction.
     */
    public function update(DoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->checkPermission('hospital:doctors:update');

        $validated = $request->validated();

        DB::transaction(function () use ($request, $doctor, $validated) {
            $user = $doctor->user;
            $profile = $user->profile ?: new Profile(['user_id' => $user->id]);

            // 1. Update User Information
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => $request->boolean('is_active', true),
            ];

            if (! empty($validated['username'])) {
                $userData['username'] = $validated['username'];
            }

            // Only update password if a new one is provided
            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            // 2. Handle Profile Photo upload / removal
            if ($request->hasFile('photo')) {
                if ($profile->photo && Storage::disk('public')->exists($profile->photo)) {
                    Storage::disk('public')->delete($profile->photo);
                }
                $profile->photo = $request->file('photo')->store('doctors/photos', 'public');
            } elseif ($request->boolean('remove_photo')) {
                if ($profile->photo && Storage::disk('public')->exists($profile->photo)) {
                    Storage::disk('public')->delete($profile->photo);
                }
                $profile->photo = null;
            }

            // 3. Update Profile Information
            $profile->fill([
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
            $profile->save();

            // 4. Update Doctor Professional Information
            $doctorData = [
                'specialization' => $validated['specialization'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'experience_years' => (int) ($validated['experience_years'] ?? 0),
                'consultation_fee' => (float) ($validated['consultation_fee'] ?? 0.00),
                'bio' => $validated['bio'] ?? null,
                'status' => $request->boolean('status', true),
            ];

            if (! empty($validated['doctor_code'])) {
                $doctorData['doctor_code'] = $validated['doctor_code'];
            }

            $doctor->update($doctorData);
        });

        return redirect()->route('hospital.doctors.show', $doctor)
            ->with('success', "Doctor '{$doctor->fresh()->full_name}' updated successfully.");
    }

    /**
     * Remove the specified doctor from storage.
     */
    public function destroy(Doctor $doctor): RedirectResponse
    {
        $this->checkPermission('hospital:doctors:delete');

        $doctorName = $doctor->full_name;
        $doctorCode = $doctor->doctor_code;

        // Soft delete the doctor record (preserves related user and profile history)
        $doctor->delete();

        return redirect()->route('hospital.doctors.index')
            ->with('success', "Doctor '{$doctorName}' ({$doctorCode}) was successfully removed.");
    }
}

