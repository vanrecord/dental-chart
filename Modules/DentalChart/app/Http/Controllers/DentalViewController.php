<?php

namespace Modules\DentalChart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\DentalChart\Models\DentalChart;
use Modules\DentalCondition\Models\DentalCondition;
use Modules\Patient\Models\Patient;
use Modules\User\Traits\AuthorizesWithPermissions;

class DentalViewController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('odontogram:dental_chart:view');

        $chartId = $request->query('chart_id');
        $activeChart = null;

        if ($chartId) {
            $activeChart = DentalChart::with(['patient', 'teeth.findings.condition'])->find($chartId);
        }

        $conditions = DentalCondition::where('is_active', true)->orderBy('name')->get();
        $patients = Patient::with(['dentalCharts' => fn ($q) => $q->latest('chart_date')])
            ->orderBy('full_name')
            ->get();

        return view('dentalchart::dental', [
            'activeChart' => $activeChart,
            'conditions' => $conditions,
            'patients' => $patients,
        ]);
    }

    public function show(DentalChart $dentalChart): View
    {
        $this->checkPermission('odontogram:dental_chart:view');

        $dentalChart->load(['patient', 'teeth.findings.condition']);
        $conditions = DentalCondition::where('is_active', true)->orderBy('name')->get();
        $patients = Patient::with(['dentalCharts' => fn ($q) => $q->latest('chart_date')])
            ->orderBy('full_name')
            ->get();

        return view('dentalchart::dental', [
            'activeChart' => $dentalChart,
            'conditions' => $conditions,
            'patients' => $patients,
        ]);
    }
}

