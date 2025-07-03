<?php

namespace App\Http\Controllers;

use App\Models\AssignedUserProject;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Enums\TransactionStatus;

class ProjectInvitationController extends Controller
{
    /**
     * Accept a project invitation
     */
    public function accept($projectId)
    {
        $invitation = AssignedUserProject::where('user_id', auth()->id())
            ->where('project_id', $projectId)
            ->where('invitation_status', 'pending')
            ->first();

        if (!$invitation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invitation not found'
            ], 404);
        }

        $invitation->update(['invitation_status' => 'accepted']);

        return response()->json([
            'status' => 'success',
            'message' => 'Project invitation accepted successfully'
        ]);
    }

    /**
     * Reject a project invitation
     */
    public function reject($projectId)
    {
        $invitation = AssignedUserProject::where('user_id', auth()->id())
            ->where('project_id', $projectId)
            ->where('invitation_status', 'pending')
            ->first();

        if (!$invitation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invitation not found'
            ], 404);
        }

        $invitation->update(['invitation_status' => 'rejected']);

        return response()->json([
            'status' => 'success',
            'message' => 'Project invitation rejected successfully'
        ]);
    }

    /**
     * Get pending invitations for the authenticated user
     */
    public function pendingInvitations(Request $request)
    {
        $status = $request->query('status', 'accepted');
        //        log::info('status',$status);

        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $projectQuery = Project::with('assignedUserProjects')
            ->where('user_id', $userId)
            ->orWhere('user_id', loginUser()->created_by)
            ->orWhereHas('assignedUserProjects', function ($query) use ($userId, $startOfMonth, $endOfMonth) {
                $query->where('user_id', $userId);
                //            ->whereHas('user.createdBy.transactions', function ($query) use ($startOfMonth, $endOfMonth) {
                //                $query->where('is_subscribe', Transaction::SUBSCRIBE);
                //                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                //                $query->where('status', TransactionStatus::COMPLETE);
                //            });
            })
            ->get();

        $projects = $projectQuery->map(function ($project) use ($userId) {
            $assigned = $project->assignedUserProjects->firstWhere('user_id', $userId);

            return $project->setAttribute('invitation_status', $assigned?->invitation_status)
                ->setAttribute('role', $assigned?->role ?? $project->user->getRoleNames()->first());
        });

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Pending invitations retrieved successfully.',
            'data' => $projects
        ]);
    }

    public function getProjectRole($projectId)
    {
        $invitation = AssignedUserProject::where('user_id', auth()->id())
            ->where('project_id', $projectId)
            ->first();

        if (!$invitation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project role not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'role' => $invitation->role,
                'invitation_status' => $invitation->invitation_status
            ]
        ]);
    }
}
