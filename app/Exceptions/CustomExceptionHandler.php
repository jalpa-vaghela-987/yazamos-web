<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;

class CustomExceptionHandler
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'status' => 'error',
                'status_code' => $e->status,
                'message' => $e->getMessage() ?: 'Validation failed',
                'errors' => $e->errors(),
            ], $e->status);
        });

        // Add more handlers here if needed
    }
}