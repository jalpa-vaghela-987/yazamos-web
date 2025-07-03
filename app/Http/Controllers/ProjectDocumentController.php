<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectFileResource;
use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectDocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'document' => 'required|file|max:20480', // max 20MB
        ]);

        $project = Project::findOrFail($request->project_id);
        $file = $request->file('document');

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // ✅ Generate filename like your image upload logic
        $filename = 'project_' . now()->format('Ymd_His') . '_' . uniqid() . '.' . $extension;
        $path = 'projects/' . $filename;

        \Log::info('Generated document path', [
            'original_name' => $originalName,
            'generated_path' => $path
        ]);

        // Store the file
        $file->storeAs('projects', $filename, 'public');

        // Save record
        $doc = ProjectDocument::create([
            'project_id' => $project->id,
            'name' => $request->name, // user-defined display name
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
        ]);

        \Log::info('Document path stored in DB', [
            'project_id' => $project->id,
            'document_path' => $path
        ]);

        return new ProjectFileResource($doc);
    }



    public function index($projectId)
    {
        $documents = ProjectDocument::where('project_id', $projectId)->get();
        return ProjectFileResource::collection($documents);
    }

    public function destroy($id)
    {
        $doc = ProjectDocument::findOrFail($id);
        Storage::disk('public')->delete($doc->file_path);
        $doc->delete();

        return response()->json(['message' => 'Document deleted']);
    }
}
