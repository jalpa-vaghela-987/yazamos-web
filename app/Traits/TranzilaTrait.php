<?php

namespace App\Traits;

// use Omnipay\Omnipay;

use Futureecom\OmnipayTranzila\TranzilaGateway;

trait TranzilaTrait
{
    protected $gateway;
    protected $payment_currency;

    /**
     * Initialization of the gatway
     *
     * @param Null
     *
     * @return Null
     */
    public function initializeGateway()
    {
        $this->payment_currency = config('services.tranzila.currency');//"ILS";
        $supplierId = config('services.tranzila.supplier');
        $password = config('services.tranzila.password');

        $this->gateway = new TranzilaGateway();
        $this->gateway->setSupplier($supplierId);
        $this->gateway->setTerminalPassword($password);
    }

    /**
     * Authorization of the payment with
     *
     */
    public function authorizePayment($data)
    {
       return $this->gateway->authorize([
           'amount'   => $data['amount'],
           'currency' => $this->payment_currency,
           'myid'     => $data['my_id'],
           'ccno'     => $data['card_number'],
           'cred_type'=>$data['cred_type'],//normal transaction
           'tranmode'=>'A',
           'expdate'  => $data['expiration_month'] . substr($data['expiration_year'], -2),
           'mycvv'    => $data['cvv'],
       ])->send();
    }

    /**
     * Capture the payment after authorization
     *
     * @param String $tran_ref
     *
     * @return Object $response
     */
    public function capturePayment($tran_ref, $amount)
    {
        $response = $this->gateway->capture([
            'transaction_reference' => $tran_ref,
            'amount'                => $amount,
        ])->send();
        return $response;
    }

    /**
     * Authorize and Process the payment with card details
     *
     * @param Array $data
     */
    public function processPayment1($data)
    {
        $response = $this->gateway->purchase([
            'tranmode'=> 'E',
            'amount'    => $data['amount'],
            'currency'  => $this->payment_currency,
            // 'myid'      => $data['my_id'],
            'ccno'     => $data['card_number'],
            'cred_type' => 2,//normal transaction
            'expdate'  => $data['expiration_month'] . substr($data['expiration_year'], -2),
            'mycvv'    => $data['cvv'],
        ])->send();

        return $response;
    }

    public function refundPayment($tran_ref, $amount = null)
    {
        $response = $this->gateway->refund([
            'amount'                => $amount,
            'currency'              => $this->payment_currency,
            'transaction_reference' => $tran_ref,
        ])->send();

        return $response;
    }

    /**
     * Cancel a transaction
     *
     * @param string $tran_ref
     *
     * @return object $response
     */
    public function cancelTransaction($tran_ref)
    {
        $response = $this->gateway->void([
            'transaction_reference' => $tran_ref,
            // 'TranzilaTK' => 'SomeToken',
        ])->send();
        return $response;
    }

    public function isGatewayInitialized()
    {
        return isset($this->gateway);
    }

    public function fetchTransaction($transaction_id)
    {
        return $this->gateway->fetchTransaction(['transactionId' => $transaction_id])->send();
    }
}
