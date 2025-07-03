<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class SignupController extends Controller
{
    // public function showSignupForm($role, Request $request)
    // {
    //     $country_code = $request->query('country_code', '');
    //     $phone = $request->query('phone', '');

    //     return view('signup.form', compact('role', 'country_code', 'phone'));
    // }


    public function processSignup(Request $request, $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone_number' => ['nullable', 'regex:/^[0-9]{6,15}$/'],
            'country_code' => 'required|string',
        ]);

        // Check if user with given phone number exists
        $user = User::where('phone_number', $request->phone_number)
            ->where('country_code', $request->country_code)
            ->first();

        // If user exists, update the user
        if ($user) {
            // Optionally, you can update other details like name or email if needed.
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        } else {
            // If user doesn't exist, create a new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'country_code' => $request->country_code,
            ]);
        }

        // Assign role to the user
        $user->assignRole($role);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ]
        ]);

    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|numeric',
            'country_code' => 'required|string',
        ]);
        $fullPhone = $request->country_code . $request->phone_number;
        $otp = random_int(100000, 999999);
        $otpData = [
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
        ];
        $otpData['mobile_no'] = $fullPhone;
        Otp::where('mobile_no', $fullPhone)->delete();
        Otp::create($otpData);
        $this->sendSmsNotification($user ?? $fullPhone, "Your OTP is: $otp");

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'OTP sent successfully',
        ], 200);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|numeric',
            'country_code' => 'required|string',
            'otp' => 'required|numeric|digits:6',
        ]);

        $fullPhone = $request->country_code . $request->phone_number;

        $storedOtp = Otp::where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->where(function ($query) use ($fullPhone) {
                $query->where('mobile_no', $fullPhone)
                    ->orWhereHas('user', function ($q) use ($fullPhone) {
                        $q->whereRaw("CONCAT(country_code, phone_number) = ?", [$fullPhone]);
                    });
            })
            ->first();

        if (!$storedOtp) {
            return response()->json([
                'status' => 'error',
                'status_code' => 422,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        if ($storedOtp->user_id) {
            $user = User::find($storedOtp->user_id);

            if (!$user || ($request->phone_number != $user->phone_number)) {
                return response()->json([
                    'status' => 'error',
                    'status_code' => 422,
                    'message' => 'OTP mismatch.',
                ], 422);
            }
        } else {
            if ($storedOtp->mobile_no !== $fullPhone) {
                return response()->json([
                    'status' => 'error',
                    'status_code' => 422,
                    'message' => 'OTP mismatch.',
                ], 422);
            }
        }

        $storedOtp->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'OTP verified successfully.',
        ], 200);
    }



    private function sendSmsNotification($user, $message)
    {
        $phone = is_string($user) ? $user : $user->country_code . $user->phone_number;

        $response = Http::asForm()->post('https://rest.nexmo.com/sms/json', [
            'api_key'    => env('VONAGE_API_KEY'),
            'api_secret' => env('VONAGE_API_SECRET'),
            'to'         => $phone,
            'from'       => env('VONAGE_FROM'),
            'text'       => $message
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to send SMS.');
        }

        return true;
    }
}
