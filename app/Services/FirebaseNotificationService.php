<?php

namespace App\Services;

use App\Models\FcmToken;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    public function sendToUser(int $userId, string $title, string $body): array
    {
        Log::error('Sending notification to user: ' . $userId);

        $record = FcmToken::where('user_id', $userId)->first();

        if (! $record || ! $record->fcm_token) {
            throw new \Exception('FCM token not available for this user');
        }

        $response = $this->sendFirebaseNotification($record->fcm_token, $title, $body);

        // Handle unregistered tokens
        if (
            isset($response['error']['details'][0]['errorCode']) &&
            $response['error']['details'][0]['errorCode'] === 'UNREGISTERED'
        ) {
            $record->delete();
            throw new \Exception('Token is unregistered and has been deleted');
        }

        return $response;
    }

    private function sendFirebaseNotification(string $fcmToken, string $title, string $body): array
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('firebase/google-services.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $accessTokenArr = $client->fetchAccessTokenWithAssertion();
        if (isset($accessTokenArr['error'])) {
            throw new \Exception('Unable to fetch Firebase access token: '
                . json_encode($accessTokenArr));
        }

        $accessToken = $accessTokenArr['access_token'];
        $projectId = env('FIREBASE_PROJECT_ID', 'yazamos');
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => ['notification' => ['sound' => 'default']],
                'apns' => ['payload' => ['aps' => ['sound' => 'default']]],
                'data' => ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ])->post($url, $payload);

        return $response->json();
    }
}
