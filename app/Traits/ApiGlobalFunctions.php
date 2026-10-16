<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;


trait ApiGlobalFunctions
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'status' => true,
            'error' => false,
            'code' => 200,
            'message' => $message,
            'data' => $result,

        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404, $data = [])
    {
        $response = [
            'status' => false,
            'error' => true,
            'code' => $code,
            'message' => !empty($errorMessages) ? $errorMessages : $error,
            'data' => (object) $data,
        ];
        return response()->json($response, $code);
    }

    public function sendErrorForVersion($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => false,
            'error' => true,
            'code' => 402,
            'message' => !empty($errorMessages) ? $errorMessages : $error,
            'data' => (object) [],
        ];
        return response()->json($response, $code);
    }

    public function validateRequest($request, $rules)
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors());
        }
    }

    /**
     * return validation error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendValidationError($error)
    {
        $response = response()->json([
            'error' => true,
            'data' => (object)[],
            'message' => $error,
            'code' => 400,
            'status' => false,
        ]);

        throw new HttpResponseException($response);
    }

    public function messageDefault($label)
    {
        $msgArray = [
            'params_not_available' => 'Request not available',
            'signup_success' => 'Registration Successful!',
            'signup_error' => 'User could not be saved. Please try again',
            'validate_error' => 'error - invalid data',
            'invalid_login' => 'Email or Password is incorrect',
            'invalid_token' => 'Invalid Token',
            'invalid_csrf_token' => 'Invalid CSRF Token',
            'invalid_request' => 'Invalid Request',
            'invalid_account' => 'Invalid Account, you are not authorize to access this',
            'invalid_access' => 'You are not authorize to access this',
            'not_verified' => 'Your account is not verified yet. Please check your email to verify it',
            'not_activated' => 'Your account is not activated, Please contact to admin',
            'verified_success' => 'Your account has been verified successfully',
            'logout_success' => 'You have logged out successfully',
            'login_success' => 'You have logged in successfully',
            'forgot_success' => 'A verification code has been sent to your email. Please check your email for the code',
            'forgot_app_success' => 'New password has been sent to your registered email',
            'resend_otp_success' => 'New verification code generated and has been sent to your email',
            'profile_created'=> 'Your account has been created successfully',
            'profile_update' => 'Your profile has been updated successfully',
            'profile_get' => 'View profile data',
            'profile_delete' => 'Profile deleted',
            'password_update' => 'Your password has been updated successfully',
            'token_not_match' => 'Token not matching',
            'list_found' => 'List found',
            'record_found' => 'Record found',
            'list_not_found' => 'List not found',
            'record_not_found' => 'Record not found',
            'records_delete' => 'Records has been deleted successfully',
            'process_failed' => 'Please try again',
            'record_exists' => 'Request data is already exists',
            'save_records' => 'Record has been saved successfully',
            'update_records' => 'Record has been updated successfully',
            'save_failed' => 'User could not be saved. Please try again',
            'not_register_email' => 'Email address is not registered with us',
            'registered_email' => 'Email address is already registered with us',
            'not_active' => 'Your account has been deactivated. Please contact to admin',
            'change_password_success' => 'Your password changed successfully',
            'oops' => 'Something went wrong',
            'feeback_submitted' => 'Feedback has been submitted successfully',
            'invalid_otp_login' => 'Invalid OTP',
            'valid_otp_login' => 'Your OTP has been successfully verified',
            'otp_sent' => 'OTP sent',
            'otp_resent' => 'OTP sent again',
            'forgot_password_otp_sent' => 'OTP sent successfully',
            'profile_save_success' => 'Your profile is saved successfully',
            'profile_save_error' => 'The profile could not be saved. Please try again',
            'affirmation_delete_error' => 'This affirmation not belongs to you, you can not delete this.',
            'liked_successfully' => 'Affirmation is added to your favourite',
            'disliked_successfully' => 'Affirmation remove from your favourite',
            'prompt_delete_error' => 'You can only delete your own prompts',
            'prompt_edit_error' => 'You can only Edit your own prompts',
            'prompt_show_success' => 'Prompt has been successfully shown',
            'prompt_hide_success' => 'Prompt has been successfully hidden',
            'save_affirmation'=>'Affirmation has been saved successfully',
            'affirmation_records_delete' =>'your affirmation is deleted successfully',
            'account_deleted' => 'Your account has been deleted successfully',
            'user_status_pending' => 'Your account has not been approved by the admin. Please contact the admin',
            'user_status_decline' => 'Your account has been declined by the admin. Please contact the admin',
            'university_list' => 'University list',
            'university_not_found' => 'University not found!',
            'promocode_not_found' => 'Promocode not found!',
            'promocode_applied_success' => 'Promocode has been applied successfully',
            'promocode_appled_only_for_student' => 'You cannot use this promo code. It is only for students',
            'promocode_limit_reached' => 'Sorry, this promo code has reached its usage limit, so you cannot apply it. Please try another one',
            'promocode_already_applied' => 'You have already applied this promo code',
            'promocode_appled_only_for_event' => 'You cannot use this promo code. It is only for event organisers',
            'promocode_cannot_appled' => 'You cannot use this promo code. It is only valid for limited type of users',
            'forum_report_created_success' => 'Report created successfully',
            'predefined_reason_list' => 'Report reasons list',
            'predefined_reason_not_found' => 'Report reasons not found!',
            'university_id_required' => 'University id field is required',
            'subscription_not_found' => 'You do not have an active plan',
            'promocode_applied_only_belongs_to_university' => 'This promocode does not belong to the selected university',
            'acnc_registration_file_success' => 'ACNC registration document uploaded successfully',
            'event_detail' => 'Event detail',
            'event_detail_not_found' => 'Event detail not found!',
            'event_deleted' => 'Event deleted successfully',
            'location_not_found' => 'Location not found!',
            'location_list' => 'Location list',
            'user_not_found' => 'User not found',

        ];
        return isset($msgArray[$label]) ? $msgArray[$label] : $label;
    }

    private function timeAgo($date)
    {
        // Ensure $date is a Carbon instance
        if (!$date instanceof Carbon) {
            try {
                $date = Carbon::parse($date); // Convert to Carbon if not already
            } catch (\Exception $e) {
                return 'invalid date'; // Return an error message for invalid dates
            }
        }

        $now = Carbon::now(); // Get the current time
        $diff = $now->diff($date); // Get the difference as a Carbon Interval
        if (!$diff instanceof \DateInterval) {
            return 'invalid date interval'; // Handle the case where diff is not valid
        }

        // Calculate differences
        $diffInDays = $diff->d;
        $diffInHours = $diff->h;
        $diffInMinutes = $diff->i;

        // Determine the appropriate format to return
        if ($diffInDays === 0) {
            if ($diffInHours === 0) {
                return $diffInMinutes === 0 ? 'just now' : $diffInMinutes . ' minute' . ($diffInMinutes > 1 ? 's' : '') . ' ago';
            }
            return $diffInHours . ' hour' . ($diffInHours > 1 ? 's' : '') . ' ago';
        } elseif ($diffInDays === 1) {
            return '1 day ago';
        } elseif ($diffInDays < 7) {
            return $diffInDays . ' days ago';
        } elseif ($diffInDays < 30) {
            $diffInWeeks = floor($diffInDays / 7);
            return $diffInWeeks . ' week' . ($diffInWeeks > 1 ? 's' : '') . ' ago';
        } else {
            $diffInMonths = floor($diffInDays / 30);
            return $diffInMonths . ' month' . ($diffInMonths > 1 ? 's' : '') . ' ago';
        }
    }

    //    ============================== send notification ==============================
    public function sendNotificationToAllExceptOne($excepted_user_id = null, $msg = "", $include_role_ids = array())
    {
        $query = User::where('status', 1);

        if ($excepted_user_id) {
            $query->where('id', '!=', $excepted_user_id);
        }
        if ($include_role_ids) {
            $query->whereHas('roles', function ($q) use ($include_role_ids) {
                $q->whereIn('id', $include_role_ids);
            });
        }

        $user_idsArray = $query->pluck('id')->toArray();

        if (!empty($user_idsArray)) {
            return $this->sendNotification($user_idsArray, $msg, 'info');
        } else {
            Log::warning('No active users found to send notifications.', [
                'excepted_user_id' => $excepted_user_id,
                'expected_role_id' => $include_role_ids
            ]);
            return response()->json(['message' => 'No active users found to send notifications.'], 404);
        }
    }

    public function sendNotification($userIds, $body, $type, $item = [])
    {
        // Disable auto rendering of responses
        $this->autoRender = false;

        // Set default notification body if not provided
        $body = $body ?: 'This is a test notification.';
        $title = config('GET.system_application_name');

        // Generate OAuth 2.0 access token using the service account
        $accessToken = $this->generateAccessToken();

        // Check if access token is generated
        if (!$accessToken) {
            Log::error('Failed to generate access token');
            return response()->json(['error' => 'Failed to generate access token'], 500);
        }

        // Initialize responses array to collect results
        $responses = [];

        foreach ($userIds as $userid) {
            // Retrieve the user from the database
            $user = User::find($userid);
            $deviceID = $user ? $user->fcm_token : '';

            if ($deviceID) {
                // Prepare the notification payload
                $notificationPayload = [
                    'message' => [
                        'token' => $deviceID,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => [
                            'title' => $title,
                            'body' => $body,
                            'sound' => 'default',
                            'notification_type' => $type,
                            // Include additional data if provided
                            ...$item,
                        ],
                        'android' => [
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                                'icon' => 'ic_notification',
                                'sound' => 'default',
                                'channel_id' => config('GET.system_application_name'),
                            ],
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'alert' => [
                                        'title' => $title,
                                        'body' => $body,
                                    ],
                                    'sound' => 'default',
                                ],
                            ],
                            'headers' => [
                                'apns-priority' => '10',
                            ],
                        ],
                    ],
                ];

                // Send push notification using FCM
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post('https://fcm.googleapis.com/v1/projects/tribal-f215a/messages:send', $notificationPayload);

                // Handle response from FCM
                if ($response->successful()) {
                    $responses[$userid] = ['message' => 'Notification sent successfully'];
                } else {
                    Log::error('FCM Notification Error for user ID ' . $userid . ': ' . $response->body(), ['status' => $response->status()]);
                    $responses[$userid] = ['error' => 'Failed to send notification', 'details' => $response->body()];
                }
            } else {
                Log::error('No device ID found for user ID: ' . $userid);
                $responses[$userid] = ['error' => 'No device ID found'];
            }
        }

        return response()->json($responses, 200);
    }

    private function generateAccessToken()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/tribal-f215a-firebase-adminsdk-gf7ga-cada2395c7.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Fetch the access token
        $accessToken = $client->fetchAccessTokenWithAssertion();
        return $accessToken['access_token'] ?? null; // Return the access token or null
    }

    public function sendNotification_single($userid, $body, $type, $item = [])
    {
        // Retrieve the user from the database
        $user = User::find($userid);
        $this->autoRender = false; // Disable auto rendering of responses

        // Set default notification body if not provided
        $body = $body ?: 'This is a test notification.';
        $title = config('GET.system_application_name');

        // Generate OAuth 2.0 access token using the service account
        $accessToken = $this->generateAccessToken();
        $deviceID = $user ? $user->fcm_token : '';

        if ($accessToken && $deviceID) {
            // Prepare the notification payload
            $notificationPayload = [
                'message' => [
                    'token' => $deviceID,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => [
                        'title' => $title,
                        'body' => $body,
                        'sound' => 'default',
                        'notification_type' => $type,
                        // Include additional data if provided
                        ...$item,
                    ],
                    'android' => [
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                            'icon' => 'ic_notification',
                            'sound' => 'default',
                            'channel_id' => config('GET.system_application_name'),
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'alert' => [
                                    'title' => $title,
                                    'body' => $body,
                                ],
                                'sound' => 'default',
                            ],
                        ],
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                    ],
                ],
            ];

            // Send push notification using FCM
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/v1/projects/tribal-f215a/messages:send', $notificationPayload);

            // Handle response from FCM
            if ($response->successful()) {
                return response()->json(['message' => 'Notification sent successfully'], 200);
            } else {
                Log::error('FCM Notification Error: ' . $response->body(), ['status' => $response->status()]);
                return response()->json(['error' => 'Failed to send notification', 'details' => $response->body()], $response->status());
            }
        } else {
            Log::error('Failed to generate access token or no device ID found');
            return response()->json(['error' => 'Failed to generate access token or no device ID'], 500);
        }
    } 
}
