<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Hash;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;


class UserController extends Controller
{
    public function myProfile(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) return $this->sendError('user_not_found', '', 401);

        try {
            $userDataArray = $user->toArray();
            $userDataArray['profile_image'] = $user->profile_image ? asset(Storage::url($user->profile_image)) : null;

            return $this->sendResponse($userDataArray, $this->messageDefault('record_found'));
        } catch (\Throwable $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', '200');
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) return $this->sendError('user_not_found', '', 401);

        // try {
        // Update user attributes
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone_number = $request->phone_number ?? null;
        $user->dob = $request->dob ?? null;
        $user->gender = $request->gender ?? null;

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Define relative path inside storage/app/public
            $relativePath = 'frontend/images/profile';

            // Ensure directory exists
            if (!Storage::exists($relativePath)) {
                Storage::makeDirectory($relativePath, 0755, true); // recursive = true
            }

            // Delete old image if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store new image
            $file = $request->file('profile_image');
            $photoName = time() . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs($relativePath, $photoName, 'public');

            $user->profile_image = $storedPath;
        }

        $user->save();

        return $this->sendResponse($user, 'Profile has been updated');
        // } catch (\Throwable $th) {
        //     return $this->sendError('update_failed', $th->getMessage(), 500);
        // }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) return $this->sendError('user_not_found', '', 401);

        // Check if new password is same as old password
        if (Hash::check($request->password, $user->password)) {
            return $this->sendError($this->messageDefault('The new password you entered is the same as your old password. Enter a different password.'), '', '200');
        }

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        //Logout from All Decices
        // if (auth('sanctum')->check()) {
        //     auth('sanctum')->user()->tokens()->delete();
        // }
        return $this->sendResponse('', $this->messageDefault('change_password_success'));
    }

    public function deleteAccount(Request $request)
    {
        try {
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
                if ($user->fcm_token) {
                    $user->update(['fcm_token' => null]);
                }

                auth('sanctum')->user()->currentAccessToken()->delete();
                $user->delete();

                return $this->sendResponse('', $this->messageDefault('account_deleted'));
            }

            return $this->sendError($this->messageDefault('user_not_found'), '', 401);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
