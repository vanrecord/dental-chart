<?php

namespace Modules\Patient\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Patient\Http\Requests\PatientRequest;
use Modules\Patient\Models\Patient;

class PatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Patient::query()->withCount('dentalCharts');

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('patient_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json($patients);
    }

    public function store(PatientRequest $request): JsonResponse
    {
        $patient = Patient::create($request->validated());

        return response()->json([
            'message' => 'Patient created successfully',
            'data' => $patient,
        ], 201);
    }

    public function show(Patient $patient): JsonResponse
    {
        $patient->load(['dentalCharts' => fn ($q) => $q->latest('chart_date')]);

        return response()->json(['data' => $patient]);
    }

    public function update(PatientRequest $request, Patient $patient): JsonResponse
    {
        $patient->update($request->validated());

        return response()->json([
            'message' => 'Patient updated successfully',
            'data' => $patient,
        ]);
    }

    public function destroy(Patient $patient): JsonResponse
    {
        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }
}
