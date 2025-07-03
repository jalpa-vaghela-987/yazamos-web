<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function index(Request $request)
    {

        $input = $request->all();

        $transactions = Transaction::with(['user', 'plan', 'plan.currency', 'cardDetail'])
                        ->where('user_id', loginUser()->id)
                        ->orderBy('id', 'desc')
                        ->queryCrud($input);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Transaction retrieved successfully.',
            'data' => TransactionResource::collection($transactions)
        ], 200);
    }
}
