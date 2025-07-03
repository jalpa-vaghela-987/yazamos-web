<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Models\CardDetail;

class TestController extends Controller
{
    public function expiredUserExistingPlan(Request $request) {
        $activePlan = Transaction::with('cardDetail')
                    ->latest('created_at')
                    ->where('status', TransactionStatus::COMPLETE)
                    ->where('user_id', loginUser()->id)
                    ->first();
       

        $activePlan->update([
            'created_at' => $request->created_at
        ]);
        

        return response()->json(['plan' => Transaction::find($activePlan->id)]);

    }
}
