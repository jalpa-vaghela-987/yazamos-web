<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->google2fa_secret && $user->two_factor_verified === 0) {
            return response()->json([
                'status' => 'error',
                'status_code' => 403,
                'message' => '2FA verification required.',
                'data' => null
            ], 403);
            
        }

        return $next($request);
    }
}

