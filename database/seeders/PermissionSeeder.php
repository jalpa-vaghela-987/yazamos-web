<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissions = [
            'create-project',
            'view-project',
            'edit-project',
            'delete-project',
            'create-investor',
            'view-investor',
            'edit-investor',
            'delete-investor',
            'create-tenant',
            'view-tenant',
            'edit-tenant',
            'delete-tenant',
            'invest-in-project',
            'approve-investments',
            'upload-business-plan',
            'approve-business-plan',
            'upload-design-plans',
            'approve-design-plans',
            'track-construction-progress',
            'create-phase',
            'edit-phase',
            'delete-phase',
            'view-phase',
            'create-milestone',
            'edit-milestone',
            'delete-milestone',
            'view-milestone',
            'view-entrepreneur',
            'edit-entrepreneur',
            'delete-entrepreneur',
            'create-entrepreneur',
            'view-investment',
            'edit-investment',
            'delete-investment',
            'view-financial-report',
            'edit-financial-report',
            'delete-financial-report',
            'view-legal-document',
            'edit-legal-document',
            'delete-legal-document',
            'view-construction-report',
            'edit-construction-report',
            'delete-construction-report',
            'view-project-update',
            'edit-project-update',
            'delete-project-update',
            'view-project-financials',
            'edit-project-financials',
            'delete-project-financials',
            'view-project-legal-documents',
            'edit-project-legal-documents',
            'delete-project-legal-documents',
            'view-project-construction-reports',
            'edit-project-construction-reports',
            'delete-project-construction-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                ['guard_name' => 'api']
            );
        }
    }
}
