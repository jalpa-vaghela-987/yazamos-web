<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Phase;
use App\Models\Milestone;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\Mobile\ProjectResource;
use App\Http\Resources\Mobile\PhaseResource;
use App\Http\Resources\Mobile\MessageResource;
use App\Http\Resources\PhaseTimelineResource;
use App\Http\Resources\Mobile\MilestoneResource;
use App\Http\Resources\PhaseBudgetResource;
use App\Models\Message;
use App\Enums\PhaseType;
use App\Models\ProjectHistory;
use App\Http\Resources\Mobile\ProjectValueOverTimeResource;
use App\Http\Resources\ProjectDocumentResource;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with(['assetType', 'phases'])->where('id', $id)->first();
        $timelines = Phase::with(['children', 'phaseHistories','milestone'])->where('project_id', $id)->where('parent_id', null)->where('type', PhaseType::TIMELINE)->get();
        $milestones = Milestone::where('project_id', $id)->get();
        $phases = Phase::with(['children', 'phaseHistories'])->where('project_id', $id)->where('parent_id', null)->get();
        $messages = Message::with(['sender', 'receiver', 'phase', 'project'])
                        ->where('project_id', $project->id)
                        ->where(function ($query) {
                            $query->where('sender_id', loginUser()->id)
                                  ->orWhere('receiver_id', loginUser()->id);
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
        $projectHistories = ProjectHistory::where('project_id', $id)->orderBy('id', 'asc')->get();

        $response = [];
        $totalActualCost = 0;
        $totalExtraValues = 0;
        $totalPlannedCost = 0;

        foreach ($phases as $phase) {
            $childrens = $phase?->children;
            foreach ($childrens as $children) {
                $totalExtraValues += abs($children->planned_cost - $children->actual_cost);
            }
            $totalActualCost += $phase->actual_cost;
            $totalPlannedCost += $phase->planned_cost;
        }

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Project get successfully.',
            'data' => [
                'project' => ProjectResource::make($project),
                'project_timeline' => [
                    'timeline' => PhaseTimelineResource::collection($timelines),
                    'milestones' => MilestoneResource::collection($milestones)
                ],
                'budget_breakdown' => [
                    'phases' => PhaseBudgetResource::collection($phases),
                    'total_actual_cost' => $totalActualCost,
                    'total_planned_cost'=> $totalPlannedCost,
                    'total_extra_values' => $totalExtraValues,
                    'grand_total' =>  ($totalActualCost + $totalExtraValues)
                ],
                'value_over_time' => ProjectValueOverTimeResource::collection($projectHistories),
                'messages' => MessageResource::collection($messages)
            ]
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function documents(Request $request, string $projectId) {
        $input = $request->all();

        $phases = Phase::with(['children'])
                    ->where('project_id', $projectId)
                    ->where('parent_id', null)
                    ->orderBy('date_uploaded', 'desc')
                    ->filterRecords($input)
                    ->get();

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Documents received successfully.',
            'data' => ProjectDocumentResource::collection($phases)
        ], 201);
    }
}
