<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;


class WebhookController extends Controller
{
    public function connectRedirect($token)
    {
        Log::Info('connectRedirect=====URL=======Call========');
        $usrData = User::where('stripe_token', $token)->first();

        $stripe = new \Stripe\StripeClient(config('services.stripe.secretKey'));

        $acctstatus = $stripe->accounts->retrieve(
            $usrData->stripe_account_id,
            []
        );

        if (!empty($usrData) && $acctstatus->charges_enabled == true) {
            $usrData->stripe_payouts_enabled = 1;
            $usrData->save();
            echo 'success';
        } else {
            echo 'failed';
        }
    }
}