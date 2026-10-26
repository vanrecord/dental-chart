<?php

namespace Modules\ToothFinding\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ToothFinding\Http\Requests\ToothFindingRequest;
use Modules\ToothFinding\Models\ToothFinding;

class ToothFindingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ToothFinding::query()->with(['condition', 'chartTooth']);

        if ($request->filled('chart_tooth_id')) {
            $query->where('chart_tooth_id', $request->integer('chart_tooth_id'));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(ToothFindingRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['created_by'] = $request->user()->id;
        }

        $finding = ToothFinding::create($data);

        return response()->json([
            'message' => 'Tooth finding created successfully',
            'data' => $finding->load('condition', 'chartTooth'),
        ], 201);
    }

    public function show(ToothFinding $toothFinding): JsonResponse
    {
        return response()->json([
            'data' => $toothFinding->load('condition', 'chartTooth'),
        ]);
    }

    public function update(ToothFindingRequest $request, ToothFinding $toothFinding): JsonResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['updated_by'] = $request->user()->id;
        }

        $toothFinding->update($data);

        return response()->json([
            'message' => 'Tooth finding updated successfully',
            'data' => $toothFinding->fresh(['condition', 'chartTooth']),
        ]);
    }

    public function destroy(ToothFinding $toothFinding): JsonResponse
    {
        $toothFinding->delete();

        return response()->json(['message' => 'Tooth finding deleted successfully']);
    }
}
