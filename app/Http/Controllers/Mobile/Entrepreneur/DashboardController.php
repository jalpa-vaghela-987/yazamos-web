<?php

namespace App\Http\Controllers\Mobile\Entrepreneur;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Http\Resources\ProjectResource;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index() {

        $projectQuery = Project::with('assignedUserProjects','assetType')
            ->where('user_id', loginUser()->id)
            ->orWhere('user_id', loginUser()->created_by)
            ->orWhereHas('assignedUserProjects', function ($query){
                $query->where('user_id', loginUser()->id);
            })
            ->get();

        $projects = $projectQuery->map(function ($project) {
            $assigned = $project->assignedUserProjects->firstWhere('user_id', loginUser()->id);

            return $project->setAttribute('invitation_status', $assigned ? $assigned->invitation_status : null)
                ->setAttribute('role', $assigned ? $assigned->role : $project->user->getRoleNames()->first());
        });

        $assets = $projectQuery->map(function ($project) {
            return $project->assetType;
        })->filter()->unique('id')->values();

        $totalValueOfAssets = $projects->sum('current_property_value');
        $totalInvestment = $projects->sum('purchase_price') + $projects->sum('renovation_cost');
        $roi = ($totalValueOfAssets != 0) ? ($totalInvestment / $totalValueOfAssets) * 100 : 0;
        $valueOverTime = $projects->map(function ($project) {
            return [
                'current_property_value' => $project->current_property_value,
                'date' => $project->created_at->format('Y-m-d'),
            ];
        });


        $companyIds = $projects->map(function ($project) {
            $role =  $project->role;

            if ($role === 'admin' || $role === 'entrepreneur') {
                return $project->company_id;
            }

            return null;
        })->filter()->unique()->values();

        $companies = Company::whereIn('id',$companyIds)->get();

        if ($projects->isEmpty()) {
           if (loginUser()->hasRole(ROLE_ADMIN)){
               $companyName = loginUser()->company_name;
           }
           elseif (loginUser()->hasRole(ROLE_ENTREPRENEUR)){
                $companyName = loginUser()->creator->company_name;
            }
            $companies = Company::where('name',$companyName)->get();
        }
        $status = $companies->isNotEmpty(); // true if at least one record exists, false otherwise

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
                'company' =>$companies,
                'projectAdd' =>$status,
                'assets' =>$assets,
            ]
        ], 200);
    }
}
