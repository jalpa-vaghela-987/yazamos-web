<?php

namespace App\Http\Controllers;

use App\Http\Requests\VarifytwofactorRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Validation\ValidationException;

class TwoFactorAuthController extends Controller
{
    public function enable2FA(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        $google2fa = new Google2FA();

        if ($user->google2fa_secret) {
            return response()->json([
                'status' => 'error',
                'status_code' => 400,
                'message' => '2FA is already enabled.',
                'data' => null
            ], 400);
        }

        // Generate a new secret key
        $user->google2fa_secret = $google2fa->generateSecretKey();
        $user->save();

        // Generate QR Code URL for Google Authenticator
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'), // App name
            $user->email, // User email
            $user->google2fa_secret // Secret Key
        );

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => '2FA enabled successfully. Scan the QR code with Google Authenticator.',
            'data' => [
                'qr_code_url' => $qrCodeUrl,
                'manual_key' => $user->google2fa_secret,
                'user' => new UserResource($user)
            ]
        ], 200);
    }
    public function verify2FA(VarifytwofactorRequest $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'User not found'
            ], 404);
        }

        $google2fa = new Google2FA();

        if (!$user->google2fa_secret) {
            return response()->json([
                'status' => 'error',
                'status_code' => 400,
                'message' => '2FA is not enabled'
            ], 400);
        }

        // Verify OTP
        if ($google2fa->verifyKey($user->google2fa_secret, $request->otp) || $request->otp == getMasterOTP()) {
            $user->update(['two_factor_verified' => true,'email_verified_at' => now()]);

            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => '2FA successfully verified!',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => new UserResource($user)
                ]
            ], 200);
        }

        // return response()->json([
        //     'status' => 'error',
        //     'status_code' => 401,
        //     'message' => 'Invalid OTP code.',
        //     'data' => null
        // ], 401);
        throw ValidationException::withMessages(['otp' => 'Invalid OTP code.']);

    }
    public function disable2FA(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!$user->google2fa_secret) {
            return response()->json([
                'status' => 'error',
                'status_code' => 400,
                'message' => '2FA is not enabled.',
                'data' => null
            ], 400);
        }

        $user->google2fa_secret = null;
        $user->save();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => '2FA disabled successfully.',
            'data' => [
                'user' => new UserResource($user)
            ]
        ], 200);
    }
}
