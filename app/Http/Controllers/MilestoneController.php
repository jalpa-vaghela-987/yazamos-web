<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Http\Resources\MilestoneResource;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index(Request $request)
    {

        $input = $request->all();
        $query = Milestone::with('project');


        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        $milestones = $query->queryCrud($input);
        if ($milestones->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No milestones found.',
            ]);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Milestones retrieved successfully.',
            'data' => MilestoneResource::collection($milestones)
        ]);
    }

    public function store(StoreMilestoneRequest $request)
    {

        $validated = $request->validated();
        $milestone = Milestone::create($validated);
        if (!$milestone) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Failed to create milestone.',
            ]);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Milestone created successfully.',
            'data' => new MilestoneResource($milestone)
        ]);
    }

    public function show(string $id)
    {
        $milestone = Milestone::with('project')->find($id);

        if (!$milestone) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Milestone not found.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Milestone retrieved successfully.',
            'data' => new MilestoneResource($milestone)
        ]);
    }

    public function update(UpdateMilestoneRequest $request, string $id)
    {

        $milestone = Milestone::find($id);

        if (!$milestone) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Milestone not found.',
            ]);
        }

        $milestone->update($request->validated());

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Milestone updated successfully.',
            'data' => new MilestoneResource($milestone)
        ]);
    }

    public function destroy(string $id)
    {
        $milestone = Milestone::find($id);

        if (!$milestone) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Milestone not found.',
            ]);
        }

        $milestone->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Milestone deleted successfully.',
        ]);
    }

    public function getStageColors()
    {
        $stages = Milestone::getStagesWithColors();
        
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Stage colors retrieved successfully.',
            'data' => $stages
        ]);
    }
}
