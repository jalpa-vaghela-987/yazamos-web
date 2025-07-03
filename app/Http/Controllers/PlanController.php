<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Http\Resources\PlanResource;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Enums\TransactionStatus;
use App\Http\Resources\UserActivePlanResource;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $plans = Plan::query()->with('currency')->queryCrud($input);
        // if ($plans->isEmpty()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'status_code' => 404,
        //         'message' => 'No plans found.',
        //     ], 404);
        // }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Plan retrieved successfully.',
            'data' => PlanResource::collection($plans)
        ], 200);
    }

    public function checkUserCurrentMonthActivePlan()
    {

        $transaction = Transaction::with(['plan', 'cardDetail'])
            ->where('user_id', loginUser()->id)
            ->whereRaw("DATE_ADD(created_at, INTERVAL 1 MONTH) >= CURDATE()")
            ->latest('created_at')
            ->where('status', TransactionStatus::COMPLETE)
            ->first();
        // if (!$transaction) {
        //     return response()->json([
        //         'status' => 'error',
        //         'status_code' => 404,
        //         'message' => 'No active plan found for the current month.',
        //         'data' => []
        //     ], 404);
        // }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Plan retrieved successfully.',
            'data' => $transaction ? UserActivePlanResource::make($transaction) : [],
        ], 200);
    }

    public function subscribeUnsubscribePlan(string $transactionId, int $status)
    {
        $transaction = Transaction::find($transactionId);
        // if (!$transaction) {
        //     return response()->json([
        //         'status' => 'error',
        //         'status_code' => 404,
        //         'message' => 'Transaction not found.',
        //     ], 404);
        // }
        Transaction::where('id', $transactionId)
            ->where('user_id', loginUser()->id)
            ->update([
                'is_subscribe' => $status
            ]);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Plan status updated successfully.',
            'data' => UserActivePlanResource::make($transaction),
        ], 200);
    }

    public function checkUserExpiredPlan()
    {
        $transaction = Transaction::with(['plan', 'cardDetail'])
            ->where('user_id', loginUser()->id)
            ->whereRaw("DATE_ADD(created_at, INTERVAL 1 MONTH) <= CURDATE()")
            ->latest('created_at')
            ->first();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Plan retrieved successfully.',
            'data' => $transaction ? UserActivePlanResource::make($transaction) : [],
        ], 200);
    }
}
