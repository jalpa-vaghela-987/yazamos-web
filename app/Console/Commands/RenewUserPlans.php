<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Traits\TranzilaTrait;
use App\Models\CardDetail;

class RenewUserPlans extends Command
{
    use TranzilaTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yazamos:renew-user-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-renew active user plans monthly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            \Log::info(["plan auto renewable command run..."]);

            // Get all plans that end today or earlier and are renewable
            $expiringPlans = Transaction::with('cardDetail')
                ->whereRaw("DATE_ADD(created_at, INTERVAL 1 MONTH) <= CURDATE()")
                ->latest('created_at')
                ->where('status', TransactionStatus::COMPLETE)
                ->where('is_renew', Transaction::PENDING_RENEW)
                ->where('is_subscribe', Transaction::SUBSCRIBE)
                ->get();

            foreach ($expiringPlans as $plan) {
                //create user transaction with succes and failed message
                $cardDetail = CardDetail::where('user_id', $plan->user_id)
                                        ->where('is_active', CardDetail::ACTIVE)
                                        ->first();
                                        
                $payload    =   [
                    'card_number'=> $cardDetail->card_no,
                    'expiration_month' => $cardDetail->expiry_month,
                    'expiration_year' => $year = substr(Carbon::now()->year, 0, 2) . $cardDetail->expiry_year,
                    'cvv' => $cardDetail->cvv,
                    'amount' => $plan->amount,
                ];
                
                $this->initializeGateway();

                $response   =   $this->processPayment1($payload);
                $transactionId     =   $response->getTransactionReference();
                $transactionData = [
                    'transaction_id' => $transactionId,
                    'plan_id' => $plan->plan_id,
                    'amount' => $plan->amount,
                    'user_id' => $plan->user_id,
                    'transaction_data' => json_encode($response->getData()),
                    'card_detail_id' => $cardDetail->id,
                    'message' => $response->getMessage(),
                    'status' => $response->isSuccessful() 
                        ? TransactionStatus::COMPLETE 
                        : TransactionStatus::FAILED,
                ];
        
                Transaction::create($transactionData);
                $plan->update([
                    'is_renew' => $response->isSuccessful() ? 1 : 2,
                ]);
        
                \Log::info([
                    'message' => $response->isSuccessful() 
                        ? 'Payment successful' 
                        : 'Payment failed',
                    'data' => $transactionData,
                ]);
        
            }

        }catch(Exception $e){
            \Log::info(['message' => $e->getMessage()]);
        }
    }
}
