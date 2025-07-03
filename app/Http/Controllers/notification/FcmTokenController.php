<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;


class FcmTokenController extends Controller
{
    /**
     * Persist or update the device's FCM registration token.
     *
     * POST /api/fcm/token
     * Body: { fcm_token: string }
     */
    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        FcmToken::updateOrCreate(
            ['user_id' => $user->id],
            ['fcm_token' => $request->fcm_token]
        );

        return response()->json(['message' => 'FCM token saved'], 200);
    }

    /**
     * Send a notification to the authenticated user's device.
     *
     * POST /api/fcm/send
     * Body: { title: string, body: string }
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title'    => 'required|string',
            'body'     => 'required|string',
            'user_id'  => 'nullable|exists:users,id',
        ]);

        // If user_id is provided, use it. Otherwise, fallback to the authenticated user.
        $targetUser = $request->user_id
            ? User::find($request->user_id)
            : Auth::user();

        if (! $targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $record = FcmToken::where('user_id', $targetUser->id)->first();
        if (! $record || ! $record->fcm_token) {
            return response()->json(['error' => 'FCM token not available for this user'], 422);
        }

        try {
            $result = $this->sendFirebaseNotification(
                $record->fcm_token,
                $request->title,
                $request->body
            );
        } catch (\Exception $e) {
            Log::error('FCM send error', ['exception' => $e]);
            return response()->json([
                'error'   => 'Failed to send notification',
                'details' => $e->getMessage(),
            ], 500);
        }

        // Handle unregistered token
        if (
            isset($result['error']['details'][0]['errorCode'])
            && $result['error']['details'][0]['errorCode'] === 'UNREGISTERED'
        ) {
            $record->delete();
            return response()->json([
                'error' => 'Token is unregistered; it has been deleted from our records.'
            ], 410);
        }

        return response()->json([
            'message'  => 'Notification sent',
            'response' => $result,
        ], 200);
    }

    /**
     * Internal helper: uses FCM HTTP v1 API with Service Account JSON.
     *
     * @throws \Exception
     */
    private function sendFirebaseNotification(string $fcmToken, string $title, string $body): array
    {
        // 1) Authenticate with service account
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('firebase/google-services.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $accessTokenArr = $client->fetchAccessTokenWithAssertion();
        if (isset($accessTokenArr['error'])) {
            throw new \Exception('Unable to fetch Firebase access token: '
                . json_encode($accessTokenArr));
        }
        $accessToken = $accessTokenArr['access_token'];

        // 2) Build endpoint URL
        $projectId = env('FIREBASE_PROJECT_ID', 'yazamos');
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // 3) Compose payload
        $payload = [
            'message' => [
                'token'        => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                // Optional: platform-specific sound settings
                'android' => [
                    'notification' => ['sound' => 'default'],
                ],
                'apns' => [
                    'payload' => ['aps' => ['sound' => 'default']],
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            ],
        ];

        // 4) Send request
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ])->post($url, $payload);

        return $response->json();
    }
}
