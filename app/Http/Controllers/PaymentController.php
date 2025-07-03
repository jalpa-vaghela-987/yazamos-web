<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Http\Requests\PaymentRefundRequest;
use App\Http\Requests\PaymentRequest;
use App\Models\CardDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Traits\TranzilaTrait;
use Exception;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Models\User;

class PaymentController extends Controller
{
    use TranzilaTrait;

    public function processPayment(PaymentRequest $request){
        try{
            if($request->card_id && $request->card_id != 'new') {
                $cardDetail = CardDetail::find($request->card_id);
                $year = $cardDetail->expiry_year;
                $payload    =   [
                    'card_number'=> $cardDetail->card_no,
                    'expiration_month' => $cardDetail->expiry_month,
                    'expiration_year' => $year,
                    'cvv' => $cardDetail->cvv,
                    'amount' => $request->amount,
                ];
            } else {
                $year = substr(Carbon::now()->year, 0, 2) . $request->expiry_year;
                $payload    =   [
                    'card_number'=> $request->card_no,
                    'expiration_month' => $request->expiry_month,
                    'expiration_year' => $year,
                    'cvv' => $request->cvv,
                    'amount' => $request->amount,
                ];
            }

            $this->initializeGateway();

            $response   =   $this->processPayment1($payload);
            if($response->isSuccessful()){
                if($request->card_id && $request->card_id == 'new') {
                    //update old card is active = 0
                    CardDetail::where('user_id', loginuser()->id)
                    ->update([
                        'is_active' => 0
                    ]);
        

                    //create card details
                    $cardDetail = CardDetail::create([
                            'user_id' => loginUser()->id,
                            'expiry_year' => $year,
                            'is_active' => 1
                        ] + $request->validated()
                    );
                }

                $transaction_id     =   $response->getTransactionReference();
                $transaction        =   Transaction::create([
                    'transaction_id' => $transaction_id,
                    'plan_id' => $request->plan_id,
                    'amount' => $request->amount,
                    'user_id' => auth()->user()->id,
                    'transaction_data' => json_encode($response->getData()),
                    'status' => TransactionStatus::COMPLETE,
                    'card_detail_id' => $cardDetail->id,
                    'message' => $response->getMessage(),
                ]);
                return $response = response()->json([
                        'status' => 'success',
                        'status_code' => 200,
                        'message' => 'Transaction successfully.',
                        'data' => $transaction
                ]);
            }else{
                return response()->json([
                    'status' => 'error',
                    'status_code' => 500,
                    'message' => "Could not process the payment, Error ".$response->getMessage()
                ], 500);
            }
        }catch(Exception $e){
            return $response = response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    /**
     * Make refund to the patron
     *
     * @param $transaction_id
     *
     * @return response with success message
     */
    public function refund(PaymentRefundRequest $request){
        try{
            $record     =   Transaction::firstWhere(['id' => $request->transaction_id]);
            $this->initializeGateway();
            $transaction_id =   $record->transaction_id;//explode('-',$record->transaction_id)[0];
            $response   =   $this->refundPayment($transaction_id,$record->amount);
            print_r($response);
        }catch(Exception $e){
            return $response = response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
