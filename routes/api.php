<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('/user', [AuthController::class, 'user'])->middleware('auth:api');

Route::middleware('role:admin')->group(function () {
    Route::resource('users', UserController::class);
});

Route::middleware('auth:api')->resource('contacts', ContactController::class);

