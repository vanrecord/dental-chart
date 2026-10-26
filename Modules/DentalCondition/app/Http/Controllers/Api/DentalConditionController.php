<?php

namespace Modules\DentalCondition\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\DentalCondition\Http\Requests\DentalConditionRequest;
use Modules\DentalCondition\Models\DentalCondition;

class DentalConditionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DentalCondition::query();

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $conditions = $query->orderBy('name')->get();

        return response()->json(['data' => $conditions]);
    }

    public function store(DentalConditionRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['created_by'] = $request->user()->id;
        }

        $condition = DentalCondition::create($data);

        return response()->json([
            'message' => 'Dental condition created successfully',
            'data' => $condition,
        ], 201);
    }

    public function show(DentalCondition $dentalCondition): JsonResponse
    {
        return response()->json(['data' => $dentalCondition]);
    }

    public function update(DentalConditionRequest $request, DentalCondition $dentalCondition): JsonResponse
    {
        $dentalCondition->update($request->validated());

        return response()->json([
            'message' => 'Dental condition updated successfully',
            'data' => $dentalCondition,
        ]);
    }

    public function destroy(DentalCondition $dentalCondition): JsonResponse
    {
        $dentalCondition->delete();

        return response()->json(['message' => 'Dental condition deleted successfully']);
    }
}
