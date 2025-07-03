<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuccessResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserinfoResources;
use App\Http\Resources\UserResource;
use App\Mail\SendOtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FAQRCode\Google2FA;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);


        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        return UserResource::make($user);
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);


        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages(['email' => 'email not found.']);
        }
        if (!($user->hasRole(ROLE_SUPER_ADMIN) || $user->hasRole(ROLE_ADMIN))) {
            throw ValidationException::withMessages(['email' => 'You don\'t have permission to login.']);
        }
        if (!($user->hasRole('super admin') || $user->hasRole('admin'))) {
            return response()->json([
                'status' => 'unauthorized',
                'status_code' => 403,
                'message' => 'Access denied. Only admin can login here.',
                'data' => null,
            ], 403);
        }

        if (empty($user->google2fa_secret) || empty($user->email_verified_at)) {
            $google2fa = new Google2FA();
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );

            return response()->json([
                'status' => '2fa_required',
                'status_code' => 200,
                'message' => '2FA setup required. Scan QR code.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'two_factor_enabled' => true,
                    ],
                    'qr_code_url' => $qrCodeUrl,
                    'manual_key' => $user->google2fa_secret
                ]
            ], 200);
        }

        // Check if 2FA is enabled
        if (!empty($user->google2fa_secret)) {
            return response()->json([
                'status' => '2fa_required',
                'status_code' => 200,
                'message' => 'Two-factor authentication required.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'two_factor_enabled' => !is_null($user->google2fa_secret),
                    ],
                ]
            ], 200);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => UserResource::make($user)
            ]
        ], 200);
    }


    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            $currentToken = $request->bearerToken();
            if ($currentToken) {
                $user->tokens()->where('name', 'auth_token')->delete();
            }

            $user->update(['two_factor_verified' => false]);

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

    public function profile(Request $request)
    {

        $user = User::findOrFail(loginUser()->id);
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        return ProfileResource::make($user);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email|string',
            'country_code' => 'required_with:phone|string',
        ]);


        $user = null;
        if ($request->has('email')) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = User::where('phone_number', $request->phone)
                ->where('country_code', $request->country_code)
                ->first();
        }

        log::info('user', [$user]);
        if (!$user) {
            throw ValidationException::withMessages([
                $request->has('email') ? 'email' : 'phone' => ['User not found'],
            ]);
        }

        if (!$user->hasRole(ROLE_ENTREPRENEUR) && !$user->hasRole(ROLE_INVESTOR) && !$user->hasRole(ROLE_TENANT)) {
            throw ValidationException::withMessages([
                $request->has('email') ? 'email' : 'phone' => ['You don\'t have permission to login.'],
            ]);
        }

        if ($user->hasRole('super admin')) {
            return response()->json([
                'status' => 'unauthorized',
                'status_code' => 403,
                'message' => 'Access denied. Only User can login here.',
                'data' => null,
            ], 403);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Save to DB
        Otp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
            ]
        );

        // Send OTP based on login method
        if ($request->has('email')) {
            Mail::to($request->email)->send(new SendOtpMail($otp));
        } else {

            $response = Http::asForm()->post('https://rest.nexmo.com/sms/json', [
                'api_key'    => env('VONAGE_API_KEY'),
                'api_secret' => env('VONAGE_API_SECRET'),
                'to'         => $user->country_code . $user->phone_number,
                'from'       => env('VONAGE_FROM'),
                'text'       => "Your OTP is: $otp"
            ]);
        }

        return response()->json([
            'message' => 'OTP sent successfully.',
            'user_id' => $user->id,
            'otp' => $otp // remove this in production!
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if (!$user->hasRole(ROLE_ENTREPRENEUR) && !$user->hasRole(ROLE_INVESTOR) && !$user->hasRole(ROLE_TENANT)) {
            throw ValidationException::withMessages([
                'user_id' => ['You don\'t have permission to login.'],
            ]);
        }

        if ($user->hasRole('super admin')) {
            return response()->json([
                'status' => 'unauthorized',
                'status_code' => 403,
                'message' => 'Access denied. Only User can login here.',
                'data' => null,
            ], 403);
        }

        // Generate a new 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP to the database
        Otp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
            ]
        );

        // TODO: Send OTP via email, SMS, etc.
        return response()->json([
            'message' => 'OTP resent successfully.',
            'otp' => $otp,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        ]);

        $otpRecord = Otp::where('user_id', $request->user_id)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $user = User::find($request->user_id);
        $token = $user->createToken('auth_token')->plainTextToken;

        // Remove OTP after successful login
        $otpRecord->delete();

        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ]
        ]);
    }


    public function showAllUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->role($request->role, 'api');
        }

        if ($request->has('created_by')) {

            $entrepreneurIds = User::where('created_by', $request->created_by)
                ->whereHas('roles', function ($q) {
                    $q->where('name', ROLE_ENTREPRENEUR);
                })
                ->pluck('id');

            $query->where(function ($q) use ($entrepreneurIds) {
                $q->whereIn('id', $entrepreneurIds)
                    ->orWhereHas('assignedUserProjects', function ($q2) use ($entrepreneurIds) {
                        $q2->whereHas('project', function ($q3) use ($entrepreneurIds) {
                            $q3->whereIn('user_id', $entrepreneurIds);
                        });
                    });
            });
        }

        $users = $query->get();

        return UserResource::collection($users);
    }

    public function showAllUsersinfo(Request $request)
    {
        $query = User::with(['assignedUserProjects.project', 'transactions.plan']);

        if ($request->has('role')) {
            $query->role($request->role, 'api');
        }



        if ($request->has('created_by')) {
            $entrepreneurIds = User::where('created_by', $request->created_by)
                ->whereHas('roles', function ($q) {
                    $q->where('name', ROLE_ENTREPRENEUR);
                })
                ->pluck('id');

            $query->where(function ($q) use ($entrepreneurIds) {
                $q->whereIn('id', $entrepreneurIds)
                    ->orWhereHas('assignedUserProjects', function ($q2) use ($entrepreneurIds) {
                        $q2->whereHas('project', function ($q3) use ($entrepreneurIds) {
                            $q3->whereIn('user_id', $entrepreneurIds);
                        });
                    });
            });
        }
        if ($request->has('user_id')) {
            $query->where('id', $request->user_id);
        }

        $users = $query->get();

        return UserinfoResources::collection($users);
    }

    public function send2FAQrToEmail(Request $request)
    {
        $user = User::find($request->id);

        if (!$user || !$user->google2fa_secret) {
            return response()->json([
                'message' => 'User not found or 2FA not enabled.'
            ], 404);
        }
        // Send Email
        Mail::to($user->email)->send(new \App\Mail\Send2FACodseMail($user));

        return response()->json([
            'message' => 'QR Code sent to your email.'
        ], 200);
    }
}
