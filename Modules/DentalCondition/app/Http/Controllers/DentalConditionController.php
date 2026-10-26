<?php

namespace Modules\DentalCondition\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\DentalCondition\Http\Requests\DentalConditionRequest;
use Modules\DentalCondition\Models\DentalCondition;

class DentalConditionController extends Controller
{
    public function index(Request $request): View
    {
        $conditions = DentalCondition::query()
            ->withCount('findings')
            ->orderBy('name')
            ->paginate(20);

        return view('dentalcondition::index', compact('conditions'));
    }

    public function create(): View
    {
        return view('dentalcondition::create');
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
        return view('dentalcondition::edit', compact('dentalCondition'));
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
