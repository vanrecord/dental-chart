<?php

namespace Database\Seeders;

use App\Models\DentalCondition;
use Illuminate\Database\Seeder;

class DentalConditionSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            [
                'name' => 'Caries / decay',
                'code' => 'caries',
                'description' => 'Active caries lesion / dental decay requiring treatment',
                'default_color' => '#d83c43',
                'category' => 'finding',
                'is_active' => true,
            ],
            [
                'name' => 'Existing restoration',
                'code' => 'restoration',
                'description' => 'Composite, amalgam or glass ionomer filling present',
                'default_color' => '#2f66cf',
                'category' => 'treatment',
                'is_active' => true,
            ],
            [
                'name' => 'Treatment planned',
                'code' => 'planned',
                'description' => 'Procedure planned for upcoming visit',
                'default_color' => '#e59b26',
                'category' => 'planned',
                'is_active' => true,
            ],
            [
                'name' => 'Completed treatment',
                'code' => 'completed',
                'description' => 'Successfully finished procedure',
                'default_color' => '#319b62',
                'category' => 'treatment',
                'is_active' => true,
            ],
            [
                'name' => 'Missing tooth',
                'code' => 'missing',
                'description' => 'Tooth congenitally absent or extracted',
                'default_color' => '#707782',
                'category' => 'finding',
                'is_active' => true,
            ],
            [
                'name' => 'Crown',
                'code' => 'crown',
                'description' => 'Full coverage crown restoration',
                'default_color' => '#8b5bc7',
                'category' => 'treatment',
                'is_active' => true,
            ],
            [
                'name' => 'Fracture',
                'code' => 'fracture',
                'description' => 'Tooth fracture or enamel crack',
                'default_color' => '#8a5a38',
                'category' => 'finding',
                'is_active' => true,
            ],
            [
                'name' => 'Sealant',
                'code' => 'sealant',
                'description' => 'Pit and fissure preventive sealant',
                'default_color' => '#169d9a',
                'category' => 'treatment',
                'is_active' => true,
            ],
        ];

        foreach ($conditions as $cond) {
            DentalCondition::updateOrCreate(['code' => $cond['code']], $cond);
        }
    }
}

