<?php

use Illuminate\Support\Facades\Route;
use admin\wallets\Controllers\Admin\WalletWebStripeController;

Route::middleware('web')
    ->get('/connect-account-redirect/{userId}', [WalletWebStripeController::class, 'connectAccountRedirect'])
    ->name('stripe.connect.account.redirect');
