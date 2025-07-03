<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $ids = User::where('created_by', loginUser()->id)->pluck('id');
        $projects = Project::with(['assetType'])->whereIn('user_id', $ids)->orWhere('user_id',loginUser()->id)->get();
        $totalValueOfAssets = $projects->sum('current_property_value');
        $totalInvestment = $projects->sum('purchase_price') + $projects->sum('renovation_cost');
        $roi = ($totalValueOfAssets != 0) ? ($totalInvestment / $totalValueOfAssets) * 100 : 0;
        $valueOverTime = $projects->map(function ($project) {
            return [
                'current_property_value' => $project->current_property_value,
                'date' => $project->created_at->format('Y-m-d'),
            ];
        });

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
                'projects' => ProjectResource::collection($projects)
            ]
        ], 200);
    }
    public function projectFinancialChart()
    {
        $userIds = \App\Models\User::where('created_by', loginUser()->id)->pluck('id');

        $projects = \App\Models\Project::select(
            'id',
            'name',
            'estimated_budget',
            'purchase_price',
            'renovation_cost',
            'current_property_value',
            'calculated_value'
        )
            ->whereIn('user_id', $userIds)
            ->get();

        return response()->json([
            'projects' => $projects
        ]);
    }
    // In PhaseController or ProjectController

    public function getPhasesByProject($projectId)
    {
        $project = Project::with('phases')->findOrFail($projectId);

        return response()->json($project->phases);
    }
}
