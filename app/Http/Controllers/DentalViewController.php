<?php

namespace App\Http\Controllers;

use App\Models\DentalChart;
use App\Models\DentalCondition;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DentalViewController extends Controller
{
    public function index(Request $request): View
    {
        $chartId = $request->query('chart_id');
        $activeChart = null;

        if ($chartId) {
            $activeChart = DentalChart::with(['patient', 'teeth.findings.condition'])->find($chartId);
        }

        $conditions = DentalCondition::where('is_active', true)->orderBy('name')->get();
        $patients = Patient::with(['dentalCharts' => fn ($q) => $q->latest('chart_date')])
            ->orderBy('full_name')
            ->get();

        return view('dental', [
            'activeChart' => $activeChart,
            'conditions' => $conditions,
            'patients' => $patients,
        ]);
    }

    public function show(DentalChart $dentalChart): View
    {
        $dentalChart->load(['patient', 'teeth.findings.condition']);
        $conditions = DentalCondition::where('is_active', true)->orderBy('name')->get();
        $patients = Patient::with(['dentalCharts' => fn ($q) => $q->latest('chart_date')])
            ->orderBy('full_name')
            ->get();

        return view('dental', [
            'activeChart' => $dentalChart,
            'conditions' => $conditions,
            'patients' => $patients,
        ]);
    }
}

