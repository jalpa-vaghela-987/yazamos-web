<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectDropDownResource;
use App\Models\Project;
use App\Models\ProjectHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $input = $request->all();

        $query = Project::with('user');

        if (!empty($request->asset_type_id)) {
            $query->where('asset_type_id', $request->asset_type_id);
        }

        if (loginUser()->hasRole(ROLE_ADMIN)) {
            $ids = User::where('created_by', loginUser()->id)->pluck('id');
            $query->whereIn('user_id', $ids)->orWhere('user_id', loginUser()->id);
        }

        if (loginUser()->hasRole(ROLE_ENTREPRENEUR)) {
            $query->where('user_id', loginUser()->id)->orWhere('user_id', loginUser()->created_by);
        }

        $projects = $query->queryCrud($input);
        if ($projects->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No projects found for this user.'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Projects retrieved successfully.',
            'data' => ProjectResource::collection($projects)
        ], 200);
    }



    /**
     * Display a listing of the deleted projects.
     */
    public function deletedProjects(Request $request)
    {
        $query = Project::onlyTrashed()->with('user');

        if (loginUser()->hasRole(ROLE_ADMIN)) {
            $ids = User::where('created_by', loginUser()->id)->pluck('id')->push(loginUser()->id);

            $query->where(function ($q) use ($ids) {
                $q->whereIn('user_id', $ids);
            });
        }

        if (loginUser()->hasRole(ROLE_ENTREPRENEUR)) {
            $query->where(function ($q) {
                $q->where('user_id', loginUser()->id)
                    ->orWhere('user_id', loginUser()->created_by);
            });
        }

        $projects = $query->get();
        if ($projects->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No deleted projects found for this user.'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Deleted projects retrieved successfully.',
            'data' => ProjectResource::collection($projects),
        ]);
    }



    public function restoreProject($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        if (!$project) {
            return response()->json(['message' => 'Project not found.'], 404);
        }
        if (is_null($project->deleted_at)) {
            return response()->json(['message' => 'Project is not deleted.'], 400);
        }

        // Check permission
        if (
            loginUser()->hasRole(ROLE_ADMIN) &&
            !in_array($project->user_id, User::where('created_by', loginUser()->id)->pluck('id')->push(loginUser()->id)->toArray())
        ) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (
            loginUser()->hasRole(ROLE_ENTREPRENEUR) &&
            !in_array($project->user_id, [loginUser()->id, loginUser()->created_by])
        ) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $project->restore();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Project restored successfully.',
            'data' => new ProjectResource($project)
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            // Log request validation

            $validated = $request->validated();
            $validated['user_id'] = loginUser()->id;

            // Create project
            $project = Project::create($validated);
            if (!$project) {
                Log::error('Failed to create project', $validated);
                return response()->json([
                    'status' => 'error',
                    'status_code' => 500,
                    'message' => 'Failed to create project.'
                ], 500);
            }
            $validated['project_id'] = $project->id;

            // Create project history
            ProjectHistory::create($validated);

            $images = $request->input('images');
            if (is_array($images)) {
                foreach ($images as $index => $imageData) {
                    if ($request->hasFile("images.$index.file")) {
                        $image = $request->file("images.$index.file");
                        $flag = $request->input("images.$index.flag");

                        $originalName = $image->getClientOriginalName();
                        $extension = $image->getClientOriginalExtension();

                        $filename = 'project_' . now()->format('Ymd_His') . '_' . uniqid() . '.' . $extension;
                        $path = 'projects/' . $filename;

                        Log::info('Generated image path', ['original_name' => $originalName, 'generated_path' => $path]);

                        $project->images()->create([
                            'image' => $path,
                            'flag' => $flag
                        ]);

                        Log::info('Image path stored in DB', ['project_id' => $project->id, 'image_path' => $path]);

                        $image->storeAs('projects', $filename, 'public');
                        Log::info('Image stored in filesystem', ['path' => $path]);
                    }
                }
            } else {
                Log::warning('Images input is not an array or is missing', ['images' => $images]);
            }



            return response()->json([
                'status' => 'success',
                'status_code' => 201,
                'message' => 'Project created successfully.',
                'data' => new ProjectResource($project)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating project', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Error creating project',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with('user')->find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Project not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Project created successfully.',
            'data' => new ProjectResource($project)
        ], 201);
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
    public function update(UpdateProjectRequest $request, string $id)
    {
        Log::info('Updating project - request validated', $request->validated());

        $validated = $request->validated();

        $project = Project::find($id);

        if (!$project) {
            Log::error('Project not found', ['project_id' => $id]);
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Project not found.',
            ], 404);
        }

        try {
            $incomingImages = $request->input('images', []);

            // Collect IDs of images user wants to keep
            $keepImageIds = collect($incomingImages)->pluck('id')->filter()->toArray();

            // Delete images that are currently attached but not in keep list
            $imagesToDelete = $project->images()->whereNotIn('id', $keepImageIds)->get();
            foreach ($imagesToDelete as $oldImage) {
                if ($oldImage->image && Storage::disk('public')->exists($oldImage->image)) {
                    Storage::disk('public')->delete($oldImage->image);
                }
                $oldImage->delete();
            }

            // Update flags on existing images
            foreach ($incomingImages as $index => $imgData) {
                if (isset($imgData['id'])) {
                    $image = $project->images()->find($imgData['id']);
                    if ($image && isset($imgData['flag'])) {
                        $image->flag = $imgData['flag'];
                        $image->save();
                    }
                }
            }

            // Handle new images upload
            foreach ($incomingImages as $index => $imgData) {
                if ($request->hasFile("images.$index.file")) {
                    $imageFile = $request->file("images.$index.file");
                    $flag = $imgData['flag'] ?? null;

                    $filename = 'project_' . now()->format('Ymd_His') . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                    $path = 'projects/' . $filename;

                    $project->images()->create([
                        'image' => $path,
                        'flag' => $flag
                    ]);

                    $imageFile->storeAs('projects', $filename, 'public');
                }
            }

            // Update project fields
            $project->update($validated);
            Log::info('Project updated', ['project_id' => $project->id]);

            // Create project history record
            $validated['changed_at'] = now();
            $validated['project_id'] = $project->id;
            ProjectHistory::create($validated);


            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Project updated successfully.',
                'data' => new ProjectResource($project)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating project', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Error updating project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //        $this->authorize('delete', Project::class);

        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Project not found.',
            ], 404);
        }

        if ($project->user_id !== loginUser()->id) {
            return response()->json([
                'status' => 'error',
                'status_code' => 403,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $project->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Project deleted successfully.',
        ], 200);
    }

    public function history($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Project not found.',
            ], 404);
        }

        $histories = ProjectHistory::where('project_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Project history retrieved successfully.',
            'data' => $histories
        ], 200);
    }

    public function userProjects(Request $request)
    {
        $query = Project::query();

        // Filter by role if provided
        if ($request->has('role')) {
            $query->whereHas('user', function ($query) use ($request) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            });
        }

        $projects = $query->get();

        return ProjectDropDownResource::collection($projects);
    }

    public function adminProjects()
    {
        $projects = Project::whereHas('user', function ($query) {
            $query->where('created_by', loginUser()->id);
            $query->whereHas('roles', function ($q) {
                $q->where('name', ROLE_ENTREPRENEUR);
            });
        })->orwhere('user_id', loginUser()->id)
            ->get();

        return ProjectDropDownResource::collection($projects);
    }

    public function getAllProjects(Request $request)
    {
        $projects = Project::whereHas('user', function ($query) use ($request) {
            $query->where('created_by', $request->created_by);
            $query->whereHas('roles', function ($q) {
                $q->where('name', ROLE_ENTREPRENEUR);
            });
        })->orwhere('user_id', $request->created_by)
            ->get();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Projects retrieved successfully.',
            'data' => ProjectResource::collection($projects)
        ], 200);
    }
}
