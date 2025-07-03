<?php

namespace App\Http\Controllers\Mobile\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectHistory;
use App\Http\Resources\Mobile\ProjectValueOverTimeResource;

class DashboardController extends Controller
{
    public function index() {
        $projects = Project::with(['assetType'])->whereHas('assignedUserProjects',function ($q){
            $q->where('user_id',loginUser()->id);
        })->get();
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
}
