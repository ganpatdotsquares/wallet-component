<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiGlobalFunctions;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Stripe\Stripe;
use Illuminate\Support\Facades\DB;
use App\Models\WithdrawRequest;
use App\Mail\AppMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Api\WalletWithdrawRequest;


class WalletController extends Controller
{
    use ApiGlobalFunctions;

    public function depositInitiate(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) return $this->sendError('User not found', '', 401);

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = $request->amount;
        $currency = config('services.stripe.stripeCurrency', 'usd');
        Stripe::setApiKey(config('services.stripe.secretKey'));

        try {
            // Create Stripe customer if needed
            if (!$user->stripe_customer_id) {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secretKey'));
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            // Create payment intent
            $stripe = new \Stripe\StripeClient(config('services.stripe.secretKey'));
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => intval($amount * 100),
                'currency' => $currency,
                'customer' => $user->stripe_customer_id,
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'user_id' => $user->id,
                    'type' => 'wallet_deposit',
                ],
            ]);

            return $this->sendResponse([
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
            ], 'Payment intent created.');
        } catch (\Exception $e) {
            return $this->sendError('Deposit initiation failed: ' . $e->getMessage());
        }
    }

    public function depositConfirm(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) return $this->sendError('User not found', '', 401);

        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secretKey'));

        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secretKey'));
            $paymentIntent = $stripe->paymentIntents->retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return $this->sendError('Payment not completed.');
            }

            $amount = $paymentIntent->amount / 100;

            DB::beginTransaction();

            // Update wallet
            $wallet = $user->wallet ?? Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00,
            ]);

            $wallet->balance += $amount;
            $wallet->save();

            // Log transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'status' => 'completed',
                'transaction_id' => $paymentIntent->id,
                'description' => 'Wallet deposit via Stripe',
            ]);

            DB::commit();

            return $this->sendResponse(['balance' => $wallet->balance], 'Wallet updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Deposit confirmation failed: ' . $e->getMessage());
        }
    }

    public function getBalance(Request $request)
    {
        try{
            $user = auth('sanctum')->user();
            if (!$user) return $this->sendError('User not found', '', 401);

            $wallet = $user->wallet;
            $balance = $wallet ? $wallet->balance : 0.00;
            $currency = config('services.stripe.stripeCurrency', 'usd');

            return $this->sendResponse(['balance' => $balance, 'currency' => $currency], 'Wallet balance fetched successfully.');

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), '', 500);
        }
    }

    public function withdrawRequest(WalletWithdrawRequest $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) return $this->sendError('User not found', '', 401);

        $amount = $request->amount;
        $currency = config('services.stripe.currency_sign', '$');

        Stripe::setApiKey(config('services.stripe.secretKey'));

        $wallet = $user->wallet;
        $balance = (float) $wallet->balance;
        $amount = (float) $amount;
        
        if ($balance < $amount) {
            return $this->sendError('Insufficient wallet balance.');
        }

        if (!$user->stripe_account_id) {
            return $this->sendError('Stripe account not connected.');
        }

        $alreadySubmit = WithdrawRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first(); // Cancel any previous pending requests

        if ($alreadySubmit) {
            return $this->sendError('You already have a pending withdrawal request.');
        }

        DB::beginTransaction();
        try {
            
            WithdrawRequest::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'method' => $request->method,
                'method_details' => json_encode($request->method_details),
            ]);

            // $replacement['USERNAME'] = $user->name;
            // $replacement['USER_EMAIL'] =  $user->email;
            // $replacement['AMOUNT'] = $amount;
            // $replacement['CURRENCY'] = $currency;
            // $replacement['REQUEST_METHOD'] = ucfirst($request->method);
            // $replacement['REQUESTED_AT'] = now()->format(config('GET.admin_date_format'));

            // $maildata = ['to' => $user->email, 'template' => 'user-withdraw-request', 'hooksVars' => $replacement];
            // $adminEmail = config('GET.admin_email');
            // if ($adminEmail) {
            //     Mail::to($adminEmail)->send(new AppMail($maildata));
            // }

            DB::commit();

            return $this->sendResponse(['balance' => $wallet->balance], 'Withdrawal request submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Withdrawal failed: ' . $e->getMessage());
        }
    }

    public function withdrawRequestCancel(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) return $this->sendError('User not found', '', 401);

        $withdraw = WithdrawRequest::where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->latest()
                        ->first(); // Cancel any previous pending requests

        if (!$withdraw) {
            return $this->sendError('No pending withdrawal request found.');
        }

        if ($withdraw->user_id !== $user->id) {
            return $this->sendError('Unauthorized action.');
        }

        if ($withdraw->status !== 'pending') {
            return $this->sendError('Only pending requests can be cancelled.');
        }

        DB::beginTransaction();
        try {
            $withdraw->status = 'cancelled';
            $withdraw->save();

            $amount = $withdraw->amount;
            $currency = config('services.stripe.currency_sign', '$');

            // $replacement['USERNAME'] = $user->name;
            // $replacement['USER_EMAIL'] =  $user->email;
            // $replacement['AMOUNT'] = $amount;
            // $replacement['CURRENCY'] = $currency;
            // $replacement['REQUEST_METHOD'] = ucfirst($request->method);
            // $replacement['REQUESTED_AT'] = now()->format(config('GET.admin_date_format'));

            // $maildata = ['to' => $user->email, 'template' => 'user-withdraw-request-cancel', 'hooksVars' => $replacement];
            // $adminEmail = config('GET.admin_email');
            // if ($adminEmail) {
            //     Mail::to($adminEmail)->send(new AppMail($maildata));
            // }

            DB::commit();

            return $this->sendResponse([], 'Withdrawal request cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Cancellation failed: ' . $e->getMessage());
        }
    }

    public function transactionHistory(Request $request)
    {
        try{
            $user = auth('sanctum')->user();
            if (!$user) return $this->sendError('User not found', '', 401);

            $transactions = $user->transactions()->orderBy('created_at', 'desc')->get();
            
            return $this->sendResponse(['transactions' => $transactions], 'Transaction history fetched successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), '', 500);
        }
    }
} 