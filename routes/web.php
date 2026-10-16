<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CMSController;
use App\Http\Controllers\Api\V1\WebhookController;

// Clear Cache Routes
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    return '<h1>Cache facade value cleared</h1>';
});

// Admin Redirect Routes
Route::get('/', function () {
    return redirect('/admin');
});

// Pages Routes
Route::get('page/{slug}', [CMSController::class, 'show'])->name('frontend.page.show');

Route::get('/stripe-redirect/{token}', [WebhookController::class, 'connectRedirect'])->name('stripe.redirect');


