<?php

namespace Modules\DentalChart\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\DentalChart\Http\Requests\DentalChartRequest;
use Modules\DentalChart\Http\Requests\SyncChartFindingsRequest;
use Modules\DentalChart\Models\DentalChart;
use Modules\DentalChart\Services\DentalChartSyncService;

class DentalChartController extends Controller
{
    public function __construct(
        protected DentalChartSyncService $syncService
    ) {}

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
        $updatedChart = $this->syncService->sync(
            $dentalChart,
            $request->validated(),
            $request->user()?->id
        );

        return response()->json([
            'message' => 'Chart findings synchronized successfully',
            'data' => $updatedChart,
        ]);
    }
}

