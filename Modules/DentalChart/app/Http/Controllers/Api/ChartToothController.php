<?php

namespace Modules\DentalChart\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\DentalChart\Http\Requests\ChartToothRequest;
use Modules\DentalChart\Models\ChartTooth;

class ChartToothController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ChartTooth::query()->with('findings.condition');

        if ($request->filled('dental_chart_id')) {
            $query->where('dental_chart_id', $request->integer('dental_chart_id'));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(ChartToothRequest $request): JsonResponse
    {
        $tooth = ChartTooth::create($request->validated());

        return response()->json([
            'message' => 'Chart tooth created successfully',
            'data' => $tooth->load('findings'),
        ], 201);
    }

    public function show(ChartTooth $chartTooth): JsonResponse
    {
        return response()->json([
            'data' => $chartTooth->load('findings.condition', 'dentalChart'),
        ]);
    }

    public function update(ChartToothRequest $request, ChartTooth $chartTooth): JsonResponse
    {
        $chartTooth->update($request->validated());

        return response()->json([
            'message' => 'Chart tooth updated successfully',
            'data' => $chartTooth->load('findings'),
        ]);
    }

    public function destroy(ChartTooth $chartTooth): JsonResponse
    {
        $chartTooth->delete();

        return response()->json(['message' => 'Chart tooth deleted successfully']);
    }
}
