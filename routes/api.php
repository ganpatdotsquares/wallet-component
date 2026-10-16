<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\StripeController;
use App\Http\Controllers\Api\V1\WalletController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api\V1', 'as' => 'api.', 'middleware' => 'api.header'], function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('delete', [AuthController::class, 'delete']);
        Route::post('otp-verification', [AuthController::class, 'otpVerification']);
        Route::post('otp-verification-resend', [AuthController::class, 'otpreSendVerification']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('forgot-password-otp', [AuthController::class, 'forgotPasswordOtp']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::group(['middleware' => ['api.authtoken']], function () {
    
        // User Routes
        Route::get('my-profile', [UserController::class, 'myProfile']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('change-password', [UserController::class, 'changePassword']);
        Route::get('delete-account', [UserController::class, 'deleteAccount']);

        // Pages Routes
        Route::get('page/{slug}', [BaseController::class, 'cmsPage']);

        Route::get('connect-stripe', [StripeController::class, 'connectStripe']);

        // Wallet API
        Route::prefix('wallet')->group(function () {
            Route::post('deposit/initiate', [WalletController::class, 'depositInitiate']);
            Route::post('deposit/confirm', [WalletController::class, 'depositConfirm']);
            Route::get('balance', [WalletController::class, 'getBalance']);
            Route::post('withdraw-request', [WalletController::class, 'withdrawRequest']);
            Route::post('withdraw-request-cancel', [WalletController::class, 'withdrawRequestCancel']);
            Route::post('send', [WalletController::class, 'sendMoney']);
            Route::get('transactions', [WalletController::class, 'transactionHistory']);
        });
    });
});