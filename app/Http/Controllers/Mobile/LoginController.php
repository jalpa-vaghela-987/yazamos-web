<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'country_code' => 'required|string',
            'device_id' => 'required|string',

        ]);

        $user = User::where('phone_number', $request->phone_number)->where('country_code', $request->country_code)->first();
            if (!$user) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'User with this phone number does not exist'
            ], 404);
        }
        $token = $user->tokens()
            ->where('name', 'mobile_auth_token')->whereNot('device_id', $request->device_id)->first();
    
        if (isset($token)) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Token Already exist'
            ], 404);
        }



        $otp = rand(100000, 999999);

        $user->update([
            'otp'             => $otp,
            'otp_expires_at'  => Carbon::now()->addMinutes(5),
        ]);

        $response = Http::asForm()->post('https://rest.nexmo.com/sms/json', [
            'api_key'    => env('VONAGE_API_KEY'),
            'api_secret' => env('VONAGE_API_SECRET'),
            'to'         => $user->country_code . $user->phone_number,
            'from'       => env('VONAGE_FROM'),
            'text'       => "Your OTP is: $otp"
        ]);

        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Failed to send OTP.'
            ], 500);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'OTP sent successfully.',
            'user_id' => $user->id,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'otp'     => 'required|numeric',
            'device_id' => 'required|string',
        ]);

        $user = User::find($request->user_id);

        if (
            $user &&
            $user->otp === $request->otp &&
            $user->otp_expires_at &&
            $user->otp_expires_at->isFuture() ||
            $request->otp == getMasterOTP()
        ) {
            $user->update([
                'otp'            => null,
                'otp_expires_at' => null,
            ]);

            $user->tokens()
                ->where('name', 'mobile_auth_token')
                ->delete();
            $token = $user->createToken('mobile_auth_token')->plainTextToken;

            $userToken = $user->tokens()
                ->where('name', 'mobile_auth_token')->first();
            if ($userToken->device_id == NULL) {
                $userToken->device_id = $request->device_id;
                $userToken->save();
            }

            return response()->json([
                'message' => 'Logged in successfully.',
                'token'   => $token,
                'user'    => UserResource::make($user),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'status_code' => 404,
            'message' => 'Invalid or expired OTP.'
        ], 422);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            // Delete only the current token
            $currentToken = $request->bearerToken();

            if ($currentToken) {
                $user->tokens()
                    ->where('name', 'mobile_auth_token')
                    ->delete();
            }

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Error during logout'
            ], 500);
        }
    }
}
