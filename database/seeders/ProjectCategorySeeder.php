<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetType;

class ProjectCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectCategories = [
            [
                'name' => 'Lots'
            ],
            [
                'name' => 'Apartments'
            ],
            [
                'name' => 'Renovations'
            ]
        ];

        foreach ($projectCategories as $projectCategory) {
            AssetType::create($projectCategory);
        }
    }
}
