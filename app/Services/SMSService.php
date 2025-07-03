<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class SMSService
{
    protected $client;
    protected $apiUrl;
    protected $apiKey;
    protected $apiSecret;
    protected $apiFrom;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = config('services.sms.api_send_url');
        $this->apiKey = config('services.sms.vonage_api_key');
        $this->apiSecret = config('services.sms.vonage_api_secret');
        $this->apiFrom = config('services.sms.vonage_from');
    }

    public function sendSMS(string $message, string $phone)
    {
        if ( env('APP_ENV') !== 'local' ) {
            try {
                $response = Http::asForm()->post($this->apiUrl, [
                    'api_key'    => $this->apiKey ,
                    'api_secret' => $this->apiSecret,
                    'to'         => $phone,
                    'from'       => $this->apiFrom,
                    'text'       => $message
                ]);

                return json_decode($response->getBody(), true);
            } catch (\Exception $e) {
                Log::info('SMS service error ' . $e->getMessage());
                return ['error' => $e->getMessage(), 'code' => $e->getCode()];
            }
        }
    }
}
