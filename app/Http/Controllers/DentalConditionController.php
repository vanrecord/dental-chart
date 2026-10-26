<?php

namespace App\Http\Controllers;

use App\Http\Requests\DentalConditionRequest;
use App\Models\DentalCondition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DentalConditionController extends Controller
{
    public function index(Request $request): View
    {
        $conditions = DentalCondition::query()
            ->withCount('findings')
            ->orderBy('name')
            ->paginate(20);

        return view('dental_conditions.index', compact('conditions'));
    }

    public function create(): View
    {
        return view('dental_conditions.create');
    }

    public function store(DentalConditionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->user()) {
            $data['created_by'] = $request->user()->id;
        }

        DentalCondition::create($data);

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition created successfully.');
    }

    public function edit(DentalCondition $dentalCondition): View
    {
        return view('dental_conditions.edit', compact('dentalCondition'));
    }

    public function update(DentalConditionRequest $request, DentalCondition $dentalCondition): RedirectResponse
    {
        $dentalCondition->update($request->validated());

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition updated successfully.');
    }

    public function destroy(DentalCondition $dentalCondition): RedirectResponse
    {
        $dentalCondition->delete();

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition deleted successfully.');
    }
}

