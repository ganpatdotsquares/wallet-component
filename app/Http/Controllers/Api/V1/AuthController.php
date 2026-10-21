<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\AppMail;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Api\UserRegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\OtpVerificationRequest;
use App\Http\Requests\Api\ResentOtpVerificationRequest;
use Illuminate\Support\Str;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Models\WalletTransaction;


class AuthController extends Controller
{
    use ApiGlobalFunctions;

    public function register(UserRegisterRequest $request)
    {
        try {

            $referrer = null;
            // Step 1: Check if referral code was entered and is valid
            if (!empty($request->wallet_referred_by)) {
                $referrer = User::where('status', 1)
                    ->whereNotNull('email_verified_at') // Corrected
                    ->where('wallet_referral_code', $request->wallet_referred_by)
                    ->first();

                if (!$referrer) {
                    return $this->sendError('Your applied referral code does not exist. Try another referral code.');
                }
            }

            DB::beginTransaction();

            $otp = (config('app.env') === 'production') ? rand(1000, 9999) : 1234;

            $referralCode = strtoupper(Str::random(6));

            $user = new User([
                'first_name'            => $request->first_name,
                'last_name'             => $request->last_name,
                'email'                 => $request->email,
                'phone_number'          => $request->phone_number,
                'dob'                   => $request->dob ?? null,
                'gender'                => $request->gender ?? null,
                'password'              => bcrypt($request->password),
                'verification_otp'      => $otp,
                'fcm_token'             => $request->fcm_token ?? null,
                'stripe_token'          => Str::random(10) . time(),
                'profile_step'          => 1,
                'wallet_referral_code'  => $referralCode,
                'wallet_referred_by'    => $request->wallet_referred_by ?? null,
            ]);

            if (!$user->save()) {
                DB::rollBack();
                return $this->sendError($this->messageDefault('profile_not_created'));
            }

            // Step 2: Create Wallet
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0, // Start from zero
            ]);

            if ($referrer) {
                $referralBonusAmount = config('GET.user_referral_bonus', 0); // fallback to 0 if not set

                // Get or create the referrer's wallet
                $referrerWallet = Wallet::firstOrCreate(['user_id' => $referrer->id]);

                // Add bonus to existing balance
                $referrerWallet->balance += $referralBonusAmount;
                $referrerWallet->save();
            }

            // $replacement['USERNAME'] = $user->name;
            // $replacement['OTP_CODE'] =  $otp;
            // $maildata = ['to' => $user->email, 'template' => 'otp-mail-to-user', 'hooksVars' => $replacement];
            // Mail::to($user->email)->send(new AppMail($maildata));
            // $data = ['user_id' => $user->id];

            DB::commit();

            return $this->sendResponse($user, $this->messageDefault('profile_created'), 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($this->messageDefault('invalid_request'), '', 200);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            // Check if the user with the given email exists
            if (User::where('email', $request->email)->doesntExist()) {
                Auth::guard('api')->logout();
                return $this->sendError($this->messageDefault('not_register_email'), '', 200);
            }

            // Attempt to authenticate the user
            $credentials = $request->only('email', 'password');
            if (!Auth::guard('api')->attempt($credentials)) {
                Auth::guard('api')->logout();
                return $this->sendError($this->messageDefault('invalid_login'), '', 200);
            }

            // Retrieve the authenticated user
            $user = Auth::guard('api')->user();


            // Check the user's active status
            if ($user->status == 0) {
                Auth::guard('api')->logout(); // Ensure user is logged out
                return $this->sendError($this->messageDefault('not_activated'), '', 200);
            }

            if ($user->email_verified_at == null) {
                $otp = rand(1000, 9999);
                User::where('id', $user->id)->update(['verification_otp' => $otp]);
                $data = ['user_id' => $user->id, 'email_verified_at' => $user->email_verified_at];
                $replacement['USERNAME'] = $user->name;
                $replacement['OTP_CODE'] =  $otp;
                $maildata = ['to' => $user->email, 'template' => 'otp-mail-to-user', 'hooksVars' => $replacement];

                try {
                    Mail::to($user->email)->send(new AppMail($maildata));
                } catch (\Throwable $mailException) {
                    Log::error('Failed to send login OTP email to ' . $user->email . ': ' . $mailException->getMessage());
                }
                Auth::guard('api')->logout();
                return $this->sendError('Your email is not verified', '', 200, $data);
            }

            // Update FCM ID if provided
            if ($request->fcm_token) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }

            // Logout from all devices (revoke all tokens)
            $user->tokens()->delete();

            // Create a new token for the user
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            $user['profile_image'] = $user->profile_image ? asset(storage_path($user->profile_image)) : null;
            $user['access_token'] = $token;
            $user['token_type'] = 'Bearer';
            $user['name'] = $user->name ?? $user->first_name . ' ' . $user->last_name;


