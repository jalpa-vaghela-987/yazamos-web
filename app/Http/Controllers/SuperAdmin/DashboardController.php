<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $projects = Project::with(['assetType'])->get();
        $totalValueOfAssets = $projects->sum('current_property_value');
        $totalInvestment = $projects->sum('purchase_price') + $projects->sum('renovation_cost');
        $roi = ($totalValueOfAssets != 0) ? ($totalInvestment / $totalValueOfAssets) * 100 : 0;
        $valueOverTime = $projects->map(function ($project) {
            return [
                'current_property_value' => $project->current_property_value,
                'date' => $project->created_at->format('Y-m-d'),
            ];
        });
        $adminCount = User::whereHas('roles', function ($q) {
            $q->where('name', ROLE_ADMIN);
        })->count();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Project get successfully.',
            'data' => [
                'total_value_of_assets' => $totalValueOfAssets,
                'total_investment' =>  $totalInvestment,
                'roi' => round($roi, 2),
                'value_over_time' => $valueOverTime,
                'number_of_assets' => $projects->count(),
                'projects' => ProjectResource::collection($projects),
                'adminCount' => $adminCount
            ]
        ], 200);
    }
}
