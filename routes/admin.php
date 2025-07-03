<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'adminLogin']);
Route::group(['middleware' => ['auth:sanctum', 'role:super admin']], function () {
    Route::post("logout", [AuthController::class, 'logout']);
    Route::apiResource('admin-user', AdminController::class);
});
