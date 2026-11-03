<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->middleware('throttle:login');
Route::post('/register', RegisterController::class)->middleware('throttle:register');
Route::post('/logout', LogoutController::class);
Route::post('/forgot-password', ForgotPasswordController::class)->middleware('throttle:password-reset');
Route::post('/reset-password', ResetPasswordController::class)->middleware('throttle:password-reset');

Route::view('/{any?}', 'spa')->where('any', '.*');
