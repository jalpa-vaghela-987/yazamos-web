<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhaseRequest;
use App\Http\Requests\UpdatePhaseRequest;
use App\Http\Resources\PhaseResource;
use App\Models\Phase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Enums\PhaseType;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PhaseTimeLineRequest;
use App\Http\Requests\UpdatePhaseTimeLineRequest;
use App\Http\Requests\PhaseBudgetRequest;
use App\Http\Requests\UpdatePhaseBudgetRequest;
use App\Models\PhaseHistory;
use App\Http\Resources\PhaseCategoryResource;

class PhaseController extends Controller
{
    public function index(Request $request)
    {

        $input = $request->all();

        $query = Phase::with('project');

        // Filter by project_id if provided
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $phases = $query->queryCrud($input);
        if ($phases->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No phases found for this project.'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Phases retrieved successfully.',
            'data' => PhaseResource::collection($phases)
        ], 200);
    }


    public function store(StorePhaseRequest $request)
    {
        //        $this->authorize('create', Phase::class);

        $validated = $request->validated();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('expenses', 'public');
            $validated['file'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['date_uploaded'] = now()->toDateString();
        }

        $phase = Phase::create($validated);
        if (!$phase) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Failed to create phase.'
            ], 500);
        }
        //create phase history
        $validated['phase_id'] = $phase->id;
        PhaseHistory::create($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Phase created successfully.',
            'data' => new PhaseResource($phase)
        ], 201);
    }

    public function show(string $id)
    {
        $phase = Phase::with('project')->find($id);

        if (!$phase) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Phase not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Phase retrieved successfully.',
            'data' => new PhaseResource($phase)
        ], 200);
    }

    public function update(UpdatePhaseRequest $request, string $id)
    {
        //        $this->authorize('update', Phase::class);

        $validated = $request->validated();

        if ($request->hasFile('file')) {
            // Delete the old photo if it exists
            if ($request->file) {
                Storage::delete($request->file);
            }

            $file = $request->file('file');
            $path = $file->store('expenses', 'public');
            $validated['file'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['date_uploaded'] = now()->toDateString();
        }



        $phase = Phase::find($id);
        //create phase history
        $validated['phase_id'] = $phase->id;
        PhaseHistory::create($validated);

        if (!$phase) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Phase not found.',
            ], 404);
        }

        $phase->update($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Phase updated successfully.',
            'data' => new PhaseResource($phase)
        ], 200);
    }

    public function destroy(string $id)
    {
        //        $this->authorize('delete', Phase::class);

        $phase = Phase::find($id);

        if (!$phase) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Phase not found.',
            ], 404);
        }

        $phase->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Phase deleted successfully.',
        ], 200);
    }

    public function summary($project_id)
    {
        $phases = Phase::where('project_id', $project_id)
            ->get();

        $response = [];
        $total_actual_cost = 0;
        $total_extra_values = 0;

        foreach ($phases as $phase) {
            $formatted_extras = [];

            $extras = is_array($phase->extra) ? $phase->extra : [];

            foreach ($extras as $item) {
                if (isset($item['key']) && isset($item['value'])) {
                    $formatted_extras[] = [$item['key'] => $item['value']];

                    if (is_numeric($item['value'])) {
                        $total_extra_values += $item['value'];
                    }
                }
            }

            $response[] = [
                'title' => $phase->title,
                'actual_cost' => $phase->actual_cost,
                'extra' => $formatted_extras
            ];

            $total_actual_cost += $phase->actual_cost;
        }

        return response()->json([
            'phases' => $response,
            'total_actual_cost' => $total_actual_cost,
            'total_extra_values' => $total_extra_values
        ]);
    }

    public function timeline(PhaseTimeLineRequest $request)
    {
        $validated = $request->validated();

        $validated['type'] = PhaseType::TIMELINE;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('expenses', 'public');
            $validated['file'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['date_uploaded'] = now()->toDateString();
        }

        $timeline = Phase::create($validated);
        $validated['phase_id'] = $timeline->id;
        PhaseHistory::create($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Phase timeline created successfully.',
            'data' => new PhaseResource($timeline)
        ], 201);
    }

    public function budget(PhaseBudgetRequest $request)
    {
        $validated = $request->validated();

        $validated['type'] = PhaseType::BUDGET;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('expenses', 'public');
            $validated['file'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['date_uploaded'] = now()->toDateString();
        }

        $budget = Phase::create($validated);
        $validated['phase_id'] = $budget->id;
        PhaseHistory::create($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Phase timeline created successfully.',
            'data' => new PhaseResource($budget)
        ], 201);
    }

    public function updateTimeline(UpdatePhaseTimeLineRequest $request, string $id)
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            // Delete the old photo if it exists
            if ($request->file) {
                Storage::delete($request->file);
            }

            $file = $request->file('file');
            $path = $file->store('expenses', 'public');
            $validated['file'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['date_uploaded'] = now()->toDateString();
        }

        $timeline = Phase::find($id);
        $validated['phase_id'] = $timeline->id;
        PhaseHistory::create($validated);

        if (!$timeline) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Phase not found.',
            ], 404);
        }

        $validated['type'] = PhaseType::TIMELINE;

        $timeline->update($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Phase timeline updated successfully.',
            'data' => new PhaseResource($timeline)
        ], 201);
    }

    public function updateBudget(UpdatePhaseBudgetRequest $request, string $id)
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            // Delete the old photo if it exists
            if ($request->file) {
                Storage::delete($request->file);
            }

            $file = $request->file('file');
            $path = $file->store('expenses', 'public');
            $validated['file'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['date_uploaded'] = now()->toDateString();
        }

        $budget = Phase::find($id);

        if (!$budget) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Phase not found.',
            ], 404);
        }

        $validated['type'] = PhaseType::BUDGET;
        $budget->update($validated);
        $validated['phase_id'] = $budget->id;
        PhaseHistory::create($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Phase budget created successfully.',
            'data' => new PhaseResource($budget)
        ], 201);
    }

    public function categories(string $projectId)
    {
        $phases = Phase::where('project_id', $projectId)->where('parent_id', null)->get();

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Phase categories created successfully.',
            'data' => PhaseCategoryResource::collection($phases)
        ], 201);
    }
}
