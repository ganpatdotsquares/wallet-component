<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiGlobalFunctions;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\Customer;
use Illuminate\Support\Facades\Log;


class StripeController extends Controller
{
    use ApiGlobalFunctions;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secretKey'));
    }

    public function connectStripe(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user) {
                if ($user->stripe_payouts_enabled == 0) {
                    $this->createConnectedAccount($user);
                    $user = User::find($user->id);
                    if (is_null($user)) {
                        return $this->sendError('Unauthorised.', ['error' => 'Please login again.']);
                    }
                    $stripe = new \Stripe\StripeClient(config('services.stripe.secretKey'));

                    $acctlink = $stripe->accountLinks->create([
                        'account' => $user->stripe_account_id,
                        'refresh_url' => 'https://connect.stripe.com/reauth',
                        'return_url' => route('stripe.redirect', $user->stripe_token),
                        'type' => 'account_onboarding',
                    ]);

                    $success['url'] = $acctlink->url;
                    $success['expires_at'] = date('d M, Y H:i:s', $acctlink->expires_at);
                    return $this->sendResponse($success, 'stripe connect url.');
                } else {
                    return $this->sendError('Stripe already connected.', '', 200);
                }
            } else {
                return $this->sendError('Please login again.', '', 200);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), '', 200);
        }
    }

    public function createConnectedAccount($checkuser)
    {
        if ($checkuser->stripe_account_id) {
            return $checkuser->stripe_account_id;
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secretKey'));

        // Ensure Stripe customer exists
        if (!$checkuser->stripe_customer_id) {
            $customer = $stripe->customers->create([
                'email' => $checkuser->email,
                'name'  => $checkuser->name,
            ]);

            $checkuser->stripe_customer_id = $customer->id;
            $checkuser->save();
        }
        

        $account = $stripe->accounts->create([
            'type' => 'custom',
            'email' => $checkuser->email,
            'business_type' => 'individual',
            'capabilities' => [
                'transfers' => ['requested' => true],
            ],
            'business_profile' => [
                'product_description' => 'Digital wallet system for sending and receiving money.',
                'url' => 'https://wallet-system.co.org',
            ],
        ]);

        $stripe_account_id = $account->id;
        $checkuser->update([
            'stripe_account_id'  => $account->id,
            'stripe_customer_id' => $checkuser->stripe_customer_id,
            'profile_step' => 3,
        ]);

        return $stripe_account_id;
    }
}