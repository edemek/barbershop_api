<?php

use App\Http\Controllers\AuthController; // Ensure this class exists in the specified namespace
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route pour envoyer l'OTP
Route::post('/send-otp', [SmsController::class, 'sendOtp']);

// Route pour vérifier l'OTP
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
