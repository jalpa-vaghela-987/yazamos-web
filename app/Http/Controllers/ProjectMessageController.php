<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectMessageResource;
use App\Models\Project;
use App\Models\ProjectMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMessageController extends Controller
{
    public function index($projectId)
    {
        $messages = ProjectMessage::with('user')
            ->where('project_id', $projectId)
            ->orderBy('created_at')
            ->get();
        if ($messages->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No messages found for this project.',
                'status_code' => 404,
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => ProjectMessageResource::collection($messages),
        ]);
    }

    public function store(Request $request, $projectId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = ProjectMessage::create([
            'project_id' => $projectId,
            'user_id' => loginUser()->id,
            'message' => $request->message,
        ]);
        if (!$message) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create message.',
                'status_code' => 500,
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'data' => new ProjectMessageResource($message),
        ]);
    }
}