            return $this->sendResponse($user, $this->messageDefault('login_success'));
        } catch (\Throwable $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', 200);
        }
    }

    public function otpVerification(OtpVerificationRequest $request)
    {
        try {
            $user = User::where(['email' => $request->email, 'verification_otp' => $request->otp])->first();
            if ($user) {
                $user->update(['email_verified_at' => \Carbon\Carbon::now(), 'verification_otp' => null, 'profile_step'    => 2]);

                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->plainTextToken;
                $userData = $user;
                $userData['accessToken'] = $token;
                $userData['token_type'] = 'Bearer';
                return $this->sendResponse($userData, $this->messageDefault('valid_otp_login'));
            } else {
                return $this->sendError($this->messageDefault('invalid_otp_login'), '', 200);
            }
        } catch (\Throwable $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', 200);
        }
    }

    public function otpreSendVerification(ResentOtpVerificationRequest $request)
    {
        try {
            $user = User::find($request->user_id);
            $otp = rand(1000, 9999);

            if ($user) {
                $user->update(['email_verified_at' => null, 'verification_otp' => $otp]);

                $replacement['USERNAME'] = $user->name;
                $replacement['OTP_CODE'] =  $otp;
                $maildata = ['to' => $user->email, 'template' => 'resend-otp-to-user', 'hooksVars' => $replacement];
                try {
                    Mail::to($user->email)->send(new AppMail($maildata));
                } catch (\Throwable $mailException) {
                    Log::error('Failed to send resend-OTP email to ' . $user->email . ': ' . $mailException->getMessage());
                }

                $data = ['user_id' => $user->id];
                return $this->sendResponse($data, $this->messageDefault('otp_resent'));
            } else {
                return $this->sendError($this->messageDefault('invalid_request'), '', '200');
            }
        } catch (\Throwable $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', 200);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                //check USER active status
                if ($user->status == 0) {
                    return $this->sendError($this->messageDefault('not_activated'), '', '200');
                }
                $otp = rand(1000, 9999);
                $user->update(['forgot_password_otp' => $otp]);
                $replacement['USERNAME'] = $user->name;
                $replacement['OTP_CODE'] =  $otp;
                $maildata = ['to' => $user->email, 'template' => 'forgot-password-email-to-user', 'hooksVars' => $replacement];
                try {
                    Mail::to($user->email)->send(new AppMail($maildata));
                } catch (\Throwable $mailException) {
                    Log::error('Failed to send forgot-password OTP email to ' . $user->email . ': ' . $mailException->getMessage());
                }
                return $this->sendResponse(['user_id' => $user->id], $this->messageDefault('forgot_password_otp_sent'));
            }

            return $this->sendError($this->messageDefault('user_not_found'), '', 404);
        } catch (\Throwable $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', 200);
        }
    }

    public function forgotPasswordOtp(OtpVerificationRequest $request)
    {
        try {
            $user = User::where(['id' => $request->user_id, 'forgot_password_otp' => $request->otp])->first();
            if ($user) {
                $user->update(['forgot_password_otp' => null]);

                // $user = $request->user();
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->plainTextToken;
                $userData = $user;
                $userData['accessToken'] = $token;
                $userData['token_type'] = 'Bearer';

                return $this->sendResponse($userData, $this->messageDefault('valid_otp_login'));
            }

            return $this->sendError($this->messageDefault('user_not_found'), '', 404);
        } catch (\Throwable $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', 200);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = User::where('id',  $request->user_id)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    return $this->sendError($this->messageDefault('The new password you entered is the same as your old password. Enter a different password.'), '', '200');
                }

                $user->update([
                    'password' => bcrypt($request->password)
                ]);
                //Logout from All Devices
                if (auth('sanctum')->check()) {
                    auth('sanctum')->user()->tokens()->delete();
                }
                return $this->sendResponse('', $this->messageDefault('change_password_success'));
            } else {
                return $this->sendError($this->messageDefault('invalid_request'), '', '200');
            }
        } catch (\Throwable $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', 200);
        }
    }

    public function logout(Request $request)
    {
        if (auth('sanctum')->check()) {
            //Tag expiry time code
            $user = auth('sanctum')->user();
            //logout from all device
            auth('sanctum')->user()->tokens()->delete();
            if (auth('sanctum')->user()->fcm_token) {
                auth('sanctum')->user()->update(['fcm_token' => null]);
            }
            auth('sanctum')->user()->currentAccessToken()->delete();

            return $this->sendResponse('', $this->messageDefault('logout_success'));
        } else {
            return $this->sendError($this->messageDefault('invalid_request'), '', '200');
        }
    }
}
