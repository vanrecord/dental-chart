<?php

namespace Modules\DentalChart\Services;

use Illuminate\Support\Facades\DB;
use Modules\DentalChart\Models\DentalChart;
use Modules\DentalCondition\Models\DentalCondition;

class DentalChartSyncService
{
    /**
     * Synchronize tooth findings and examination notes in a transaction.
     *
     * @param DentalChart $dentalChart
     * @param array $validatedData
     * @param int|null $userId
     * @return DentalChart
     */
    public function sync(DentalChart $dentalChart, array $validatedData, ?int $userId = null): DentalChart
    {
        return DB::transaction(function () use ($dentalChart, $validatedData, $userId) {
            if (array_key_exists('examination_notes', $validatedData)) {
                $dentalChart->examination_notes = $validatedData['examination_notes'];
            }
            if (!empty($validatedData['dentition_type'])) {
                $dentalChart->dentition_type = $validatedData['dentition_type'];
            }
            if ($userId) {
                $dentalChart->updated_by = $userId;
            }
            $dentalChart->save();

            // Cache conditions keyed by id and code
            $conditions = DentalCondition::all();
            $conditionByCode = $conditions->keyBy('code');

            $inputFindings = $validatedData['findings'] ?? [];

            // Group input findings by tooth number
            $groupedByTooth = [];
            foreach ($inputFindings as $f) {
                $toothNum = (int) $f['tooth'];
                $groupedByTooth[$toothNum][] = $f;
            }

            // Existing teeth in this chart
            $existingTeeth = $dentalChart->teeth()->with('findings')->get()->keyBy('tooth_number');

            // Unique set of all teeth to process
            $allToothNumbers = array_unique(array_merge(
                $existingTeeth->keys()->toArray(),
                array_keys($groupedByTooth)
            ));

            foreach ($allToothNumbers as $toothNum) {
                $toothFindings = $groupedByTooth[$toothNum] ?? [];
                $toothRecord = $existingTeeth->get($toothNum);

                // If tooth has no findings in input, delete its surface findings
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

            return $dentalChart->fresh(['patient', 'teeth.findings.condition']);
        });
    }
}
