<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\QueryCrudBuilderTrait;
use App\Models\InvitationLog;

class InvitationLogController extends Controller
{
    use QueryCrudBuilderTrait;

    public function index(Request $request)
    {
        $input = $request->all();
        $user = auth()->user();

        $query = InvitationLog::with('user');

        // Only allow non-admin/superadmin to view their own logs
        if (!in_array($user->custom_role ?? $user->getRoleNames()->first(), ['admin', 'super admin'])) {
            $query->where('user_id', $user->id);
        }

        $logs = $query->queryCrud($input); 

        return response()->json($logs, 200);
    }
}
