<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogViewerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Optional: Add auth or role check here
        // abort_if(!auth()->user()->hasRole('admin'), 403);

        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Log file not found.',
            ], 404);
        }

        $lines = File::lines($logPath);
        $logs = collect($lines)->reverse()->take(100)->reverse()->values(); // last 100 lines

        return response()->json([
            'status' => 'success',
            'message' => 'Logs fetched successfully.',
            'data' => $logs,
        ]);
    }
}
