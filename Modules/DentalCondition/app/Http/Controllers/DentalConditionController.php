<?php

namespace Modules\DentalCondition\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\DentalCondition\Http\Requests\DentalConditionRequest;
use Modules\DentalCondition\Models\DentalCondition;
use Modules\User\Traits\AuthorizesWithPermissions;

class DentalConditionController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('conditions:dental_condition:view');

        $conditions = DentalCondition::query()
            ->withCount('findings')
            ->orderBy('name')
            ->paginate(20);

        return view('dentalcondition::index', compact('conditions'));
    }

    public function create(): View
    {
        $this->checkPermission('conditions:dental_condition:create');

        return view('dentalcondition::create');
    }

    public function store(DentalConditionRequest $request): RedirectResponse
    {
        $this->checkPermission('conditions:dental_condition:create');

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
        $this->checkPermission('conditions:dental_condition:edit');

        return view('dentalcondition::edit', compact('dentalCondition'));
    }

    public function update(DentalConditionRequest $request, DentalCondition $dentalCondition): RedirectResponse
    {
        $this->checkPermission('conditions:dental_condition:update');

        $dentalCondition->update($request->validated());

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition updated successfully.');
    }

    public function destroy(DentalCondition $dentalCondition): RedirectResponse
    {
        $this->checkPermission('conditions:dental_condition:delete');

        $dentalCondition->delete();

        return redirect()->route('dental-conditions.index')
            ->with('success', 'Dental condition deleted successfully.');
    }
}
