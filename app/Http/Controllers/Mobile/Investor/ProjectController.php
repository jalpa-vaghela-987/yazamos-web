<?php

namespace App\Http\Controllers\Mobile\Investor;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhaseBudgetResource;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Phase;
use App\Models\Milestone;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\Mobile\ProjectResource;
use App\Http\Resources\Mobile\PhaseResource;
use App\Http\Resources\Mobile\MilestoneResource;
use App\Http\Resources\Mobile\MessageResource;
use App\Models\Message;
use App\Models\ProjectHistory;
use App\Http\Resources\Mobile\ProjectValueOverTimeResource;

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
        try {
            $project = Project::with(['assetType', 'phases'])
                ->where('id', $id)
                ->first();

            if (!$project) {
                return response()->json([
                    'status' => 'error',
                    'status_code' => 404,
                    'message' => 'Project not found or you do not have access.',
                ], 404);
            }

            $milestones = Milestone::where('project_id', $project->id)->get();

            $messages = Message::with(['sender', 'receiver', 'phase', 'project'])
                ->where('project_id', $project->id)
                ->where(function ($query) {
                    $query->where('sender_id', loginUser()->id)
                        ->orWhere('receiver_id', loginUser()->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $projectHistories = ProjectHistory::where('project_id', $project->id)
                ->orderBy('id', 'asc')
                ->get();
            $phases = Phase::with(['children', 'phaseHistories'])->where('project_id', $project->id)->where('parent_id', null)->get();

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Project retrieved successfully.',
                'data' => [
                    'project' => ProjectResource::make($project),
                    'phases' => PhaseBudgetResource::collection($phases),
                    'project_milestones' => MilestoneResource::collection($milestones),
                    'messages' => MessageResource::collection($messages),
                    'value_over_time' => ProjectValueOverTimeResource::collection($projectHistories),
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(), // Remove this in production or log it instead
            ], 500);
        }
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
}
