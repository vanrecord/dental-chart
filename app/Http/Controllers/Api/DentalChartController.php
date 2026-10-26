<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DentalChartRequest;
use App\Http\Requests\SyncChartFindingsRequest;
use App\Models\ChartTooth;
use App\Models\DentalChart;
use App\Models\DentalCondition;
use App\Models\ToothFinding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DentalChartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DentalChart::query()
            ->with(['patient:id,patient_number,full_name'])
            ->withCount('teeth');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->integer('patient_id'));
        }

        $charts = $query->latest('chart_date')->paginate($request->integer('per_page', 15));

        return response()->json($charts);
    }

    public function store(DentalChartRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['created_by'] = $request->user()->id;
        }

        $chart = DentalChart::create($data);

        return response()->json([
            'message' => 'Dental chart created successfully',
            'data' => $chart->load('patient'),
        ], 201);
    }

    public function show(DentalChart $dentalChart): JsonResponse
    {
        $dentalChart->load([
            'patient',
            'teeth.findings.condition',
            'creator:id,name',
            'updater:id,name',
        ]);

        return response()->json(['data' => $dentalChart]);
    }

    public function update(DentalChartRequest $request, DentalChart $dentalChart): JsonResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['updated_by'] = $request->user()->id;
        }

        $dentalChart->update($data);

        return response()->json([
            'message' => 'Dental chart updated successfully',
            'data' => $dentalChart->fresh('patient'),
        ]);
    }

    public function destroy(DentalChart $dentalChart): JsonResponse
    {
        $dentalChart->delete();

        return response()->json(['message' => 'Dental chart deleted successfully']);
    }

    /**
     * Synchronize findings from the interactive dental chart
     */
    public function syncFindings(SyncChartFindingsRequest $request, DentalChart $dentalChart): JsonResponse
    {
        $validated = $request->validated();
        $userId = $request->user()?->id;

        DB::transaction(function () use ($dentalChart, $validated, $userId) {
            if (array_key_exists('examination_notes', $validated)) {
                $dentalChart->examination_notes = $validated['examination_notes'];
            }
            if (!empty($validated['dentition_type'])) {
                $dentalChart->dentition_type = $validated['dentition_type'];
            }
            if ($userId) {
                $dentalChart->updated_by = $userId;
            }
            $dentalChart->save();

            // Cache conditions keyed by id and code
            $conditions = DentalCondition::all();
            $conditionByCode = $conditions->keyBy('code');

            $inputFindings = $validated['findings'] ?? [];

            // Group input findings by tooth number
            $groupedByTooth = [];
            foreach ($inputFindings as $f) {
                $toothNum = (int) $f['tooth'];
                $groupedByTooth[$toothNum][] = $f;
            }

            // Existing teeth in this chart
            $existingTeeth = $dentalChart->teeth()->with('findings')->get()->keyBy('tooth_number');

            // Set of teeth to process
            $allToothNumbers = array_unique(array_merge(
                $existingTeeth->keys()->toArray(),
                array_keys($groupedByTooth)
            ));

            foreach ($allToothNumbers as $toothNum) {
                $toothFindings = $groupedByTooth[$toothNum] ?? [];
                $toothRecord = $existingTeeth->get($toothNum);

                // If tooth has no findings and no special status, we can clean up if desired
                if (empty($toothFindings)) {
                    if ($toothRecord) {
                        $toothRecord->findings()->delete();
                    }
                    continue;
                }

                // Determine tooth type based on FDI numbering
                // Quadrants 1-4: permanent (11-48)
                // Quadrants 5-8: primary (51-85)
                $toothType = $toothNum >= 51 && $toothNum <= 85 ? 'primary' : 'permanent';

                if (!$toothRecord) {
                    $toothRecord = $dentalChart->teeth()->create([
                        'tooth_number' => $toothNum,
                        'tooth_type' => $toothType,
                        'tooth_status' => 'present',
                    ]);
                }

                $existingSurfaceFindings = $toothRecord->findings->keyBy('surface');
                $inputSurfaces = [];

                foreach ($toothFindings as $f) {
                    $surface = $f['surface'];
                    $inputSurfaces[] = $surface;

                    // Resolve condition id
                    $conditionId = $f['condition_id'] ?? null;
                    if (!$conditionId && !empty($f['condition_code'])) {
                        $conditionId = $conditionByCode->get($f['condition_code'])?->id;
                    }

                    $findingData = [
                        'dental_condition_id' => $conditionId,
                        'custom_color' => $f['color'] ?? null,
                        'note' => $f['note'] ?? null,
                        'treatment_status' => $f['status'] ?? 'observed',
                    ];

                    if ($existingSurfaceFinding = $existingSurfaceFindings->get($surface)) {
                        if ($userId) {
                            $findingData['updated_by'] = $userId;
                        }
                        $existingSurfaceFinding->update($findingData);
                    } else {
                        if ($userId) {
                            $findingData['created_by'] = $userId;
                        }
                        $toothRecord->findings()->create(array_merge($findingData, [
                            'surface' => $surface,
                        ]));
                    }
                }

                // Delete surface findings no longer present in input
                foreach ($existingSurfaceFindings as $surface => $existingFinding) {
                    if (!in_array($surface, $inputSurfaces)) {
                        $existingFinding->delete();
                    }
                }
            }
        });

        $dentalChart->load(['teeth.findings.condition']);

        return response()->json([
            'message' => 'Chart findings synchronized successfully',
            'data' => $dentalChart,
        ]);
    }
}

